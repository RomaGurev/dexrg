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
            $query .= " WHERE `documents`.creatorID=:userID";
            $data = ["userID" => $userID];
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

        foreach ($resultDocArr as $value) 
            $statistic[$value["documentType"] . Config::getValue("categoryConverter")[$value["conCat"]] . "To" . Config::getValue("categoryConverter")[$value["docCat"]]]++;

        //chartAdjustment
        $chartAdjustment["controlCount"] = $this->nullCheck($controlCount);
        $chartAdjustment["complaintCount"] = $this->nullCheck($complaintCount);
        $chartAdjustment["returnCount"] = $this->nullCheck($returnCount);
        $chartAdjustment["changeCategoryCount"] = $this->nullCheck($changeCategoryCount);
        $chartAdjustment["allCount"] = $this->nullCheck(count($queryResult));
        //chartAdjustment

        $statistic["chartChangeCategory"] = $chartChangeCategory;
        $statistic["chartAdjustment"] = $chartAdjustment;
        return $statistic;
    }

    function nullCheck($data) 
    {
        return $data ?? 0;
    }

    function prepareChartAdjustment($statistic)
    {
        $obj = [
            "labels" => ['Контроль', 'Жалобы', 'Возвраты','Изменение категории'],
            "data" => [$statistic["controlCount"], $statistic["complaintCount"], $statistic["returnCount"], $statistic["changeCategoryCount"]],
            "titleText" => "Общее количество документов: " . $statistic["allCount"]
        ];

        return $obj;
    }

    function prepareChartChangeCategory($statistic)
    {
        $changeCategoryA = $this->nullCheck($statistic["changeCategoryAToA"]+$statistic["changeCategoryAToB"]);
        $changeCategoryB = $this->nullCheck($statistic["changeCategoryBToA"]+$statistic["changeCategoryBToB"]);

        $obj = [
            "labels" => ['А', 'Б'],
            "data" => [$changeCategoryA, $changeCategoryB],
            "titleText" => "Изменение категории"
        ];
        return $obj;
    }

    function prepareChartControl($statistic)
    {
        $controlA = $this->nullCheck($statistic["controlAToA"]+$statistic["controlAToB"]+$statistic["controlAToV"]+$statistic["controlAToG"]+$statistic["controlAToD"]+$statistic["controlAToO"]);
        $controlB = $this->nullCheck($statistic["controlBToA"]+$statistic["controlBToB"]+$statistic["controlBToV"]+$statistic["controlBToG"]+$statistic["controlBToD"]+$statistic["controlBToO"]);
        $controlV = $this->nullCheck($statistic["controlVToA"]+$statistic["controlVToB"]+$statistic["controlVToV"]+$statistic["controlVToG"]+$statistic["controlVToD"]+$statistic["controlVToO"]);
        $controlG = $this->nullCheck($statistic["controlGToA"]+$statistic["controlGToB"]+$statistic["controlGToV"]+$statistic["controlGToG"]+$statistic["controlGToD"]+$statistic["controlGToO"]);
        $controlD = $this->nullCheck($statistic["controlDToA"]+$statistic["controlDToB"]+$statistic["controlDToV"]+$statistic["controlDToG"]+$statistic["controlDToD"]+$statistic["controlDToO"]);
        $controlO = $this->nullCheck($statistic["controlOToA"]+$statistic["controlOToB"]+$statistic["controlOToV"]+$statistic["controlOToG"]+$statistic["controlOToD"]+$statistic["controlOToO"]);

        $obj = [
            "labels" => ['А', 'Б', 'В', 'Г', 'Д', 'О'],
            "data" => [$controlA, $controlB, $controlV, $controlG, $controlD, $controlO],
            "titleText" => "Контроль"
        ];
        return $obj;
    }

    function prepareChartComplaint($statistic)
    {
        $complaintA = $this->nullCheck($statistic["complaintAToA"]+$statistic["complaintAToB"]+$statistic["complaintAToV"]+$statistic["complaintAToG"]+$statistic["complaintAToD"]+$statistic["complaintAToO"]);
        $complaintB = $this->nullCheck($statistic["complaintBToA"]+$statistic["complaintBToB"]+$statistic["complaintBToV"]+$statistic["complaintBToG"]+$statistic["complaintBToD"]+$statistic["complaintBToO"]);
        $complaintV = $this->nullCheck($statistic["complaintVToA"]+$statistic["complaintVToB"]+$statistic["complaintVToV"]+$statistic["complaintVToG"]+$statistic["complaintVToD"]+$statistic["complaintVToO"]);
        $complaintG = $this->nullCheck($statistic["complaintGToA"]+$statistic["complaintGToB"]+$statistic["complaintGToV"]+$statistic["complaintGToG"]+$statistic["complaintGToD"]+$statistic["complaintGToO"]);
        $complaintD = $this->nullCheck($statistic["complaintDToA"]+$statistic["complaintDToB"]+$statistic["complaintDToV"]+$statistic["complaintDToG"]+$statistic["complaintDToD"]+$statistic["complaintDToO"]);
        $complaintO = $this->nullCheck($statistic["complaintOToA"]+$statistic["complaintOToB"]+$statistic["complaintOToV"]+$statistic["complaintOToG"]+$statistic["complaintOToD"]+$statistic["complaintOToO"]);
        
        $obj = [
            "labels" => ['А', 'Б', 'В', 'Г', 'Д', 'О'],
            "data" => [$complaintA, $complaintB, $complaintV, $complaintG, $complaintD, $complaintO],
            "titleText" => "Жалобы"
        ];
        return $obj;
    }

    function prepareChartReturns($statistic)
    {
        $returnA = $this->nullCheck($statistic["returnAToA"]+$statistic["returnAToB"]+$statistic["returnAToV"]+$statistic["returnAToG"]+$statistic["returnAToD"]+$statistic["returnAToO"]);
        $returnB = $this->nullCheck($statistic["returnBToA"]+$statistic["returnBToB"]+$statistic["returnBToV"]+$statistic["returnBToG"]+$statistic["returnBToD"]+$statistic["returnBToO"]);
        $returnV = $this->nullCheck($statistic["returnVToA"]+$statistic["returnVToB"]+$statistic["returnVToV"]+$statistic["returnVToG"]+$statistic["returnVToD"]+$statistic["returnVToO"]);
        $returnG = $this->nullCheck($statistic["returnGToA"]+$statistic["returnGToB"]+$statistic["returnGToV"]+$statistic["returnGToG"]+$statistic["returnGToD"]+$statistic["returnGToO"]);
        $returnD = $this->nullCheck($statistic["returnDToA"]+$statistic["returnDToB"]+$statistic["returnDToV"]+$statistic["returnDToG"]+$statistic["returnDToD"]+$statistic["returnDToO"]);
        $returnO = $this->nullCheck($statistic["returnOToA"]+$statistic["returnOToB"]+$statistic["returnOToV"]+$statistic["returnOToG"]+$statistic["returnOToD"]+$statistic["returnOToO"]);
        
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
      </ul>
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
            "chartChangeCategory" => $this->prepareChartChangeCategory($statistic),
            "chartControl" => $this->prepareChartControl($statistic),
            "chartComplaint" => $this->prepareChartComplaint($statistic),
            "chartReturn" => $this->prepareChartReturns($statistic),
            "statisticText" => $this->prepareStatisticText($statistic)
        ];

        return json_encode($result);
    }
}