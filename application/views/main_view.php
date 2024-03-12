<?
/*
Файл, подключаемый controller_main.php.
> содержит разметку основной страницы.
*/
?>


<div class="row row-cols-2">
    <div class="col-3">
        <div class="p-4 mb-3 rounded-3 border shadow">
            <h3 class="display-6 lh-1 fs-2">Информация</h3>
            <div class="pt-lg-3">
                <p class="lead mb-1">
                    <b>ФИО:</b>
                    <? echo Profile::$user["name"]; ?>
                </p>
                <p class="lead mb-1">
                    <b>Специальность:</b><a title="<? foreach (Profile::$user["permissions"] as $key => $value)
                        echo strtoupper($value) . " "; ?>">
                        <? echo Config::getValue('userType')[Profile::$user["position"]][0]; ?>
                    </a>
                </p>
                <p class="lead mb-3">
                    <b>База данных:</b>
                    <? echo Helper::convertAdventPeriodToString(Database::getCurrentBase()); ?>
                </p>
                <button class="btn btn-outline-danger w-100" id="logout">Выход из аккаунта</button>
            </div>
        </div>

        <? if (Profile::isHavePermission("inspection")) { ?>
            <div id="inspectionBlock" class="p-4 mb-3 rounded-3 border shadow">
                <h3 class="display-6 mb-0 lh-1 fs-2">Обследования: 0</h3>
            </div>
        <? } ?>

        <? if (Profile::isHavePermission("return")) { ?>
            <div id="returnBlock" class="p-4 mb-3 rounded-3 border shadow">
                <h3 class="display-6 mb-0 lh-1 fs-2">Возвраты: 0</h3>
            </div>
        <? } ?>
    </div>

    <div class="col-9">
        <? if (Profile::isHavePermission("adjustment")) { ?>

            <div class="p-4 mb-3 rounded-3 border shadow">
                <h3 class="display-6 lh-1 fs-2">Отработка - общая информация</h3>
                <div class="pt-lg-3 d-flex">
                    <div class="w-50">
                        <p class="lead mb-1">
                            <b>Всего дел в отработке:</b> <? print_r($data["adjustmentChartsInfo"][0]) ?> <br>
                            &nbsp; Контроль - прибыло: 13 <br>
                            &nbsp; Контроль - не прибыло: 2 <br>
                            &nbsp; Утверждено: 5 <br>
                            &nbsp; Отработка: 2 <br>
                            <br>
                            <b>Категории годности:</b> <br>
                            &nbsp; А: 4 <br>
                            &nbsp; Б: 4 <br>
                            &nbsp; В: 8 <br>
                            &nbsp; Г: 2 <br>
                            &nbsp; Д: 2 <br>
                            &nbsp; Обследование: 2
                        </p>
                    </div>

                    <div id="ChartDon" class="w-50">
                        <div class="app-chart__canvas">
                            <canvas class="chartAdjustment"></canvas>
                        </div>
                    </div>

                </div>
            </div>
        <? } ?>

        <? if (Profile::isHavePermission("statistic")) { ?>
            <div class="p-4 mb-3 rounded-3 border shadow">
                <h3 class="display-6 lh-1 fs-2">Статистика</h3>
                <div class="pt-lg-3">
                    <p class="lead mb-1">{блок статистики}</p>
                </div>
            </div>
        <? } ?>

        <? if (Profile::isHavePermission("complaint")) { ?>
            <div class="p-4 mb-3 rounded-3 border shadow">
                <h3 class="display-6 lh-1 fs-2">Жалобы и консультации</h3>
                <div class="pt-lg-3 d-flex">

                    <div id="Chart1" class="w-50">
                        <!--<p class="lead mb-1">Всего жалоб и консультаций: 11</p>-->
                        <div class="app-chart__canvas">
                            <canvas class="chartComplaint1"></canvas>
                        </div>
                    </div>
                    <div id="Chart2" class="w-50">
                        <!--<p class="lead mb-1">Категории годности по жалобам: </p>-->
                        <div class="app-chart__canvas">
                            <canvas class="chartComplaint2"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</div>