<?
/*
Обработчик POST запросов
> содержит методы для обработки данных форм и формирования ответа на них.
*/
require_once '../database/database.php';
require_once '../core/config.php';
require_once '../core/profile.php';
require_once '../core/route.php';

Profile::authInit();
$pHandler = new postHandler();

//Обработка POST-запросов на странице, путем вызова метода $key с параметрами $val
if (isset($_POST)) {
    foreach ($_POST as $key => $val) {
        if (method_exists($pHandler, $key))
            echo $pHandler->$key($val);
    }
}

//Класс, содержащий методы для обработки POST запросов
class postHandler
{

    //<-------------------------------------Панель администратора------------------------------------->//

    //Метод добавления пользователя, вызывается посредством POST-запроса
    function addAccount($param)
    {
        if ($param['name'] === null || trim($param['name']) === '' || $param['position'] === "")
            return "Некорректные данные";
        else {
            $query = "INSERT INTO `staff` (name, position, isEmployed) VALUES (:name, :position, :isEmployed);";
            $dataArr = [
                "name" => $param['name'],
                "position" => $param['position'],
                "isEmployed" => 1
            ];
            Database::execute($query, $dataArr);
            return "success";
        }
    }

    //Метод создания базы данных на новый призыв
    function createBase($param)
    {
        $dbname = $param['baseName'];

        if ($dbname == null)
            return "Ошибка инициализации данных.";
        elseif (in_array($dbname, Database::getDatabasesList())) {
            return "База данных $dbname уже существует.";
        } else {
            $query = 'CREATE DATABASE `' . $dbname . '`;';
            Database::execute($query);
            return Database::initializeNewDatabase($dbname);
        }

    }

    //Метод выбора рабочей базы данных
    function selectCurrentBase($param)
    {
        $dbname = $param['baseName'];

        if ($dbname == null && $dbname == "")
            return "Ошибка выбора базы.";
        elseif (!in_array($dbname, Database::getDatabasesList())) {
            return "Базы данных $dbname не существует.";
        } else {
            Database::setCurrentBase($dbname);
            $current = explode("-", Database::getCurrentBase());
            return "success";
        }

    }

    //Метод изменения поля IsEmployed в таблице Управления аккаунтами
    function changeIsEmployed($param)
    {
        $userID = $param["userID"];
        $currentValue = Database::execute("SELECT isEmployed FROM `staff` WHERE id = :id;", ["id" => $userID])[0]["isEmployed"];

        $query = "UPDATE staff SET isEmployed = :isEmployed WHERE id = :id;";
        $dataArr = [
            "isEmployed" => $currentValue == 1 ? 0 : 1,
            "id" => $userID
        ];
        $ans = Database::execute($query, $dataArr);
        return "<div class='alert alert-success mt-2'>Параметры учетной записи успешно сохранены.</div>";
    }

    function changePosition($param)
    {
        $userID = $param["userID"];
        $userPosition = $param["userPosition"];

        $query = "UPDATE staff SET position = :position WHERE id = :id;";
        $dataArr = [
            "position" => $userPosition,
            "id" => $userID
        ];
        $ans = Database::execute($query, $dataArr);
        return "<div class='alert alert-success mt-2'>Параметры учетной записи успешно сохранены.</div>";
    }
    //<-------------------------------------Панель администратора------------------------------------->//

    //<-------------------------------------Аккаунты пользователей------------------------------------->//

    //Метод входа в аккаунт
    static function authUser($param)
    {
        if ($param['position'] === "")
            return "Выберите специальность для авторизации";
        else {
            $query = "SELECT id, name FROM `staff` WHERE position=:position AND isEmployed=:isEmployed";
            $dataArr = [
                "position" => $param['position'],
                "isEmployed" => 1
            ];
            $ans = Database::execute($query, $dataArr);

            if (count($ans) > 0) {
                $SPEC_DATA = $ans[0];
                setcookie("name", $SPEC_DATA["name"], time() + 50000, '/');
                $_SESSION['id'] = $SPEC_DATA['id'];
                Profile::authInit();
                return "reloadPage";
            } else {
                return "Специалист не найден";
            }

        }
    }

    //Метод выхода из аккаунта, вызывается посредством POST-запроса
    static function outUser($param)
    {
        echo Profile::logOut();
    }

    //<-------------------------------------Аккаунты пользователей------------------------------------->//


    //<-------------------------------------Страницы шаблонов (pattern)------------------------------------->//

    //Метод добавления шаблона
    static function addPattern($param)
    {
        if ($param['patternName'] === "")
            return "Введите название шаблона";
        else {
            $query = "INSERT INTO `patternList` (name, complaint, anamnez, objectData, specialResult, diagnosis, ownerID) VALUES (:name, :complaint, :anamnez, :objectData, :specialResult, :diagnosis, :ownerID);";
            $dataArr = [
                "name" => $param['patternName'],
                "complaint" => $param['complaintTextarea'],
                "anamnez" => $param['anamnezTextarea'],
                "objectData" => $param['objectDataTextarea'],
                "specialResult" => $param['specialResultTextarea'],
                "diagnosis" => $param['diagnosisTextarea'],
                "ownerID" => Profile::$user['id']
            ];
            Database::execute($query, $dataArr);
            return "reloadPage";
        }
    }

    //Метод редактирования шаблона
    static function editPattern($param)
    {
        if ($param['patternName'] === "")
            return "Введите название шаблона";
        else {
            
            $query = "UPDATE patternList SET name = :name, complaint = :complaint, anamnez = :anamnez, objectData = :objectData, specialResult = :specialResult, diagnosis = :diagnosis WHERE id = :id;";
            $dataArr = [
                "name" => $param['patternName'],
                "complaint" => $param['complaintTextarea'],
                "anamnez" => $param['anamnezTextarea'],
                "objectData" => $param['objectDataTextarea'],
                "specialResult" => $param['specialResultTextarea'],
                "diagnosis" => $param['diagnosisTextarea'],
                "id" => $param['patternID']
            ];
            
            Database::execute($query, $dataArr);
            return "reloadPage";
        }
    }

    //Метод удаления шаблона
    static function deletePattern($param)
    {
        if ($param['patternID'] === "")
            return "Ошибка удаления шаблона. ID не найден.";
        else {
            $query = "DELETE FROM `patternList` WHERE id=:id AND ownerID=:ownerID";
            $dataArr = [
                "id" => $param['patternID'],
                "ownerID" => Profile::$user['id']
            ];
            Database::execute($query, $dataArr);
            return "reloadPage";
        }
    }

    //<-------------------------------------Страницы шаблонов (pattern)------------------------------------->//

    //<-------------------------------------Страницы добавления призывников (conscription)------------------------------------->//

    static function addConscription($param) 
    {
        if ($param['fullName'] === "")
            return "Введите имя призывника";
        else {
            $query = "INSERT INTO `conscript` (documentNumber, ownerID, creationDate, name, birthDate, rvkArticle, article, documentType, vk, healtCategory, adventPeriod, diagnosis, patternID) VALUES (:documentNumber, :ownerID, :creationDate, :name, :birthDate, :rvkArticle, :article, :documentType, :vk, :healtCategory, :adventPeriod, :diagnosis, :patternID);";
            $dataArr = [
                "documentNumber" => $param['docNumber'],
                "ownerID" => Profile::$user['id'],
                "creationDate" => $param['creationDate'],
                "name" => $param['fullName'],
                "birthDate" => $param['birthDate'],
                "rvkArticle" => $param['rvkArticle'],
                "article" => $param['article'],
                "documentType" => $param['documentType'],
                "vk" => $param['vk'],
                "healtCategory" => $param['healtCategory'],
                "adventPeriod" => $param['adventTime'],
                "diagnosis" => $param['diagnosisTextarea'],
                "patternID" => $param['pattern']
            ];

            Database::execute($query, $dataArr, "current");
            return "reloadPage";
        }
    }

    static function editConscription($param) 
    {
        if ($param['fullName'] === "")
            return "Введите имя призывника";
        else {
            $query = "UPDATE `conscript` SET documentNumber = :documentNumber, ownerID = :ownerID, creationDate = :creationDate, name = :name, birthDate = :birthDate, rvkArticle = :rvkArticle, article = :article, documentType = :documentType, vk = :vk, healtCategory = :healtCategory, adventPeriod = :adventPeriod, diagnosis = :diagnosis, patternID = :patternID WHERE id = :id;";
            $dataArr = [
                "id" => $param['id'],
                "documentNumber" => $param['docNumber'],
                "ownerID" => Profile::$user['id'],
                "creationDate" => $param['creationDate'],
                "name" => $param['fullName'],
                "birthDate" => $param['birthDate'],
                "rvkArticle" => $param['rvkArticle'],
                "article" => $param['article'],
                "documentType" => $param['documentType'],
                "vk" => $param['vk'],
                "healtCategory" => $param['healtCategory'],
                "adventPeriod" => $param['adventTime'],
                "diagnosis" => $param['diagnosisTextarea'],
                "patternID" => $param['pattern']
            ];

            Database::execute($query, $dataArr, "current");
            return "reloadPage";
        }
    }

    static function deleteConscription($param)
    {
        if ($param['adjustmentID'] === "")
            return "Ошибка удаления призывника. ID не найден.";
        else {
            $query = "DELETE FROM `conscript` WHERE id=:id AND ownerID=:ownerID";
            $dataArr = [
                "id" => $param['adjustmentID'],
                "ownerID" => Profile::$user['id']
            ];
            Database::execute($query, $dataArr, "current");
            return "reloadPage";
        }
    }

    //<-------------------------------------Страницы шаблонов (conscription)------------------------------------->//

}