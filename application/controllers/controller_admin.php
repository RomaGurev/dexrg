<?

class Controller_Admin extends Controller
{

	function action_index()
	{
		if (Profile::isHavePermission("admin")) {
			$data['userAccounts'] = $this->getUserAccounts();
			$this->view->generateView('admin_view.php', "Панель администратора", $data);
		} else
			$this->view->failAccess();
	}

	function getUserAccounts()
	{
		return Database::execute("SELECT * FROM `staff`");
	}

}