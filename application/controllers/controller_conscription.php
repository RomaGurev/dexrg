<?

class Controller_Conscription extends Controller
{
	
	//Функция отображения страницы регистрации призывника
	function action_editor()
	{	
		if (Profile::$isAuth) {
			if(isset($_GET["back"]))
				$data["activeMenuItem"] = $_GET["back"];
			else
				$data["activeMenuItem"] = "";

			$data["vkList"] = $this->getVKList();
			$data["patternList"] = $this->getPatternList();
			$this->view->generateView('conscriptionEditor_view.php', "Регистрация призывника", $data);
		}
		else
			$this->view->failAccess();
	}

	//Функция отображения страницы поиска дубликатов
	function action_searchDuplicate()
	{
		if(Profile::isHavePermission("searchDuplicate")) {
			$data["activeMenuItem"] = "";
			$this->view->generateView('searchDuplicate_view.php', "Поиск дублей", $data);
		}
		else
			$this->view->failAccess();
	}
	
	//Функция для получения списка военных комиссариатов
	function getVKList() {
		return Database::execute("SELECT * FROM vkList");
	}

	//Функция для получения списка шаблонов активного пользователя
	function getPatternList() {
		return Database::execute("SELECT * FROM patternList WHERE ownerID=:id", ["id" => Profile::$user["id"]]);
	}
} 