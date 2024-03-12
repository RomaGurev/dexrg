<?
/*
Файл, подключаемый index.php для работы шаблонизации
> содержит класс View и метод реализации вывода шаблона
*/

class View
{
	/*
	$content_view - виды отображающие контент страниц;
	$title - название страницы, отображаемое в заголовке;
	$data - массив, содержащий элементы контента страницы;
	$print_mode - режим печати, используется для отчетов.
	*/

	function generateView($content_view, $title = "Страница", $data = null, $print_mode = false)
	{
		
		// преобразуем элементы массива данных в переменные
		if(!empty($data) && is_array($data)) {
			extract($data);
		}
		/*
		динамически подключаем общий шаблон (вид),
		внутри которого будет встраиваться вид
		для отображения контента конкретной страницы.
		*/
		$title = "$title - ВВК";
		if(!$print_mode)
			include "application/views/template.php";
		else
			include "application/views/print.php";
	}

	function failAccess() {
		$data["activeMenuItem"] = "";
		$this->generateView('failAccess_view.php', "Ошибка доступа", $data);
	}

	function errorPage($errorMessage = "") {
		$data["activeMenuItem"] = "";
		$data["errorMessage"] = $errorMessage;
		$this->generateView('error_view.php', "Страница не найдена", $data);
	}
}
