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
			$fileContent = str_replace($key, isset($data[$value]) ? $this->getEditableString($data[$value]) : $this->getEditableString("{НЕКОРРЕКТНОЕ ЗНАЧЕНИЕ}"), $fileContent);
			
		return $fileContent;
	}

	function protocolPrepare($fileContent, $id) {
		$data = Database::execute("SELECT * FROM conscript WHERE id = :id", ["id" => $id], "current")[0];

		$data["ownerID"] = Database::execute("SELECT name FROM staff WHERE id=:id", ["id" => $data["ownerID"]])[0]["name"];
		$data["birthDate"] = Helper::formatDateToView($data["birthDate"]);
		$data["creationDate"] = Helper::formatDateToView($data["creationDate"]);

		$fileContent = $this->replaceData($data, $fileContent);

		return $fileContent;
	}

}