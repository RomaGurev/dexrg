<?

class Controller_Error extends Controller
{
	//Функция отображения страницы ошибка с проверкой наличия доступа
	function action_index()
	{
		$this->view->generateView('error_view.php', "Страница не найдена");
	}
}
