<?

class Controller_Confirmation extends Controller
{
	
	function action_index()
	{	
		if (Profile::isHavePermission("confirmation")) {
			if(isset($_GET["id"]))
				$data["documentID"] = $_GET["id"]; 

			$data["confirmation"] = Helper::getConscriptsWithDocuments("confirmation", 0, Profile::isHavePermission("viewForAll") ? null : Profile::$user["id"]);
			$this->view->generateView('confirmation_view.php', "Утверждение", $data);
		}
		else
			$this->view->failAccess();
	}

} 