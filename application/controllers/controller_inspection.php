<?

class Controller_Inspection extends Controller
{
	
	function action_index()
	{	
		if (Profile::isHavePermission("inspection"))
			$this->view->generateView('inspection_view.php', "Обследования");
		else
			$this->view->failAccess();
	}
	
}