<?

class Controller_Complaint extends Controller
{
	
	function action_index()
	{	
		if (Profile::isHavePermission("complaint")) 
		{
			$data['conscriptList'] = $this->getConscriptList();
			$this->view->generateView('complaint_view.php', "Жалобы", $data);
		}
		else
			$this->view->failAccess();
	}
	
	function getConscriptList() 
	{
		return Database::execute("SELECT * FROM conscript", null, "current");
	}
}