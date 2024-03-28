<? 
/*
Файл точки входа в приложение.
Необходим для подключения библиотек и инициализации маршрутизатора.
*/

//Отключение кэша (убрать после окончания разработки)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Cache-Control: post-check=0,pre-check=0", false);
header("Cache-Control: max-age=0", false);
header("Pragma: no-cache");
//Отключение кэша (убрать после окончания разработки)

ini_set('display_errors', 1);
date_default_timezone_set('Asia/Novosibirsk');

// подключаем файлы ядра
require_once 'application/core/view.php'; //подключение view
require_once 'application/core/controller.php'; //подключение контроллера

require_once 'application/database/database.php'; //подключение баз данных
require_once 'application/core/route.php'; //подключение маршрутизатора

require_once 'application/core/config.php'; //подключение конфигурации
require_once 'application/core/profile.php'; //подключение учетных записей
require_once 'application/additions/helper.php'; //подключение класса помощника
require_once 'application/additions/documentBuilder.php'; //подключение класса для вывода документов

Profile::authInit(); //инициализация учетных записей
Route::init(); // инициализация маршрутизатора

