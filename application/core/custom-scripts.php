<?
/*
Подключает кастомные .js скрипты по шаблону ["ссылка на страницу", "называние скрипта"]
*/

//Расположение файлов скриптов.
$customDir = "/js/custom_scripts";

//Привязка скриптов к странице.
$custom_scripts = [
    ["admin", "adminPage.js"],
    ["main", "mainPage.js"],
    ["pattern", "patternPage.js"],
    ["adjustment", "adjustmentPage.js"],
    ["conscription", "conscriptionPage.js"],
    ["complaint", "complaintPage.js"]
];

for ($i=0; $i < count($custom_scripts); $i++) { 
    if(!empty(Route::$mainRoute) && Route::$mainRoute == $custom_scripts[$i][0]) {
        $customURL = $customDir . "/" . $custom_scripts[$i][1];
        echo "<script src='$customURL' type='text/javascript'></script>";
    } elseif (empty(Route::$mainRoute) && $custom_scripts[$i][0] == "main") {
        echo "<script src='$customDir/mainPage.js' type='text/javascript'></script>";
    }
}

?>