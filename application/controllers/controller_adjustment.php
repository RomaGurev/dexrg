<?

class Controller_Adjustment extends Controller
{
	 
	function action_index()
	{	
		if (Profile::isHavePermission("adjustment")) 
		{
			$this->view->generateView('adjustment_view.php', "Отработка");
		}
		else
			$this->view->failAccess();
	}

}