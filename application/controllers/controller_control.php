<?

class Controller_Control extends Controller
{
	
	function action_index()
	{	
		if (Profile::isHavePermission("control")) 
			$this->view->generateView('control_view.php', "Контроль");
		else
			$this->view->failAccess();
	}
	
}