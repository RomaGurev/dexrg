<?

class Controller_Pattern extends Controller
{

	//Функция отображения обычной страницы с проверкой наличия доступа
	function action_index()
	{
		if (Profile::isHavePermission("pattern")) {
			$data['userPatternList'] = $this->getUserPatternList();
			$this->view->generateView('pattern/pattern_view.php', "Шаблоны", $data);
		} else
			$this->view->failAccess();
	}

	//Функция отображения страницы добавления шаблона с проверкой наличия доступа
	function action_editor()
	{
		if (Profile::isHavePermission("pattern")) {
			if(isset($_GET["id"])) {
				$data['currentPattern'] = $this->getUserPatternByID($_GET["id"]);

				if($data['currentPattern'] != null) {
					if($data['currentPattern'][0]['ownerID'] == Profile::$user['id'] || Profile::isHavePermission("viewForAll"))
						$this->view->generateView('pattern/patternEditor_view.php', "Редактирование шаблона", $data);
					else
						$this->view->errorPage('Вы не являетесь владельцев шаблона.');
				}
				else
					$this->view->errorPage('Идентификатор шаблона не найден.');
			} else {
				$this->view->generateView('pattern/patternEditor_view.php', "Добавление шаблона");
			}
		}
		else
			$this->view->failAccess();
	}

	//Функция для получения списка шаблонов пользователя
	function getUserPatternList()
	{
		$quary = "SELECT * FROM `patternList`";
		if(!Profile::isHavePermission("viewForAll")) {
			$quary .= " WHERE ownerID=:ownerID";
			$dataArr = [
				"ownerID" => Profile::$user['id']
			];
		}
		$quary .= " ORDER BY ID desc;";

		return Database::execute($quary, $dataArr);
	}

	//Функция для получения шаблона по ID
	function getUserPatternByID($id)
	{
		$quary = "SELECT * FROM `patternList` WHERE id=:id;";
		$dataArr = [
			"id" => $id
		];
		return Database::execute($quary, $dataArr);
	}
}