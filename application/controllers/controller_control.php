<?

class Controller_Control extends Controller
{
	
	function action_index()
	{	
		if (Profile::isHavePermission("control")) {
			if(isset($_GET["id"]))
				$data["documentID"] = $_GET["id"]; 

			$data["control"] = Helper::getConscriptsWithDocuments("control", Profile::isHavePermission("viewForAll") ? null : Profile::$user["id"]);
			$this->view->generateView('control_view.php', "Контроль", $data);
		}
		else
			$this->view->failAccess();
	}
	
}