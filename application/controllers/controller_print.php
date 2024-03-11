<?

class Controller_Print extends Controller
{

	function action_index()
	{
		$printData = [
			"test" => "testMessage",
		];
		$this->view->generateView('search_view.php', "Поиск", $printData, true);
	}

}