<?

class Controller_Complaint extends Controller
{
	//Функция отображения страницы документа "Жалоба" с проверкой наличия доступа
	function action_index()
	{	
		if (Profile::isHavePermission("complaint")) {
			if(isset($_GET["id"]))
				$data["documentID"] = $_GET["id"]; 

			$data["complaint"] = Helper::getConscriptsWithDocuments("complaint", 1, Profile::isHavePermission("viewForAll") ? null : Profile::$user["id"]);
			$this->view->generateView('complaint_view.php', "Жалобы", $data);
		}
		else
			$this->view->failAccess();
	}
}