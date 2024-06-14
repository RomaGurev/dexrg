<?
/*
Файл, подключаемый index.php
> содержит класс Helper и методы упрощающие работу
*/

class Helper
{
    public static function convertAdventPeriodToString($adventPeriod)
    {
        $advent = explode("-", $adventPeriod);
        return($advent[1] == "1" ? "Весна" : "Осень") . " " . $advent[0];
    }

    public static function getPreviousAdventPeriod()
    {
        $advent = explode("-", Database::getCurrentBase());
        return $advent[1] == "1" ? $advent[0] - 1 . "-2" : $advent[0] . "-1";
    }

    public static function getHealthCategories($exept = null)
    {
        $healthCategories = Config::getValue("healthCategory");

        if ($exept == null)
            return $healthCategories;
        else {
            foreach ($healthCategories as $key => $value) {
                if (isset($value[1])) {
                    if (in_array($exept, $value[1]))
                        continue;
                }
                $result[$key] = $value[0];
            }
            return $result;
        }
    }

    public static function getHealthCategoryNameByID($id) {
        $healthCategories = Config::getValue("healthCategory");

        if(isset($healthCategories[$id])) {
            return $healthCategories[$id][0];
        } else {
            return "Неизвестная категория годности";
        }
    }

    public static function formatDateToView($date)
    {
        return date('d.m.Y', strtotime($date));
    }

    public static function formatDateToBase($date)
    {
        return date('Y.m.d', strtotime($date));
    }

    public static function getVKNameById($vkId)
    {
        return Database::execute("SELECT * FROM vkList WHERE id=:id", ["id" => $vkId])[0];
    }

    public static function getProfileByUserID($userID)
    {
        return Database::execute("SELECT * from staff WHERE id = :id", ["id" => $userID])[0];
    }

    public static function getFinalHealthResult($userID)
    {
        $documents = Helper::getResultDocuments($userID);

        if (count($documents) > 0) {
            $healthResult = ["healthCategory" => $documents[0]["healthCategory"], "article" => $documents[0]["article"], "documentType" => $documents[0]["documentType"], "postPeriod" => $documents[0]["postPeriod"]];
            foreach ($documents as $value) {
                if($healthResult["healthCategory"] < $value["healthCategory"]) 
                {
                    $healthResult["healthCategory"] = $value["healthCategory"];
                    $healthResult["article"] = $value["article"];
                    $healthResult["documentType"] = $value["documentType"];
                }

                if($healthResult["healthCategory"] == $value["healthCategory"] && $value["healthCategory"] == "Г") 
                {
                    if($healthResult["postPeriod"] < $value["postPeriod"]) 
                    {
                        $healthResult["postPeriod"] = $value["postPeriod"];
                        $healthResult["article"] = $value["article"];
                    }
                }

            }
        } else
            $healthResult = null;

        return $healthResult;
    }

    public static function getUserPatternList($additionQuery = null)
	{
		$quary = "SELECT * FROM `patternList`";

        if(!Profile::isHavePermission("viewForAll") || $additionQuery != null)
            $quary .= " WHERE";

		if(!Profile::isHavePermission("viewForAll")) {
			$quary .= " ownerID=:ownerID";
            if($additionQuery != null)
                $quary .= " AND";
			$dataArr = [
				"ownerID" => Profile::$user['id']
			];
		}

        if($additionQuery != null) {
            $quary .= " name LIKE '%" . $additionQuery . "%'";
        }

		$quary .= " ORDER BY ID desc;";

		return Database::execute($quary, $dataArr);
	}

    public static function getShortenString($str, $limit = 300)
    {
        return mb_strimwidth($str, 0, $limit, "...");
    }

    public static function getConscriptsWithDocuments($documentType = null, $inProcess = null, $creatorID = null, $additionQueryToConscript = null, $additionQueryToDocument = null)
    {
        $result = array();
        $conscriptQuery = "SELECT * FROM `conscript`";

        if($inProcess != null) {
            $conscriptQuery .= " WHERE `inProcess` = :inProcess";
        }

        if ($additionQueryToConscript != null)
            $conscriptQuery .= " " . $additionQueryToConscript;

        $conscriptQuery .= " ORDER BY id DESC";

        $conscripts = Database::execute($conscriptQuery, ["inProcess" => $inProcess], "current");

        foreach ($conscripts as $conscript) {
            $documentQuery = "SELECT * FROM `documents` WHERE conscriptID=:id";

            $data["id"] = $conscript["id"];

            if ($documentType != null) {
                $documentQuery .= " AND documentType=:documentType";
                $data["documentType"] = $documentType;
            }

            if ($creatorID != null) {
                $documentQuery .= " AND creatorID=:creatorID";
                $data["creatorID"] = $creatorID;
            }

            if ($additionQueryToDocument != null) {
                $documentQuery .= " " . $additionQueryToDocument;
            }

            $documentQuery .= " ORDER BY id DESC";

            $conscript["documents"] = Database::execute($documentQuery, $data, "current");
            $conscript["showCreator"] = $creatorID == null ? true : false;

            if ($conscript["documents"] != null)
                array_push($result, $conscript);
        }

        return $result;

    }

    public static function getResultDocuments($userID) 
    {
        $documentPriority = ["complaint" => 0, "return" => 1, "control" => 2, "changeCategory" => 3, "confirmation" => 4];
        $documents = Database::execute("SELECT * FROM `documents` WHERE conscriptID=:conscriptID AND countable=1", ["conscriptID" => $userID], "current");
        $result = array();

        foreach ($documents as $value) {
            if($documentPriority[$value["documentType"]] <= $documentPriority[$result[0]["documentType"]] || count($result) == 0) {
                if($documentPriority[$value["documentType"]] < $documentPriority[$result[0]["documentType"]])
                    $result = array();

                array_push($result, $value);
            }
        }
        return $result;
    }

    public static function getInProccesStatus($userID) 
    {
        return Database::execute("SELECT inProcess FROM `conscript` WHERE id=:id", ["id" => $userID], "current")[0]["inProcess"];
    } 

    public static function convertDateToPrintFormat($date) 
    {
        if(!empty($date)) {
            $date = explode(".", $date);
            
	        switch ($date[1])
	        {
	        		case "01": { $mm = "января"; break;	}
	        		case "02": { $mm = "февраля"; break;	}
	        		case "03": { $mm = "марта"; break;	}
	        		case "04": { $mm = "апреля"; break;	}
	        		case "05": { $mm = "мая"; break;	}
	        		case "06": { $mm = "июня"; break;	}
	        		case "07": { $mm = "июля"; break;	}
	        		case "08": { $mm = "августа"; break;	}
	        		case "09": { $mm = "сентября"; break;	}
	        		case "10": { $mm = "октября"; break;	}
	        		case "11": { $mm = "ноября"; break;	}
	        		case "12": { $mm = "декабря"; break;	}
	        }
	        return "«".$date[0]."» ".$mm." ".$date[2]."г.";
        } else
            return "";
    }

    public static function getProtocolChanges($userID) 
    {
        return Database::execute("SELECT * FROM `protocolChanges` WHERE conscriptID=:conscriptID", ["conscriptID" => $userID], "current");
    }
}