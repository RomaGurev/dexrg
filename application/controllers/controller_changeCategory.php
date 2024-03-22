<?

class Controller_ChangeCategory extends Controller
{
	function action_index()
	{	
		if (Profile::isHavePermission("changeCategory")) 
			$this->view->generateView('changeCategory/changeCategory_view.php', "Изменение категории");
		else
			$this->view->failAccess();
	}

	function action_editor()
	{	
		if (Profile::isHavePermission("changeCategory")) 
			if(isset($_GET["id"]) || isset($_GET["conscript"])) {
				$data['currentChangeCategory'] = $this->getChangeCategoryByID($_GET["id"]);
				$data['selectedConscript'] = $this->getConscriptByID($_GET["conscript"]);
				$data['nextDocumentID'] = $this->getNextDocumentID();

				$this->view->generateView('changeCategory/changeCategoryEditor_view.php', "Добавление изменения категории", $data);
			} else {
				$this->view->generateView('changeCategory/changeCategoryEditor_view.php', "Добавление изменения категории");
			}
		else
			$this->view->failAccess();
	}

	function getConscriptByID($id) {
		return Database::execute("SELECT * from `conscript` WHERE id=:id", ["id" => $id], "current")[0];
	}

	function getNextDocumentID() {
		return Database::execute("SELECT id FROM changeCategory ORDER BY id DESC LIMIT 1", null, "current")[0]["id"]+1;
	}

	function getChangeCategoryByID($id) {
		return "15";
	}
}