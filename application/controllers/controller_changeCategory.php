<?

class Controller_ChangeCategory extends Controller
{
	function action_index()
	{	
		if (Profile::isHavePermission("changeCategory")) {
			if(isset($_GET["id"]))
				$data["documentID"] = $_GET["id"]; 

			$data["changeCategory"] = Helper::getConscriptsWithDocuments("changeCategory", 1, Profile::isHavePermission("viewForAll") ? null : Profile::$user["id"]);
			$this->view->generateView('changeCategory_view.php', "Изменение категории", $data);
		}
		else
			$this->view->failAccess();
	}


}