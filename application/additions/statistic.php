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
    function prepareChartAdjustment($userID) 
    {
        $obj = [
            "labels" => ['Прибыло', 'Не прибыло', 'Утверждено', 'Отработка'],
            "data" => [3, 12, 16, 9],
            "titleText" => "Контроль - всего: 22"
        ];
        return $obj;
    }

    function prepareChartComplaint($userID) 
    {
        $obj = [
            "labels" => ['Прибыло', 'Не прибыло', 'Утверждено', 'Отработка'],
        ];
        return $obj;
    }

    function prepareChartHealthCategory($userID) 
    {
        $obj = [
            "labels" => ['Прибыло', 'Не прибыло', 'Утверждено', 'Отработка'],
        ];
        return $obj;
    }

    function getStatistic($userID)
    {
        $result = [
            "chartAdjustment" => $this->prepareChartAdjustment($userID),
            "chartHealthCategory" => $this->prepareChartHealthCategory($userID),
            "chartComplaint" => $this->prepareChartComplaint($userID)
        ];

        return json_encode($result);
    }
}