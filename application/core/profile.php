<?
/*
Файл, подключаемый index.php для работы учетных записей
> содержит класс Profile и методы авторизации и запроса имеющихся у пользователя разрешений(из config.json)
*/


class Profile
{
    public static $user = [
        "id" => -1,
        "position" => -1,
        "name" => "",
        "permissions" => [],
        "selectedBase" => ""
    ];

    public static $isAuth = false;

    public static function authInit()
    {
        session_start();
        static::$isAuth = static::login();

        if(isset($_GET["archive"]))
            $_SESSION['archiveMode'] = true;

        if($_SESSION['archiveMode']) {
            $_SESSION['selectedBase'] = $_SESSION['selectedBase'] ?? Database::getLastArchiveBase();
        }

        if (isset($_SESSION['id'])) {
            static::$user["id"] = $_SESSION['id'];

            if (isset($_SESSION['position']) && isset($_SESSION['name']) && isset($_SESSION['permissions']) && isset($_SESSION['selectedBase'])) {
                static::$user["position"] = $_SESSION['position'];
                static::$user["name"] = $_SESSION['name'];
                static::$user["permissions"] = $_SESSION['permissions'];
                static::$user["selectedBase"] = $_SESSION['selectedBase'];
            } else {
                $quary = "SELECT name, position FROM `staff` WHERE id=:id";
                $dataArr = ["id" => $_SESSION['id']];
                $ans = Database::execute($quary, $dataArr);
                static::$user["position"] = $ans[0]["position"];
                static::$user["name"] = $ans[0]["name"];
                static::$user["permissions"] = Config::getValue("permissionSet")[Config::getValue("userType")[static::$user["position"]][1]];
                static::$user["selectedBase"] = $_SESSION['selectedBase'] ?? Database::getCurrentBase();

                $_SESSION['position'] = static::$user["position"];
                $_SESSION['name'] = static::$user["name"];
                $_SESSION['permissions'] = static::$user["permissions"];
                $_SESSION['selectedBase'] = static::$user["selectedBase"];
            }
        }
    }

    public static function isHavePermission($permission)
    {
        return in_array($permission, static::$user["permissions"]);
    }

    public static function logOut()
    {
        setcookie("name", "", time() - 360000, '/');
        $_SESSION['id'] = "";
        setcookie("PHPSESSID", "", time() - 360000, '/');


        $_SESSION['selectedBase'] = $_SESSION['selectedBase'] ?? Database::getLastArchiveBase();

        if($_SESSION['archiveMode'])
            return "reloadPageArchive";
        else
            return "reloadPage";
    }

    public static function isAdmin()
    {
        if (static::$isAuth)
            return static::$user["id"] == 0;
        else
            return false;
    }

    public static function getSelectedBase()
    {
        if(isset($_SESSION['selectedBase']))
            return $_SESSION['selectedBase'];

        return Database::getCurrentBase();
    }

    public static function isArchiveMode() 
    {
        return static::getSelectedBase() != Database::getCurrentBase();
    }

    private static function login()
    {
        if (isset($_SESSION['id'])) //если сесcия есть   
        {
            if (isset($_COOKIE['name'])) //если cookie есть, обновляется время их жизни и возвращается true      
            {
                setcookie("name", $_COOKIE['name'], time() + 50000, '/');
                return true;
            } else {
                $quary = "SELECT name FROM `staff` WHERE id=:id AND isEmployed=:isEmployed";
                $dataArr = [
                    "id" => $_SESSION['id'],
                    "isEmployed" => 1
                ];
                $ans = Database::execute($quary, $dataArr);

                if (count($ans) > 0) {
                    $SPEC_DATA = $ans[0];
                    setcookie("name", $SPEC_DATA["name"], time() + 50000, '/');
                    return true;
                } else {
                    return false;
                }
            }
        } else //если сессии нет, проверяется существование cookie. Если они существуют, проверяется их валидность по базе данных     
        {
            if (isset($_COOKIE['name'])) //если куки существуют      
            {
                $quary = "SELECT * FROM `staff` WHERE name=:name";
                $dataArr = [
                    "name" => $_COOKIE['name']
                ];
                $ans = Database::execute($quary, $dataArr);
                $SPEC_DATA = $ans[0];

                if (count($ans) > 0 && $SPEC_DATA['name'] == $_COOKIE['name']) //если имя в базе соответсвует cookie           
                {
                    $_SESSION['id'] = $SPEC_DATA['id']; //записываем в сесcию id              
                    return true;
                } else //если данные из cookie не подошли, эти куки удаляются             
                {
                    setcookie("name", "", time() - 360000, '/');
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}