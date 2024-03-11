<?

class Controller_Search extends Controller
{

	function action_index()
	{
		if (Profile::$isAuth) {
			$data["activeMenuItem"] = "";
			$this->view->generateView('search_view.php', "Поиск", $data);
		} else
			$this->view->failAccess();
	}

}