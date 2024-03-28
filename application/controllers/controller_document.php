<?

class Controller_Document extends Controller
{

	function action_index()
	{	
        if(isset($_GET["id"])) {
		    $data['currentDocument'] = $this->getDocumentByID($_GET["id"]);
			$data['currentConscript'] = $this->getConscriptByID($data['currentDocument']['conscriptID']);
		}
        else
            $data['nextDocumentID'] = $this->getNextDocumentID();

        if(isset($_GET["conscript"]))
		    $data['currentConscript'] = $this->getConscriptByID($_GET["conscript"]);

        $data["patternList"] = $this->getPatternList();


        if(isset($_GET["documentType"])) {
			if(array_key_exists($_GET["documentType"], Config::getValue("documentType"))) {
            	$data["activeMenuItem"] = $_GET["documentType"];
		    	$this->view->generateView('document_view.php', isset($_GET["id"]) ? "Редактирование документа" : "Добавление документа", $data);
			} else
				$this->view->errorPage("Некорректный тип документа.");
        }
        else
            $this->view->errorPage("Отсутствуют аргументы типа документа.");
	}
    
    //Функция для получения списка шаблонов активного пользователя
	function getPatternList() {
		return Database::execute("SELECT * FROM patternList WHERE ownerID=:id ORDER BY id DESC", ["id" => Profile::$user["id"]]);
	}

    //Функция для получения призывника по ID
	function getConscriptByID($id) {
		return Database::execute("SELECT * from `conscript` WHERE id=:id", ["id" => $id], "current")[0];
	}

    //Функция для получения документа по ID
	function getDocumentByID($id) {
		return Database::execute("SELECT * FROM `documents` WHERE id=:id", ["id" => $id], "current")[0];
	}

    //Функция для получения следующего номера документа
	function getNextDocumentID() {
		return Database::execute("SELECT id FROM `documents` ORDER BY id DESC LIMIT 1", null, "current")[0]["id"]+1;
	}
}