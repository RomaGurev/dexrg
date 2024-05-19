<?
/*
Класс статистики
> содержит методы для обработки данных POST запроса статистики и формирования ответа на них.
> конвертирует ответ в формат JSON
*/
require_once '../database/database.php';
require_once '../core/config.php';
require_once '../core/profile.php';
require_once '../core/route.php';
require_once '../additions/helper.php';

//Обработка запроса статистики и отправка в формате JSON
Profile::authInit();

$statistics = new Statistic();
$statisticString = $statistics->getStatistic(Profile::$user["id"]);

echo $statisticString;

class Statistic
{

    function getStatisticData($userID) 
    {
        $query = "SELECT `documents`.creatorID, conscriptID, SUBSTRING(`conscript`.healthCategory, 1, 1) AS conCat, SUBSTRING(`documents`.healthCategory, 1, 1) AS docCat, documentType, CASE WHEN documentType = 'complaint' THEN 0 WHEN documentType = 'return' THEN 1 WHEN documentType = 'control' THEN 2 ELSE 3 END AS docPriority FROM documents INNER JOIN `conscript` ON `documents`.`conscriptID`=`conscript`.`id`";
        if(!Profile::isHavePermission("viewForAll")) 
        {
            $query .= " WHERE `documents`.creatorID=:userID AND inProcess=0";
            $data = ["userID" => $userID];
        } else {
            $query .= " WHERE inProcess=0";
        }
        $query .= " ORDER BY docPriority";
        $queryResult = Database::execute($query, $data, "current");
        $resultDocArr = array();

        foreach ($queryResult as $value) 
        {
            ${$value["documentType"] . "Count"}++;

            if(isset($resultDocArr[$value["conscriptID"]])) {
                if($value["docPriority"] >= $resultDocArr[$value["conscriptID"]]["docPriority"]) {
                    if($value["docPriority"] > $resultDocArr[$value["conscriptID"]]["docPriority"])
                        continue;
                    if($value["docCat"] < $resultDocArr[$value["conscriptID"]]["docCat"])
                        continue;
                }
            }
            $resultDocArr[$value["conscriptID"]] = ["conCat" => $value["conCat"], "docCat" => $value["docCat"], "documentType" => $value["documentType"]];
        }

        foreach ($resultDocArr as $key => $value) {
            if ($value["docCat"] == mb_substr(Helper::getFinalHealthResult($key)["healthCategory"], 0, 1))
                $statistic[$value["documentType"] . Config::getValue("categoryConverter")[$value["conCat"]] . "To" . Config::getValue("categoryConverter")[$value["docCat"]]]++;
        }

        //chartAdjustment
        $chartAdjustment["controlCount"] = $this->nullCheck($controlCount);
        $chartAdjustment["complaintCount"] = $this->nullCheck($complaintCount);
        $chartAdjustment["returnCount"] = $this->nullCheck($returnCount);
        $chartAdjustment["changeCategoryCount"] = $this->nullCheck($changeCategoryCount);
        $chartAdjustment["confirmationCount"] = $this->nullCheck($confirmationCount);
        $chartAdjustment["allCount"] = $this->nullCheck(count($queryResult));
        //chartAdjustment

        //chartConscripts
        $chartConscripts["conscriptCount"] = Database::execute("SELECT count(id) AS count FROM conscript", null, "current")[0]["count"]; //Общее количество зарегистрированных призывников

        $conscriptsForDate = Database::execute("SELECT * FROM (SELECT count(id) AS count, creationDate AS date FROM conscript GROUP BY creationDate ORDER BY STR_TO_DATE(creationDate, '%d.%m.%Y') DESC LIMIT 5) AS XYITA ORDER BY STR_TO_DATE(XYITA.date, '%d.%m.%Y') ASC", null, "current");
        foreach ($conscriptsForDate as $value)
            $resArr[$value["date"]] = $value["count"];
        $chartConscripts["dates"] = $resArr;
        //chartConscripts

        $statistic["chartChangeCategory"] = $chartChangeCategory;
        $statistic["chartAdjustment"] = $chartAdjustment;
        $statistic["chartConscripts"] = $chartConscripts;
        return $statistic;
    }

    function nullCheck($data) 
    {
        return $data ?? 0;
    }

    function prepareChartAdjustment($statistic)
    {
        $obj = [
            "labels" => ['Контроль', 'Жалобы', 'Возвраты', 'Изменение категории', 'Утверждение'],
            "data" => [$statistic["controlCount"], $statistic["complaintCount"], $statistic["returnCount"], $statistic["changeCategoryCount"], $statistic["confirmationCount"]],
            "titleText" => "Общее количество документов: " . $statistic["allCount"]
        ];

        return $obj;
    }

    function prepareChartConscripts($statistic)
    {
        $obj = [
            "labels" => array_keys($statistic["dates"]),
            "data" => array_values($statistic["dates"]),
            "titleText" => "Зарегистрировано призывников: " . $statistic["conscriptCount"]
        ];

        return $obj;
    }

    function prepareChartChangeCategory($statistic)
    {
        $changeCategoryA = $this->nullCheck($statistic["changeCategoryToA"]+$statistic["changeCategoryAToA"]+$statistic["changeCategoryBToA"]);
        $changeCategoryB = $this->nullCheck($statistic["changeCategoryToB"]+$statistic["changeCategoryAToB"]+$statistic["changeCategoryBToB"]);

        $obj = [
            "labels" => ['А', 'Б'],
            "data" => [$changeCategoryA, $changeCategoryB],
            "titleText" => "Изменение категории"
        ];
        return $obj;
    }

    function prepareChartControl($statistic)
    {
        $controlA = $this->nullCheck($statistic["controlToA"]+$statistic["controlAToA"]+$statistic["controlBToA"]+$statistic["controlVToA"]+$statistic["controlGToA"]+$statistic["controlDToA"]+$statistic["controlOToA"]);
        $controlB = $this->nullCheck($statistic["controlToB"]+$statistic["controlAToB"]+$statistic["controlBToB"]+$statistic["controlVToB"]+$statistic["controlGToB"]+$statistic["controlDToB"]+$statistic["controlOToB"]);
        $controlV = $this->nullCheck($statistic["controlToV"]+$statistic["controlAToV"]+$statistic["controlBToV"]+$statistic["controlVToV"]+$statistic["controlGToV"]+$statistic["controlDToV"]+$statistic["controlOToV"]);
        $controlG = $this->nullCheck($statistic["controlToG"]+$statistic["controlAToG"]+$statistic["controlBToG"]+$statistic["controlVToG"]+$statistic["controlGToG"]+$statistic["controlDToG"]+$statistic["controlOToG"]);
        $controlD = $this->nullCheck($statistic["controlToD"]+$statistic["controlAToD"]+$statistic["controlBToD"]+$statistic["controlVToD"]+$statistic["controlGToD"]+$statistic["controlDToD"]+$statistic["controlOToD"]);
        $controlO = $this->nullCheck($statistic["controlToO"]+$statistic["controlAToO"]+$statistic["controlBToO"]+$statistic["controlVToO"]+$statistic["controlGToO"]+$statistic["controlDToO"]+$statistic["controlOToO"]);

        $obj = [
            "labels" => ['А', 'Б', 'В', 'Г', 'Д', 'О'],
            "data" => [$controlA, $controlB, $controlV, $controlG, $controlD, $controlO],
            "titleText" => "Контроль"
        ];
        return $obj;
    }

    function prepareChartComplaint($statistic)
    {
        $complaintA = $this->nullCheck($statistic["complaintToA"]+$statistic["complaintAToA"]+$statistic["complaintBToA"]+$statistic["complaintVToA"]+$statistic["complaintGToA"]+$statistic["complaintDToA"]+$statistic["complaintOToA"]);
        $complaintB = $this->nullCheck($statistic["complaintToB"]+$statistic["complaintAToB"]+$statistic["complaintBToB"]+$statistic["complaintVToB"]+$statistic["complaintGToB"]+$statistic["complaintDToB"]+$statistic["complaintOToB"]);
        $complaintV = $this->nullCheck($statistic["complaintToV"]+$statistic["complaintAToV"]+$statistic["complaintBToV"]+$statistic["complaintVToV"]+$statistic["complaintGToV"]+$statistic["complaintDToV"]+$statistic["complaintOToV"]);
        $complaintG = $this->nullCheck($statistic["complaintToG"]+$statistic["complaintAToG"]+$statistic["complaintBToG"]+$statistic["complaintVToG"]+$statistic["complaintGToG"]+$statistic["complaintDToG"]+$statistic["complaintOToG"]);
        $complaintD = $this->nullCheck($statistic["complaintToD"]+$statistic["complaintAToD"]+$statistic["complaintBToD"]+$statistic["complaintVToD"]+$statistic["complaintGToD"]+$statistic["complaintDToD"]+$statistic["complaintOToD"]);
        $complaintO = $this->nullCheck($statistic["complaintToO"]+$statistic["complaintAToO"]+$statistic["complaintBToO"]+$statistic["complaintVToO"]+$statistic["complaintGToO"]+$statistic["complaintDToO"]+$statistic["complaintOToO"]);
        
        $obj = [
            "labels" => ['А', 'Б', 'В', 'Г', 'Д', 'О'],
            "data" => [$complaintA, $complaintB, $complaintV, $complaintG, $complaintD, $complaintO],
            "titleText" => "Жалобы"
        ];
        return $obj;
    }

    function prepareChartReturns($statistic)
    {
        $returnA = $this->nullCheck($statistic["returnToA"]+$statistic["returnAToA"]+$statistic["returnBToA"]+$statistic["returnVToA"]+$statistic["returnGToA"]+$statistic["returnDToA"]+$statistic["returnOToA"]);
        $returnB = $this->nullCheck($statistic["returnToB"]+$statistic["returnAToB"]+$statistic["returnBToB"]+$statistic["returnVToB"]+$statistic["returnGToB"]+$statistic["returnDToB"]+$statistic["returnOToB"]);
        $returnV = $this->nullCheck($statistic["returnToV"]+$statistic["returnAToV"]+$statistic["returnBToV"]+$statistic["returnVToV"]+$statistic["returnGToV"]+$statistic["returnDToV"]+$statistic["returnOToV"]);
        $returnG = $this->nullCheck($statistic["returnToG"]+$statistic["returnAToG"]+$statistic["returnBToG"]+$statistic["returnVToG"]+$statistic["returnGToG"]+$statistic["returnDToG"]+$statistic["returnOToG"]);
        $returnD = $this->nullCheck($statistic["returnToD"]+$statistic["returnAToD"]+$statistic["returnBToD"]+$statistic["returnVToD"]+$statistic["returnGToD"]+$statistic["returnDToD"]+$statistic["returnOToD"]);
        $returnO = $this->nullCheck($statistic["returnToO"]+$statistic["returnAToO"]+$statistic["returnBToO"]+$statistic["returnVToO"]+$statistic["returnGToO"]+$statistic["returnDToO"]+$statistic["returnOToO"]);
        
        $obj = [
            "labels" => ['А', 'Б', 'В', 'Г', 'Д', 'О'],
            "data" => [$returnA, $returnB, $returnV, $returnG, $returnD, $returnO],
            "titleText" => "Возвраты"
        ];
        return $obj;
    }

    function prepareStatisticText($statistic)
    {
        return "
        <ul class='nav nav-pills mt-3 mb-3' id='pills-tab' role='tablist'>
        <li class='nav-item' role='presentation'>
          <button class='nav-link active' id='pills-control-tab' data-bs-toggle='pill' data-bs-target='#pills-control' type='button' role='tab' aria-controls='pills-control' aria-selected='true'>Контроль</button>
        </li>
        <li class='nav-item' role='presentation'>
          <button class='nav-link' id='pills-complaint-tab' data-bs-toggle='pill' data-bs-target='#pills-complaint' type='button' role='tab' aria-controls='pills-complaint' aria-selected='false'>Жалобы</button>
        </li>
        <li class='nav-item' role='presentation'>
          <button class='nav-link' id='pills-return-tab' data-bs-toggle='pill' data-bs-target='#pills-return' type='button' role='tab' aria-controls='pills-return' aria-selected='false'>Возвраты</button>
        </li>
        <li class='nav-item' role='presentation'>
          <button class='nav-link' id='pills-changecategory-tab' data-bs-toggle='pill' data-bs-target='#pills-changecategory' type='button' role='tab' aria-controls='pills-changecategory' aria-selected='false'>Изменение категории</button>
        </li>
        " . (Profile::isHavePermission("confirmation") ? "
        <li class='nav-item' role='presentation'>
          <button class='nav-link' id='pills-confirmation-tab' data-bs-toggle='pill' data-bs-target='#pills-confirmation' type='button' role='tab' aria-controls='pills-confirmation' aria-selected='false'>Утверждение</button>
        </li>" : "") .
      "</ul>
      <div class='tab-content' id='pills-tabContent'>
        <div class='tab-pane fade show active' id='pills-control' role='tabpanel' aria-labelledby='pills-control-tab'>
        
        <!-- Начало -->
            <div class='d-flex gap-2' style='flex-flow: wrap;'>
                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>A <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["controlAToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["controlAToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["controlAToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["controlAToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["controlAToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["controlAToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Б <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["controlBToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["controlBToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["controlBToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["controlBToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["controlBToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["controlBToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>В <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["controlVToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["controlVToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["controlVToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["controlVToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["controlVToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["controlVToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Г <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["controlGToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["controlGToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["controlGToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["controlGToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["controlGToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["controlGToO"]) . "
                    </div>
                </div>

                <div class='healthCard'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Д <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["controlDToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["controlDToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["controlDToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["controlDToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["controlDToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["controlDToO"]) . "
                    </div>
                </div>
            </div>  
        <!-- Конец -->
        </div>
        <div class='tab-pane fade' id='pills-complaint' role='tabpanel' aria-labelledby='pills-complaint-tab'>
        
        <!-- Начало -->
            <div class='d-flex gap-2' style='flex-flow: wrap;'>
                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>A <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["complaintAToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["complaintAToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["complaintAToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["complaintAToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["complaintAToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["complaintAToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Б <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["complaintBToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["complaintBToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["complaintBToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["complaintBToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["complaintBToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["complaintBToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>В <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["complaintVToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["complaintVToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["complaintVToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["complaintVToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["complaintVToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["complaintVToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Г <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["complaintGToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["complaintGToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["complaintGToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["complaintGToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["complaintGToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["complaintGToO"]) . "
                    </div>
                </div>

                <div class='healthCard'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Д <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["complaintDToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["complaintDToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["complaintDToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["complaintDToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["complaintDToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["complaintDToO"]) . "
                    </div>
                </div>
            </div>
        <!-- Конец -->
        
        </div>
        <div class='tab-pane fade' id='pills-return' role='tabpanel' aria-labelledby='pills-return-tab'>
        
        <!-- Начало -->
            <div class='d-flex gap-2' style='flex-flow: wrap;'>
                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>A <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["returnAToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["returnAToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["returnAToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["returnAToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["returnAToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["returnAToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Б <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["returnBToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["returnBToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["returnBToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["returnBToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["returnBToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["returnBToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>В <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["returnVToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["returnVToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["returnVToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["returnVToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["returnVToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["returnVToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Г <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["returnGToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["returnGToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["returnGToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["returnGToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["returnGToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["returnGToO"]) . "
                    </div>
                </div>

                <div class='healthCard'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Д <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["returnDToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["returnDToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["returnDToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["returnDToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["returnDToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["returnDToO"]) . "
                    </div>
                </div>
            </div>
        <!-- Конец -->

        </div>
        <div class='tab-pane fade' id='pills-changecategory' role='tabpanel' aria-labelledby='pills-changecategory-tab'>
        
        <!-- Начало -->
        <div class='d-flex gap-2' style='flex-flow: wrap;'>
            <div class='healthCard me-2'>
                <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>A <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                <div class='healthBody'>
                    А — " . $this->nullCheck($statistic["changeCategoryAToA"]) . " <br>
                    Б — " . $this->nullCheck($statistic["changeCategoryAToB"]) . "
                </div>
            </div>

            <div class='healthCard me-2'>
                <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Б <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                <div class='healthBody'>
                    А — " . $this->nullCheck($statistic["changeCategoryBToA"]) . " <br>
                    Б — " . $this->nullCheck($statistic["changeCategoryBToB"]) . "
                </div>
            </div>

        </div>
        <!-- Конец -->
        </div>

        <div class='tab-pane fade' id='pills-confirmation' role='tabpanel' aria-labelledby='pills-confirmation-tab'>
        <!-- Начало -->
            <div class='d-flex gap-2' style='flex-flow: wrap;'>
                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>A <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["confirmationAToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["confirmationAToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["confirmationAToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["confirmationAToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["confirmationAToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["confirmationAToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Б <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["confirmationBToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["confirmationBToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["confirmationBToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["confirmationBToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["confirmationBToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["confirmationBToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>В <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["confirmationVToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["confirmationVToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["confirmationVToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["confirmationVToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["confirmationVToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["confirmationVToO"]) . "
                    </div>
                </div>

                <div class='healthCard me-2'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Г <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["confirmationGToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["confirmationGToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["confirmationGToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["confirmationGToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["confirmationGToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["confirmationGToO"]) . "
                    </div>
                </div>

                <div class='healthCard'>
                    <div class='healthHeader'><div class='align-self-center col' style='text-align: center;'>Д <i class='fa fa-angle-double-right' aria-hidden='true'></i></div></div>
                    <div class='healthBody'>
                        А — " . $this->nullCheck($statistic["confirmationDToA"]) . " <br>
                        Б — " . $this->nullCheck($statistic["confirmationDToB"]) . " <br>
                        В — " . $this->nullCheck($statistic["confirmationDToV"]) . " <br>
                        Г — " . $this->nullCheck($statistic["confirmationDToG"]) . " <br>
                        Д — " . $this->nullCheck($statistic["confirmationDToD"]) . " <br>
                        О — " . $this->nullCheck($statistic["confirmationDToO"]) . "
                    </div>
                </div>
            </div>  
        <!-- Конец -->
        </div>
      </div>
            

            <style>
                .healthCard {
                    display: flex;
                    border: 1px solid rgba(0, 0, 0, 0.175);
                    border-radius: 0.375rem;
                    padding: 0;
                    flex: 1 0 0%;
                }
                
                .healthHeader {
                    background-color: rgba(33, 37, 41, 0.03);
                    border-right: 1px solid rgba(0, 0, 0, 0.175);
                    display: flex;
                    padding: 0.5rem;
                    flex: 1 0 0%;
                }

                .healthBody {
                    padding: 5px 0.5rem;
                }
            </style>
        </div>";
    }

    function getStatistic($userID)
    {
        $statistic = $this->getStatisticData($userID);

        $result = [
            "chartAdjustment" => $this->prepareChartAdjustment($statistic["chartAdjustment"]),
            "chartConscripts" => $this->prepareChartConscripts($statistic["chartConscripts"]),
            "chartChangeCategory" => $this->prepareChartChangeCategory($statistic),
            "chartControl" => $this->prepareChartControl($statistic),
            "chartComplaint" => $this->prepareChartComplaint($statistic),
            "chartReturn" => $this->prepareChartReturns($statistic),
            "statisticText" => $this->prepareStatisticText($statistic)
        ];

        return json_encode($result);
    }
}