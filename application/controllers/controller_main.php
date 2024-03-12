<?

/*
	Класс Statistic:
		total - количество дел в отработке у данного врача
		controlArrived - прибыло на контроль
		controlNotArrived - не прибыло на контроль
		approved - утверждено
		inProcess - отработка
		returns - возвраты
		healthCategories - категории годности
*/

class Controller_Main extends Controller
{

	function action_index()
	{	
		if (Profile::isHavePermission("adjustment"))
			$data['adjustmentChartsInfo'] = $this->getAdjustmentChartInfo();

		if (Profile::$isAuth)
			$this->view->generateView('main_view.php', "Главная страница", $data);
		else
			$this->view->generateView('loginPage_view.php', "Авторизация");
	}

	function getAdjustmentChartInfo() {
		// Создание экземпляра класса Statistic
		// Запрос к БД с Count'ами для получения полей объекта Statistics
		// Заполнение полей объекта Statistic
		// Return объект Statistic
		return [2, 22, 222];
	}
	
}