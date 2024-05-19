<?

class Controller_Main extends Controller
{

	function action_index()
	{	
		if (Profile::$isAuth)
			$this->view->generateView('main_view.php', "Основная");
		else
			$this->view->generateView('loginPage_view.php', "Авторизация");
	}

	function action_clown() 
	{
		$this->view->generateView('rg_view.php', "Клоун");
	}

	function getBasesForArchiveMode() 
	{
		return 0;
	}
	
}