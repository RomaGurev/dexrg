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
            "labels" => ['А', 'Б', 'В', 'Г', 'Д', 'О'],
            "data" => [3, 12, 16, 9, 5, 3],
            "titleText" => "Категории годности - всего: 22"
        ];
        return $obj;
    }

    function prepareChartComplaint($userID)
    {
        $obj = [
            "labels" => ['Изменение категории', 'Жалобы', 'Возвраты'],
            "data" => [35, 5, 12],
            "titleText" => "Всего: 52"
        ];
        return $obj;
    }

    function prepareChartHealthCategory($userID)
    {
        $obj = [
            "labels" => ['A', 'Б', 'В', 'Г', 'Д'],
            "data" => [10, 20, 7, 9, 6],
            "titleText" => "Распределение категорий: 52"
        ];
        return $obj;
    }

    function prepareStatisticText($userID)
    {
        return "<b>Всего дел в работе: 115</b><br>
        Изменение категории: 13 <br>
        Контроль: 2 <br>
        Жалоба: 5 <br>
        Возврат: 2 <br>
        <b>Всего дел завершено: 115</b><br>
        Изменение категории: 13 <br>
        Контроль: 2 <br>
        Жалоба: 5 <br>
        Возврат: 2 <br>";
    }

    function getStatistic($userID)
    {
        $result = [
            "chartAdjustment" => $this->prepareChartAdjustment($userID),
            "chartHealthCategory" => $this->prepareChartHealthCategory($userID),
            "chartComplaint" => $this->prepareChartComplaint($userID),
            "statisticText" => $this->prepareStatisticText($userID)
        ];

        return json_encode($result);
    }
}