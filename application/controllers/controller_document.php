<?

class Controller_Document extends Controller
{

	function action_index()
	{	
        if(isset($_GET["id"])) {
		    $data['currentDocument'] = $this->getDocumentByID($_GET["id"]);
			$data['currentConscript'] = $this->getConscriptByID($data['currentDocument']['conscriptID']);
		}

        if(isset($_GET["conscript"]))
		    $data['currentConscript'] = $this->getConscriptByID($_GET["conscript"]);

        $data["patternList"] = $this->getPatternList();


        if(isset($_GET["documentType"])) {
			if(array_key_exists($_GET["documentType"], Config::getValue("documentType"))) {
            	$data["activeMenuItem"] = $_GET["documentType"];

				if($_GET["documentType"] == "confirmation" && !empty($_GET["conscript"]))
					$data["confirmationInfo"] = $this->getConfirmationInfo($_GET["conscript"]);

		    	$this->view->generateView('document_view.php', isset($_GET["id"]) ? "Редактирование документа" : "Добавление документа", $data);
			} else
				$this->view->errorPage("Некорректный тип документа.");
        }
        else
            $this->view->errorPage("Отсутствуют аргументы типа документа.");
	}
    
    //Функция для получения списка шаблонов активного пользователя
	function getPatternList() {
		$quary = "SELECT * FROM patternList";

		if(!Profile::isHavePermission("viewForAll")) 
		{
			$quary .= " WHERE ownerID=:id";
			$data = array("id" => Profile::$user["id"]);
		}
		$quary .= " ORDER BY id DESC";
		
		return Database::execute($quary, $data);
	}

    //Функция для получения призывника по ID
	function getConscriptByID($id) {
		return Database::execute("SELECT * from `conscript` WHERE id=:id", ["id" => $id], "current")[0];
	}

    //Функция для получения документа по ID
	function getDocumentByID($id) {
		return Database::execute("SELECT * FROM `documents` WHERE id=:id", ["id" => $id], "current")[0];
	}

	//Функция для получения сведений документа "Утверждение"
	function getConfirmationInfo($id) {
		return Database::execute("SELECT rvkDiagnosis, healthCategory FROM `conscript` WHERE id=:id", ["id" => $id], "current")[0];
	}
}