<?

class Controller_Control extends Controller
{
	//Функция отображения страницы документа "Контроль" с проверкой наличия доступа
	function action_index()
	{	
		if (Profile::isHavePermission("control")) {
			if(isset($_GET["id"]))
				$data["documentID"] = $_GET["id"]; 

			$data["control"] = Helper::getConscriptsWithDocuments("control", 1, Profile::isHavePermission("viewForAll") ? null : Profile::$user["id"]);
			$this->view->generateView('control_view.php', "Контроль", $data);
		}
		else
			$this->view->failAccess();
	}
	
}