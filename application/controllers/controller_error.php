<?

class Controller_Error extends Controller
{
	function action_index()
	{
		$this->view->generateView('error_view.php', "Страница не найдена");
	}
}
