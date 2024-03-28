<?

class Controller_Print extends Controller
{

	function action_index()
	{
		if (isset($_GET["template"]) && $_GET["id"]) {
			$templatePath = 'printTemplate/' . $_GET["template"] . '.template';

			if (file_exists($templatePath)) {
				$action = $_GET["template"] . "Prepare"; //protocolPrepare
				if (method_exists($this, $action)) {
					$this->view->generateView($this->$action(file_get_contents($templatePath), $_GET["id"]), "Печать шаблона " . strtoupper($_GET["template"]), null, true);
				} else {
					$this->view->errorPage("Метод развертывания данных для печати не найден.");
				}
			} else {
				$this->view->errorPage("Файл шаблона печати не найден.");
			}
		} else {
			$this->view->errorPage("Идентификатор шаблона печати или записи некорректен.");
		}
	}

	function getEditableString($string) {
		return "<div contenteditable='true' style='display: inline;outline: none;'>" . $string . "</div>";
	}

	function replaceData($data, $fileContent) {
		foreach (Config::getValue("printValues") as $key => $value) 
			$fileContent = str_replace($key, isset($data[$value]) ? (empty($data[$value]) ? $this->getEditableString("{НЕ ЗАПОЛНЕНО}") : $this->getEditableString($data[$value])) : $this->getEditableString("{НЕКОРРЕКТНОЕ ЗНАЧЕНИЕ}"), $fileContent);
			
		return $fileContent;
	}

	function protocolPrepare($fileContent, $id) {
		$data = Database::execute("SELECT * FROM `conscript` WHERE id = :id", ["id" => $id], "current")[0];
		$data["vkName"] = Helper::getVKNameById($data["vk"])["fullNameNotUnique"];

		$documents = Helper::getResultDocuments($id);
		foreach ($documents as $document) {
			$data["complaint"] .= $document["complaint"] . "<br>";
			$data["anamnez"] .= $document["anamnez"] . "<br>";
			$data["objectData"] .= $document["objectData"] . "<br>";
			$data["specialResult"] .= $document["specialResult"] . "<br>";
			$data["diagnosis"] .= $document["diagnosis"] . "<br>";
		}

		//$data["rvkMedicalAppointment"] //РВК Назначение
		$data["rvkMedicalAppointment"] = "Категория годности<br>" . Helper::getHealthCategoryNameByID($data["healthCategory"]);



		//$data["medicalAppointment"] //Назначение;
		//$data["result"] //Решение
		

		//////
		//if($data["documentType"] == "complaint")
		//	$data["anamnez"] .= "<br>Не согласен с решением районной призывной комиссии.";
		/////
		$fileContent = $this->replaceData($data, $fileContent);
		return $fileContent;
	}

	function letterPrepare($fileContent, $id) {
		$data = Database::execute("SELECT * FROM `conscript` WHERE id = :id", ["id" => $id], "current")[0];
		
		$fileContent = $this->replaceData($data, $fileContent);

		return $fileContent;
	}

	function extractPrepare($fileContent, $id) {
		return $fileContent;
	}

	function examinationPrepare($fileContent, $id) {
		$data = Database::execute("SELECT * FROM `documents` WHERE id = :id", ["id" => $id], "current")[0];
		$data["creator"] = Database::execute("SELECT name FROM staff WHERE id=:id", ["id" => $data["creatorID"]])[0]["name"];
		$data["documentDate"] = Helper::formatDateToView($data["documentDate"]);

		$conscript = Database::execute("SELECT * FROM `conscript` WHERE id = :id", ["id" => $data["conscriptID"]], "current")[0];

		$data["name"] = $conscript["name"];
		$data["birthDate"] = $conscript["birthDate"];
		$data["rvkArticle"] = $conscript["rvkArticle"];
		$data["healthCategory"] = Helper::getHealthCategoryNameByID($data["healthCategory"]);

		$fileContent = $this->replaceData($data, $fileContent);

		return $fileContent;
	}

}