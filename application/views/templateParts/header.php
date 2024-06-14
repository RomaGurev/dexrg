<?
/*
Файл, подключаемый template.php.
> содержит разметку header'а страницы.
*/

//Список возможных элементов меню с привязкой к разрешениям из config.json
$menuItems = Config::getValue("menuItems");
?>

<? if (Profile::$isAuth) { ?>
    <header class="text-white" style="padding-right: 0.75rem; padding-left: 0.75rem;">
        <div class="container bg-gradient p-3 shadow"
            style="background-color: rgb(<? echo Profile::isArchiveMode() ? "33, 37, 41" : "2, 86, 96" ?>); border-radius: 0 0 .375rem .375rem;">
            <div class="d-flex flex-wrap align-items-center">
                <div id="logoFlex" class="col-xl-3 col-7">
                    <a href="/" class="text-dark text-decoration-none d-flex">
                        <svg width="48" height="48">
                            <image
                                xlink:href="/images/<? echo Profile::isArchiveMode() ? "logo_test_archive" : "logo" ?>.svg?rgnsk"
                                src="/images/<? echo Profile::isArchiveMode() ? "logo_test_archive" : "logo" ?>.svg?rgnsk" width="48"
                                height="48" />
                        </svg>
                        <div class="display-6 text-white mb-0 ms-2 me-2 d-flex align-items-center">
                            <? echo Profile::isArchiveMode() ? "ВВК Архив" : "ВВК" ?>
                        </div>
                    </a>
                </div>

                <div id="menuFlex" class="flex-fill order-1 order-xl-0 mt-3 mt-xl-0">
                    <ul class="nav nav-pills navbar-light justify-content-xl-start justify-content-sm-between">
                        <?
                        foreach (Profile::$user["permissions"] as $key => $value) {
                            if (isset($menuItems[$value])) {
                                if (!isset($data["activeMenuItem"]))
                                    $active = Route::$mainRoute == $menuItems[$value][1] ? "active" : "text-white";
                                else
                                    $active = $data["activeMenuItem"] == $menuItems[$value][1] ? "active" : "text-white";

                                if (Profile::isArchiveMode() && $value == "pattern")
                                    continue;
                                echo "<li class='nav-item'><a href='/" . $menuItems[$value][1] . "' class='nav-link $active'>" . $menuItems[$value][0] . "</a></li>";
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </header>
<? } else { ?>
    <header class="bg-gradient text-white p-3 shadow"
        style="padding-right: 0.75rem; padding-left: 0.75rem; background-color: rgb(<? echo Profile::isArchiveMode() ? "33, 37, 41" : "2, 86, 96" ?>);">
        <div class="container">
            <div class="d-flex">
                <a href="/" class="text-dark text-decoration-none mx-auto d-flex">
                    <svg width="48" height="48">
                        <image xlink:href="/images/<? echo Profile::isArchiveMode() ? "logo_test_archive" : "logo" ?>.svg"
                            src="/images/<? echo Profile::isArchiveMode() ? "logo_test_archive" : "logo" ?>.svg" width="48"
                            height="48" />
                    </svg>
                    <p class="display-6 text-white mb-0 ms-2 me-2"><? echo Profile::isArchiveMode() ? "ВВК Архив" : "ВВК" ?>
                    </p>
                </a>
            </div>
        </div>
    </header>
<? } ?>