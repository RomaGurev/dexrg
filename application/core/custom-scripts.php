<?
/*
Подключает кастомные .js скрипты по шаблону ["ссылка на страницу", "называние скрипта"]
*/

//Расположение файлов скриптов.
$customDir = "/js/custom_scripts";

//Привязка скриптов к странице.
$custom_scripts = Config::getValue("customScripts");

foreach ($custom_scripts as $key => $value) {
    if(!empty(Route::$mainRoute) && Route::$mainRoute == $key) {
        $customURL = $customDir . "/" . $value;
        echo "<script src='$customURL?ct=" . filemtime("js\\custom_scripts\\" . $value) . "' type='text/javascript'></script>";
    } elseif (empty(Route::$mainRoute) && $key == "main") {
        echo "<script src='$customDir/mainPage.js?ct=" . filemtime("js\\custom_scripts\\mainPage.js") . "' type='text/javascript'></script>";
    }
}