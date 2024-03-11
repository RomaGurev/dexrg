<?

class Controller_Protocol extends Controller
{
	
	function action_index()
	{	
		if (Profile::isHavePermission("protocol"))
			$this->view->generateView('protocol_view.php', "Протокол");
		else
			$this->view->failAccess();
	}
	
}