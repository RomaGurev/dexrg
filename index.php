<? 
/*
Файл точки входа в приложение.
Необходим для подключения библиотек и инициализации маршрутизатора.
*/
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

Profile::authInit(); //инициализация учетных записей
Route::init(); // инициализация маршрутизатора

