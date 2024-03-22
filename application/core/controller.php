<?

class Controller
{
	public $view;

	//Стандартный конструктор, определяет View для каждого контроллера, View внутри контроллера вызывается для вывода страницы.
	function __construct()
	{
		$this->view = new View();
	}

	//Стандартный action, переопределяется внутри контроллера и вызывается по умолчанию.
	function action_index()
	{
		$this->view->failAccess();
	}
}