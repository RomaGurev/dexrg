<?

class Controller_Return extends Controller
{
	
	function action_index()
	{	
		if (Profile::isHavePermission("return")) 
			$this->view->generateView('return_view.php', "Возврат");
		else
			$this->view->failAccess();
	}
	
}