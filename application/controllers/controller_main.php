<?

class Controller_Main extends Controller
{
	
	function action_index()
	{	
		$data = null;
		if (Profile::isHavePermission("adjustment"))
			$data['adjustmentChartsInfo'] = $this->getAdjustmentChartInfo();

		if (Profile::$isAuth)
			$this->view->generateView('main_view.php', "Главная страница", $data);
		else
			$this->view->generateView('loginPage_view.php', "Авторизация");
	}

	function getAdjustmentChartInfo() {
		return [26, 12, 22, 15];
	}
	
}