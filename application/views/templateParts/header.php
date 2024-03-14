<? 
/*
Файл, подключаемый template.php.
> содержит разметку header'а страницы.
*/

//Список возможных элементов меню с привязкой к разрешениям из config.json
$menuItems = Config::getValue("menuItems");
?>


<header class="bg-dark text-white p-3 shadow">
    <div class="container p-0">

        <? if (Profile::$isAuth) { ?>

            <div class="d-flex flex-wrap align-items-center">

                <div id="logoFlex" class="col-xl-3 col-7 me-2">
                    <a href="/" class="text-dark text-decoration-none d-flex">
                        <svg width="48" height="48">
                            <image xlink:href="/images/logo.svg" src="/images/logo.svg" width="48" height="48" />
                        </svg>
                        <p class="display-6 text-white mb-0 ms-2 me-2">ВВК</p>
                    </a>
                </div>

                <div id="menuFlex" class="flex-fill order-1 order-xl-0 mt-3 mt-xl-0">
                    <ul class="nav nav-pills navbar-light justify-content-xl-start justify-content-sm-between">
                        <?
                        foreach (Profile::$user["permissions"] as $key => $value) {
                            if (isset($menuItems[$value])) {
                                if(!isset($data["activeMenuItem"]))
                                    $active = Route::$mainRoute == $menuItems[$value][1] ? "active" : "text-white";
                                else
                                    $active = $data["activeMenuItem"] == $menuItems[$value][1] ? "active" : "text-white";

                                echo "<li class='nav-item'><a href='/" . $menuItems[$value][1] . "' class='nav-link $active'>" . $menuItems[$value][0] . "</a></li>";
                            }
                        }
                        ?>
                    </ul>
                </div>

            </div>

        <? } else { ?>
            <div class="d-flex">
                <a href="/" class="text-decoration-none mx-auto d-flex">
                    <svg width="48" height="48">
                        <image xlink:href="/images/logo.svg" src="/images/logo.svg" width="48" height="48" />
                    </svg>
                    <p class="display-6 text-white mb-0 ms-2 me-2">ВВК</p>
                </a>
            </div>
        <? } ?>
    </div>
</header>