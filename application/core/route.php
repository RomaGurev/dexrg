<?
/*
Класс-маршрутизатор для определения запрашиваемой страницы.
> цепляет классы контроллеров и моделей;
> создает экземпляры контролеров страниц и вызывает действия этих контроллеров.
*/

class Route
{
	public static $mainRoute = "";
	private static $debugRoute = false;

	static function init()
	{
		$routeExploder = explode('?', $_SERVER['REQUEST_URI']);

		// контроллер и действие по умолчанию
		$controller_name = 'Main';
		$action_name = 'index';

		$routes = explode('/', $routeExploder[0]);

		// получаем имя контроллера
		if (!empty($routes[1]) && mb_substr($routes[1], 0, 1) != "?") {
			$controller_name = $routes[1];
			static::$mainRoute = $routes[1];
		}

		// получаем имя экшена
		if (!empty($routes[2])) {
			$action_name = $routes[2];
		}

		// добавляем префиксы
		$fullcontroller_name = 'Controller_' . $controller_name;
		$fullaction_name = 'action_' . $action_name;

		//подключение файла с классом контроллера
		$controller_path = "application/controllers/" . strtolower($fullcontroller_name) . '.php';
		if (file_exists($controller_path)) {
			include $controller_path;
		} else {
			Route::ErrorPage();
		}

		// создаем контроллер
		$controller = new $fullcontroller_name;
		$action = $fullaction_name;

		if (static::$debugRoute) {
			echo "Полный адрес: " . $_SERVER['REQUEST_URI'] . " Контроллер: " . $fullcontroller_name . " Action: " . $fullaction_name . " GetData: " . $routeExploder[1];
		} else {
			if (method_exists($controller, $action)) {
				// вызываем действие контроллера
				$controller->$action();
			} else {
				Route::Direct(strtolower($controller_name));
			}
		}
	}

	static function Direct($url = "")
	{
		header('Location:' . 'http://' . $_SERVER['HTTP_HOST'] . '/' . $url);
	}

	static function ErrorPage()
	{
		header('HTTP/1.1 404 Not Found');
		header("Status: 404 Not Found");
		Route::Direct("error");
	}

}