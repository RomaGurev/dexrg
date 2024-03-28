<?

class Controller_Return extends Controller
{
	function action_index()
	{	
		if (Profile::isHavePermission("return")) {
			if(isset($_GET["id"]))
				$data["documentID"] = $_GET["id"]; 

			$data["return"] = Helper::getConscriptsWithDocuments("return", Profile::isHavePermission("viewForAll") ? null : Profile::$user["id"]);
			$this->view->generateView('return_view.php', "Возврат", $data);
		}
		else
			$this->view->failAccess();
	}
}