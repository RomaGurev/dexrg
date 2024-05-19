<?
/*
Файл, подключаемый index.php и postHandler.php, для работы с базами данных
> содержит класс Database и методы исполнения запросов и инициализации баз данных.
*/


class Database
{

    //Конструктор экземпляра PDO, выполняет подключение и возвращает объект
    private static function pdo(): PDO
    {
        static $pdo;

        if (!$pdo) {
            // Подключение к БД
            $dsn = 'mysql:dbname=' . Config::getValue('database')['db_settings'] . ';host=' . $_SERVER['SERVER_ADDR'];
            $pdo = new PDO($dsn, Config::getValue('database')['db_user'], Config::getValue('database')['db_pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $pdo;
    }

    //Конструктор экземпляра PDO, выполняет подключение к выбранной базе и возвращает объект
    public static function pdoCurrentBase(): PDO
    {
        static $pdo;

        if (!$pdo) {
            // Подключение к БД
            $dsn = 'mysql:dbname=' . Profile::getSelectedBase() . ';host=' . $_SERVER['SERVER_ADDR'];
            $pdo = new PDO($dsn, Config::getValue('database')['db_user'], Config::getValue('database')['db_pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $pdo;
    }

    //Метод выполнения запроса, возвращает полученный в результат
    public static function execute($query, $dataArr = null, $dbname = "")
    {
        try {
            switch ($dbname) {
                case "current":
                    $sth = Database::pdoCurrentBase()->prepare($query);
                    $sth->execute($dataArr);
                    $array = $sth->fetchAll(PDO::FETCH_ASSOC);
                    $sth->closeCursor();
                    break;
                default:
                    $sth = Database::pdo()->prepare($query);
                    $sth->execute($dataArr);
                    $array = $sth->fetchAll(PDO::FETCH_ASSOC);
                    $sth->closeCursor();
                    break;
            }
        } catch (PDOException $exception) {
            echo "<div class='alert alert-danger'>Запрос: $query к базе данных $dbname<br>Ошибка: " . $exception->getMessage() . "</div>";
        }

        return $array;
    }

    //Метод инициализации новой базы данных
    public static function initializeNewDatabase($dbname)
    {
        $initDB = realpath($_SERVER['DOCUMENT_ROOT']) . "/application/database/initializedb.sql";

        try {
            $dsn = 'mysql:dbname=' . $dbname . ';host=' . $_SERVER['SERVER_ADDR'];
            $pdo = new PDO($dsn, Config::getValue('database')['db_user'], Config::getValue('database')['db_pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $quary = file_exists($initDB) ? file_get_contents($initDB) : null;
            $sth = $pdo->prepare($quary);
            $sth->execute();
            $sth->fetchAll(PDO::FETCH_ASSOC);
            $sth->closeCursor();
        } catch (PDOException $exception) {
            echo "<div class='alert alert-danger'>Инициализация базы данных $dbname запросом $quary<br>Ошибка: " . $exception->getMessage() . "</div>";
        }

        return "success";
    }

    //Метод, возвращающий список баз данных, исключающий служебный
    public static function getDatabasesList()
    {
        $databases = Database::execute("SHOW DATABASES");
        for ($i = 0; $i < count($databases); $i++) {
            if ($databases[$i]["Database"] != "information_schema" && $databases[$i]["Database"] != "mysql" && $databases[$i]["Database"] != "performance_schema" && $databases[$i]["Database"] != "vvk_settings") {
                $result[] = $databases[$i]["Database"];
                rsort($result);
            }

        }
        return $result;
    }

    public static function getCurrentBase()
    {
        $quaryResult = Database::execute("SELECT selectedDatabase FROM dbSettings");
        return $quaryResult[0]["selectedDatabase"];
    }

    public static function getLastArchiveBase()
    {
        $databases = Database::execute("SHOW DATABASES");
        for ($i = 0; $i < count($databases); $i++) {
            if ($databases[$i]["Database"] != "information_schema" && $databases[$i]["Database"] != "mysql" && $databases[$i]["Database"] != "performance_schema" && $databases[$i]["Database"] != "vvk_settings" && $databases[$i]["Database"] != static::getCurrentBase()) {
                $result[] = $databases[$i]["Database"];
            }
        }
        rsort($result);
        return $result[0];
    }

    public static function setCurrentBase($dbName)
    {
        Database::execute("UPDATE dbSettings SET selectedDatabase = '$dbName' WHERE id = 0");
    }

}
?>