<?

class Controller_Complaint extends Controller
{
	
	function action_index()
	{	
		if (Profile::isHavePermission("complaint"))
			$this->view->generateView('complaint_view.php', "Жалобы");
		else
			$this->view->failAccess();
	}
	
}