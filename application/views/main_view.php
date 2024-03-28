<?
/*
Файл, подключаемый controller_main.php.
> содержит разметку основной страницы.
*/

if(isset($_GET["conscript"])) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                openConscriptModal(" . $_GET["conscript"] . ");
            });
          </script>";
}

?>


<div class="row row-cols-2">
    <div class="col-3">
        <div class="p-4 mb-3 rounded-3 border shadow" style="overflow-wrap: break-word;">
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
    </div>

    <div class="col-9">
        <div class="p-4 mb-3 rounded-3 border shadow">
            <div class="row">
                <div class="col">
                    <h3 class="display-6 lh-1 fs-2">Поиск учетных карт призывников</h3>
                </div>

                <?  if (Profile::isHavePermission("canAdd")) { ?>
                <div class="col-md-auto"><a href="/conscription/editor?back=" class="btn btn-outline-success">Регистрация призывника</a></div>
                <? } ?>
            </div>


            <div class="mt-2 d-flex">
                <input id="showSelect" class="d-none" value="false">
                <input type="text" id="searchInput" class="form-control me-2" placeholder="Введите запрос...">

                <select id="searchType" class="form-control form-select" style="width: 80%;cursor:pointer;">
                    <?
                    foreach (Config::getValue("searchType") as $key => $value) {
                        echo '<option value="' . $key . '">' . $value . '</option>';
                    }
                    ?>
                </select>
            </div>


            <div id="searchResult" style="overflow: hidden; height: 0px;" class="mt-2"></div>
        </div>

        <div class="p-4 mb-3 rounded-3 border shadow">
            <div class="d-flex">
                <div class="col-4 col-xl-6">
                    <h3 class="display-6 lh-1 fs-2">Общая информация</h3>
                    <div id="statisticText" class="lead mb-1 pt-lg-3">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                    </div>
                </div>
                <div class="col-8 col-xl-6">
                    <canvas class="chartAdjustment"></canvas>
                </div>
            </div>
        </div>

            <div class="p-4 mb-3 rounded-3 border shadow">
                <h3 class="display-6 lh-1 fs-2">Изменение категории, жалобы и возвраты</h3>
                <div class="pt-lg-3 d-flex">
                    <div class="col w-50">
                        <canvas class="chartComplaint1"></canvas>
                    </div>
                    <div class="col w-50">
                        <canvas class="chartComplaint2"></canvas>
                    </div>
                </div>
            </div>
    </div>
</div>