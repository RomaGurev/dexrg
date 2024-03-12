<?

class Controller_Adjustment extends Controller
{
	
	function action_index()
	{	
		if (Profile::isHavePermission("adjustment")) 
		{
			$data['adjustmentList'] = $this->getAdjustmentList();
			$this->view->generateView('adjustment_view.php', "Отработка", $data);
		}
		else
			$this->view->failAccess();
	}
	

	function getAdjustmentList() 
	{
		//WHERE documentType = control OR documentType = adjustment
		return Database::execute("SELECT * FROM conscript WHERE ownerID=:ownerID", ["ownerID" => Profile::$user["id"]], "current");
	}

}