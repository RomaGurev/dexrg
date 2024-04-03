<?

class Controller_Print extends Controller
{

	function action_index()
	{
		if (isset($_GET["template"]) && $_GET["id"]) {
			$templatePath = 'printTemplate/' . $_GET["template"] . '.template';

			if (file_exists($templatePath)) {
				$action = $_GET["template"] . "Prepare"; //Вызов метода для подготовки шаблона (например. protocolPrepare)
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

	function getEditableString($string)
	{
		return "<div contenteditable='true' style='display: inline;outline: none;'>" . $string . "</div>";
	}

	function getInputField($placeholder, $width, $fontSize, $fontWeight = 400) 
	{
		return '<input type="text" class="form-control printInputField" placeholder="' . $placeholder . '" style="width: ' . $width . 'px;font-size: ' . $fontSize . 'pt;font-weight: ' . $fontWeight . '">';
	}

	function replaceData($data, $fileContent)
	{
		foreach (Config::getValue("printValues") as $key => $value)
			$fileContent = str_replace($key, isset($data[$value]) ? (empty($data[$value]) ? $this->getEditableString("{НЕ ЗАПОЛНЕНО}") : $this->getEditableString($data[$value])) : $this->getEditableString("{НЕКОРРЕКТНОЕ ЗНАЧЕНИЕ}"), $fileContent);

		return $fileContent;
	}

	function protocolPrepare($fileContent, $id)
	{
		$data = Database::execute("SELECT * FROM `conscript` WHERE id = :id", ["id" => $id], "current")[0];
		$data["vkName"] = Helper::getVKNameById($data["vk"])["fullNameNotUnique"];
		$documents = Helper::getResultDocuments($id);

		$documentType = $documents[0]["documentType"];
		$article = array();
		$complaint = array();
		$objectData = array();
		$specialResult = array();
		$diagnosis = array();
		$anamnez = array();

		foreach ($documents as $document) {
			array_push($complaint, $document["complaint"]);
			array_push($objectData, $document["objectData"]);
			array_push($specialResult, $document["specialResult"]);
			array_push($diagnosis, $document["diagnosis"]);
			array_push($anamnez, $document["anamnez"]);
			array_push($article, $document["article"]);
			if ($documentType == "complaint")
				$data["anamnez"] .= "Не согласен с решением РПК.";

			if ($document["healthCategory"] > $finalCategory)
				$finalCategory = $document["healthCategory"];

			if($document["postPeriod"] > $postPeriod)
				$postPeriod = $document["postPeriod"];
		}

		$data["complaint"] = implode("<br>", $complaint);
		$data["objectData"] = implode("<br>", $objectData);
		$data["specialResult"] = implode("<br>", $specialResult);
		$data["diagnosis"] = implode("<br>", $diagnosis);
		$data["anamnez"] = implode("<br>", $anamnez);

		//РВК Назначение
		$rvkHealthCategory = mb_substr($data["healthCategory"], 0, 1);
		$rvkMedicalAppointment = mb_substr($data["healthCategory"], 1, 1);
		$data["rvkAppointment"] = "Категория годности<br>«" . $rvkHealthCategory . "» - " . Helper::getHealthCategoryNameByID($data["healthCategory"]) . ".<br>";
		switch ($rvkHealthCategory) {
			case 'А':
			case 'Б':
				$data["rvkAppointment"] .= "Статья 22<br>Призвать на военную службу." . (!empty($rvkMedicalAppointment) ? "<br>Показатель предназначения - " . $rvkMedicalAppointment : "");
				break;
			case 'В':
				$data["rvkAppointment"] .= "Статья 23 п.1 «а»<br>Освободить от призыва<br>на военную службу.<br>Зачислить в запас.";
				break;
			case 'Г':
				$data["rvkAppointment"] .= "Статья 24 п.1 «а»<br>Предоставить отсрочку от<br>призыва на военную службу.";
				break;
			case 'Д':
				$data["rvkAppointment"] .= "Статья 23 п.4<br>Освободить  от исполнения <br> воинской обязанности.<br>";
				break;
		}
		$data["rvkAppointment"] .= "<br>Статья – " . $data["rvkArticle"] . "<br>Протокол № " . $data["rvkProtocolNumber"] . " <br> от " . $data["rvkProtocolDate"] . "г.";
		//РВК Назначение

		//Назначение
		$healthCategory = mb_substr($finalCategory, 0, 1);
		$medicalAppointment = mb_substr($finalCategory, 1, 1);
		$data["appointment"] = "Статья " . implode(", ", $article) . ($healthCategory == "О" ? "" : "<br>Категория годности<br>«" . $healthCategory . "» - " . Helper::getHealthCategoryNameByID($finalCategory)) . (!empty($medicalAppointment) ? "<br>Показатель предназначения - " . $medicalAppointment : "");
		if ($healthCategory == "О")
			$data["appointment"] .= "<br>Подлежит обследованию.";
		//Назначение

		//Решение
		$data["result"] = "Решение призывной<br>комиссии<br>" . Helper::getVKNameById($data["vk"])["fullNameUnique"] . "<br>" . ($data["healthCategory"] == $finalCategory || $healthCategory == "А" && $rvkHealthCategory == "Б" || $healthCategory == "Б" && $rvkHealthCategory == "А" ? " утвердить. " : " отменить. ");
		if (!($data["healthCategory"] == $finalCategory || $healthCategory == "А" && $rvkHealthCategory == "Б" || $healthCategory == "Б" && $rvkHealthCategory == "А")) {
			switch ($healthCategory) {
				case 'А':
				case 'Б':
					$data["result"] .= "Принять решение в<br>соответствии со ст. 28<br>п.1 ФЗ «О воинской обязанности и военной службе» - призвать на военную службу. Предназначить в остальные воинские части Сухопутных войск.";
					break;
				case 'В':
					$data["result"] .= "Принять решение в<br>соответствии со ст. 28<br>п.1 ФЗ «О воинской обязанности и военной службе» - освободить от призыва на военную службу.";
					break;
				case 'Г':
					$data["result"] .= "Принять решение в<br>соответствии со ст. 24<br>п.1 «а» ФЗ «О воинской обязанности и военной службе» - предоставить<br>отсрочку от призыва на военную службу сроком на " . Config::getValue("postPeriod")[$postPeriod];
					break;
				case 'Д':
					$data["result"] .= "Принять решение в<br>соответствии со ст.28<br>п.1 ФЗ «О воинской обязанности и военной службе» - освободить от исполнения воинской обязанности.";
					break;
				case 'О':
					$data["result"] = "Направить<br>на медицинское<br>обследование<br>_____________________<br>___________________<br>явиться на<br>повторное освидетельствование<br>_____________________<br>___________________<br>";
					break;
			}
		}
		$data["result"] .= "<br>Протокол № " . $data["protocolNumber"] . " <br> от " . $data["protocolDate"] . "г.";
		if (!($data["healthCategory"] == $finalCategory || $healthCategory == "А" && $rvkHealthCategory == "Б" || $healthCategory == "Б" && $rvkHealthCategory == "А"))
			$data["result"] .= "<br>Служебное письмо<br>от " . $this->getInputField("дата", 80, "12", 700) . "г.<br>№ " . $this->getInputField("номер", 60, "12", 700);
		//Решение

		$fileContent = $this->replaceData($data, $fileContent);
		return $fileContent;
	}

	function letterPrepare($fileContent, $id)
	{
		$data = Database::execute("SELECT * FROM `conscript` WHERE id = :id", ["id" => $id], "current")[0];
		$data["vkName"] = Helper::getVKNameById($data["vk"])["fullNameNotUnique"];
		$documents = Helper::getResultDocuments($id);

		$article = array();
		$diagnosis = array();
		$reasonForCancel = array();

		foreach ($documents as $document) {
			array_push($diagnosis, $document["diagnosis"]);
			array_push($article, $document["article"]);

			if(!empty($document["reasonForCancel"]))
				array_push($reasonForCancel, $document["reasonForCancel"]);

			if ($document["healthCategory"] > $finalCategory)
				$finalCategory = $document["healthCategory"];

			if($document["postPeriod"] > $postPeriod)
				$postPeriod = $document["postPeriod"];
		}

		$healthCategory = mb_substr($finalCategory, 0, 1);

		$data["healthCategory"] = "«" . $healthCategory . "» - " . Helper::getHealthCategoryNameByID($finalCategory);

		$data["diagnosis"] = implode("<br>", $diagnosis);
		$data["reasonToChangeResult"] = count($reasonForCancel) > 0 ? "Причина отмены решения: " . implode("<br>", $reasonForCancel) : " ";
		$data["result"] = "Ранее вынесенное решение призывной комиссии " . Helper::getVKNameById($data["vk"])["fullNameUnique"] . " отменить.";
		$data["article"] = implode(", ", $article);
		$data["letterDate"] = Helper::convertDateToPrintFormat(date("d.m.Y"));
		$data["letterNumber"] = $this->getInputField("номер", 60, "12");

		$fileContent = $this->replaceData($data, $fileContent);
		return $fileContent;
	}

	function extractPrepare($fileContent, $id)
	{
		$data = Database::execute("SELECT * FROM `conscript` WHERE id = :id", ["id" => $id], "current")[0];
		$data["creator"] = Profile::$user["name"];
		$documents = Helper::getResultDocuments($id);
		$article = array();

		foreach ($documents as $document) {
			array_push($article, $document["article"]);

			if ($document["healthCategory"] > $finalCategory)
				$finalCategory = $document["healthCategory"];
		}

		$rvkHealthCategory = mb_substr($data["healthCategory"], 0, 1);
		$healthCategory = mb_substr($finalCategory, 0, 1);

		$data["appointment"] = "<div class='mb-3'>Решение призывной комиссии " . Helper::getVKNameById($data["vk"])["shortNameUnique"] . "</div>";
		$data["appointment"] .= "<div class='mb-3'>От " . Helper::convertDateToPrintFormat($data["rvkProtocolDate"]) . " № " . $data["rvkProtocolNumber"] . "</div>";
		$data["appointment"] .= "<div class='mb-3'>по гражданину <span id='name'></span> " . $data["birthDate"] . " г.р.</div>";

		$data["result"] = "<div class='mb-3'>";
		switch ($rvkHealthCategory) {
			case 'А':
			case 'Б':
				$data["result"] .= "о призыве на военную службу<br>";
				break;
			case 'В':
				$data["result"] .= "об освобождении от призыва на военную службу<br>";
				break;
			case 'Г':
				$data["result"] .= "о предоставлении отсрочки от призыва на военную службу<br>";
				break;
			case 'Д':
				$data["result"] .= "об освобождении от исполнения воинской обязанности<br>";
				break;
		}
		$data["result"] .= "</div>";
		$data["result"] .= "<div class='mb-3'><b><u>" . ($data["healthCategory"] == $finalCategory || $healthCategory == "А" && $rvkHealthCategory == "Б" || $healthCategory == "Б" && $rvkHealthCategory == "А" ? "УТВЕРДИТЬ" : "ОТМЕНИТЬ") . "</u></b></div>";
		$data["result"] .= "<div class='mb-3'>Признать " . ($healthCategory == "О" ? "Подлежит обследованию." : "ст. " . implode(", ", $article) . " «" . $healthCategory . "» - " . Helper::getHealthCategoryNameByID($finalCategory)) . "</div>";

		if ($data["healthCategory"] == $finalCategory || $healthCategory == "А" && $rvkHealthCategory == "Б" || $healthCategory == "Б" && $rvkHealthCategory == "А") {
			switch ($healthCategory) {
				case 'А':
				case 'Б':
					$data["result"] .= "<div class='mb-3'>Основание: п.1 ст. 22 ФЗ «О воинской обязанности и военной службе»</div>";
					break;
				case 'В':
					$data["result"] .= "<div class='mb-3'>Основание: ст. 23   п.1 «а» ФЗ «О воинской обязанности и военной службе»</div>";
					break;
				case 'Г':
					$data["result"] .= "<div class='mb-3'>Основание: ст. 24. п.1 «а» ФЗ «О воинской обязанности и военной службе»</div>";
					break;
				case 'Д':
					$data["result"] .= "<div class='mb-3'>Основание: ст. 23   п.4 ФЗ «О воинской обязанности и военной службе»</div>";
					break;
			}
		} else {
			switch ($healthCategory) {
				case 'А':
				case 'Б':
					$data["result"] .= "<div class='mb-3'>ст. 28 п.1 ФЗ «О воинской обязанности и военной службе» - <b>призвать на военную службу. Предназначить в остальные воинские части Сухопутных войск.</b></div>";
					break;
				case 'В':
					$data["result"] .= "<div class='mb-3'>ст. 28 п.1 ФЗ «О воинской обязанности и военной службе» - <b>освободить от призыва на военную службу.</b></div>";
					break;
				case 'Г':
					$data["result"] .= "<div class='mb-3'>ст. 28. п.1 ФЗ «О воинской обязанности и военной службе» - <b>предоставить отсрочку от призыва на военную службу.</b></div>";
					break;
				case 'Д':
					$data["result"] .= "<div class='mb-3'>ст. 28 п.1 ФЗ «О воинской обязанности и военной службе» - <b>освободить от исполнения воинской обязанности.</b></div>";
					break;
			}
		}
		$data["result"] .= "<div class='mb-3'>Протокол от " . Helper::convertDateToPrintFormat($data["protocolDate"]) . " № " . $data["protocolNumber"] . "</div>";

		$fileContent = $this->replaceData($data, $fileContent);
		return $fileContent;
	}

	function examinationPrepare($fileContent, $id)
	{
		$data = Database::execute("SELECT * FROM `documents` WHERE id = :id", ["id" => $id], "current")[0];
		$data["creator"] = Database::execute("SELECT name FROM staff WHERE id=:id", ["id" => $data["creatorID"]])[0]["name"];
		$data["documentDate"] = Helper::formatDateToView($data["documentDate"]);

		$conscript = Database::execute("SELECT * FROM `conscript` WHERE id = :id", ["id" => $data["conscriptID"]], "current")[0];

		$data["name"] = $conscript["name"];
		$data["birthDate"] = $conscript["birthDate"];
		$data["rvkArticle"] = $conscript["rvkArticle"];
		$data["healthCategory"] = "«" . $data["healthCategory"] . "» - " . Helper::getHealthCategoryNameByID($data["healthCategory"]);
		$data["reasonToChangeResult"] = empty($data["reasonForCancel"]) ? " " : "Причина изменения решения: " . $data["reasonForCancel"];

		$fileContent = $this->replaceData($data, $fileContent);

		return $fileContent;
	}

}