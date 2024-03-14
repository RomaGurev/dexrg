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
			$data["nextConscriptID"] = $this->getNextConscriptID();

			if(isset($_GET["id"])) {
				$data['currentConscript'] = $this->getConscriptByID($_GET["id"]);

				if($data['currentConscript'] != null)
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

	//Функция для получения списка военных комиссариатов
	function getVKList() {
		return Database::execute("SELECT * FROM vkList");
	}

	//Функция для получения списка шаблонов активного пользователя
	function getPatternList() {
		return Database::execute("SELECT * FROM patternList WHERE ownerID=:id", ["id" => Profile::$user["id"]]);
	}

	//Функция для получения следующего номера документа
	function getNextConscriptID() {
		return Database::execute("SELECT id FROM conscript ORDER BY id DESC LIMIT 1", null, "current")[0]["id"]+1;
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