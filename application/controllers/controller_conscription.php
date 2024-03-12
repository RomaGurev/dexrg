<?

class Controller_Conscription extends Controller
{
	
	//Функция отображения страницы регистрации призывника
	function action_editor()
	{	
		if (Profile::$isAuth) {
			if(isset($_GET["back"])) {
				$data["activeMenuItem"] = $_GET["back"];
				$data["documentType"] = $_GET["back"];
			}
			else
				$data["activeMenuItem"] = "";

			$data["vkList"] = $this->getVKList();
			$data["patternList"] = $this->getPatternList();
			$data["nextDocumentNumber"] = $this->getNextDocumentNumber($data["documentType"]);

			if(isset($_GET["id"])) {
				$data['currentConscript'] = $this->getConscriptByID($_GET["id"]);

				if($data['currentConscript'] != null && $data['currentConscript'][0]['ownerID'] == Profile::$user['id'])
					$this->view->generateView('conscriptionEditor_view.php', "Редактирование призывника", $data);
				else
					$this->view->errorPage('Идентификатор призывника не найден.');

			} else {
				$this->view->generateView('conscriptionEditor_view.php', "Регистрация призывника", $data);
			}
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

	//Функция для получения следующего номера документа
	function getNextDocumentNumber($docType) {
		return Database::execute("SELECT documentNumber FROM conscript WHERE documentType=:documentType ORDER BY documentNumber DESC LIMIT 1", 
								["documentType" => $docType], 
								"current")[0]["documentNumber"]+1;
	}

	//Функция для получения призывника по ID
	function getConscriptByID($id)
	{
		$quary = "SELECT * FROM `conscript` WHERE id=:id;";
		$dataArr = [
			"id" => $id
		];
		return Database::execute($quary, $dataArr, "current");
	}

} 