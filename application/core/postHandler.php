<?
/*
Обработчик POST запросов
> содержит методы для обработки данных форм и формирования ответа на них.
*/
require_once '../database/database.php';
require_once '../core/config.php';
require_once '../core/profile.php';
require_once '../core/route.php';
require_once '../additions/conscriptBuilder.php';
require_once '../additions/documentBuilder.php';
require_once '../additions/helper.php';

Profile::authInit();
$pHandler = new postHandler();

//Обработка POST-запросов на странице, путем вызова метода $key с параметрами $val
if (isset($_POST)) {
    foreach ($_POST as $key => $val) {
        if (method_exists($pHandler, $key))
            echo $pHandler->$key($val);
        else
            echo "Обработчик POST запроса не найден.";
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
            //$current = explode("-", Database::getCurrentBase());
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
        Database::execute($query, $dataArr);
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
        Database::execute($query, $dataArr);
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
            $query = "INSERT INTO `patternList` (name, complaint, anamnez, objectData, specialResult, diagnosis, healthCategory, article,ownerID) VALUES (:name, :complaint, :anamnez, :objectData, :specialResult, :diagnosis, :healthCategory, :article, :ownerID);";
            $dataArr = [
                "name" => $param['patternName'],
                "complaint" => $param['complaintTextarea'],
                "anamnez" => $param['anamnezTextarea'],
                "objectData" => $param['objectDataTextarea'],
                "specialResult" => $param['specialResultTextarea'],
                "diagnosis" => $param['diagnosisTextarea'],
                "healthCategory" => $param['healthCategorySelect'],
                "article" => $param['articleInput'],
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

            $query = "UPDATE patternList SET name = :name, complaint = :complaint, anamnez = :anamnez, objectData = :objectData, specialResult = :specialResult, diagnosis = :diagnosis, healthCategory = :healthCategory, article = :article WHERE id = :id;";
            $dataArr = [
                "name" => $param['patternName'],
                "complaint" => $param['complaintTextarea'],
                "anamnez" => $param['anamnezTextarea'],
                "objectData" => $param['objectDataTextarea'],
                "specialResult" => $param['specialResultTextarea'],
                "diagnosis" => $param['diagnosisTextarea'],
                "healthCategory" => $param['healthCategorySelect'],
                "article" => $param['articleInput'],
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

    static function addConscript($param)
    {
        if ($param['fullName'] === "")
            return "Введите имя призывника";
        else {
            $duplicateCheck = Database::execute("SELECT * FROM `conscript` WHERE name=:name AND birthDate=:birthDate", ["name" => $param['fullName'], "birthDate" => Helper::formatDateToView($param['birthDate'])], "current");

            if (count($duplicateCheck) == 0) {
                $query = "INSERT INTO `conscript` (creatorID, creationDate, name, birthDate, rvkArticle, rvkDiagnosis, vk, healthCategory, adventPeriod) VALUES (:creatorID, :creationDate, :name, :birthDate, :rvkArticle, :rvkDiagnosis, :vk, :healthCategory, :adventPeriod);";
                $dataArr = [
                    "creatorID" => Profile::$user['id'],
                    "creationDate" => Helper::formatDateToView($param['creationDate']),
                    "name" => $param['fullName'],
                    "birthDate" => Helper::formatDateToView($param['birthDate']),
                    "rvkArticle" => $param['rvkArticle'],
                    "rvkDiagnosis" => $param['rvkDiagnosis'],
                    "vk" => $param['vk'],
                    "healthCategory" => $param['healthCategory'],
                    "adventPeriod" => $param['adventTime']
                ];
                Database::execute($query, $dataArr, "current");
                return "reloadPage";
            } else {
                return "<div>Призывник <a href='/?conscript=" . $duplicateCheck[0]["id"] . "' style='text-decoration: none;'> " . $param['fullName'] . " [" . Helper::formatDateToView($param['birthDate']) . "]</a> уже существует.</div>";
            }
        }
    }

    static function editConscript($param)
    {
        if ($param['fullName'] === "")
            return "Введите имя призывника";
        else {
            $query = "UPDATE `conscript` SET creatorID = :creatorID, creationDate = :creationDate, name = :name, birthDate = :birthDate, rvkArticle = :rvkArticle, rvkDiagnosis = :rvkDiagnosis, vk = :vk, healthCategory = :healthCategory, adventPeriod = :adventPeriod WHERE id = :id;";
            $dataArr = [
                "id" => $param['id'],
                "creatorID" => Profile::$user['id'],
                "creationDate" => Helper::formatDateToView($param['creationDate']),
                "name" => $param['fullName'],
                "birthDate" => Helper::formatDateToView($param['birthDate']),
                "rvkArticle" => $param['rvkArticle'],
                "rvkDiagnosis" => $param['rvkDiagnosis'],
                "vk" => $param['vk'],
                "healthCategory" => $param['healthCategory'],
                "adventPeriod" => $param['adventTime']
            ];

            Database::execute($query, $dataArr, "current");
            return "reloadPage";
        }
    }

    static function deleteConscript($param)
    {
        if ($param['conscriptID'] === "")
            return "Ошибка удаления призывника. ID не найден.";
        else {
            Database::execute("DELETE FROM `documents` WHERE conscriptID=:id", ["id" => $param['conscriptID']], "current");
            $query = "DELETE FROM `conscript` WHERE id=:id";
            $dataArr = [
                "id" => $param['conscriptID'],
            ];
            Database::execute($query, $dataArr, "current");
            return "reloadPage";
        }
    }
    //<-------------------------------------Страницы шаблонов (conscription)------------------------------------->//

    //<-------------------------------------Поиск на сайте------------------------------------->//

    static function searchConscript($param)
    {
        $valueLength = mb_strlen($param['value'], "UTF-8");

        if ($valueLength >= 3) {

            switch ($param['type']) {
                case 'vk':
                    $keys = array_column(Helper::getVKNames(), 'name');
                    $keys = array_map('mb_strtolower', $keys);
                    $matches = preg_grep('#((?i)' . trim(mb_strtolower($param['value'])) . '(\W*))#i', $keys);
                    $param['value'] = key($matches);
                    break;
                case 'article':
                    echo "<div id='resizeDiv' class='lead'>Не реализовано.</div>";
                    break;
            }

            $query = "SELECT * FROM `conscript` WHERE " . $param['type'] . " LIKE '%" . $param['value'] . "%' ORDER BY id DESC LIMIT 5";
            $ans = Database::execute($query, null, "current");

            if (count($ans) == 0)
                return "<div id='resizeDiv' class='lead'>Учетная карта не найдена." . (Profile::isHavePermission("canAdd") ? "Необходимо <a style='text-decoration: none;' href='/conscription/editor?back='>зарегистрировать</a> призывника.</div>" : "</div>");
            else {
                $result = "<div id='resizeDiv' class='d-grid gap-2'>";
                foreach ($ans as $conscript)
                    $result .= ConscriptBuilder::getConscriptCard($conscript, $param['showSelect']);
                $result .= "</div>";

                return $result;
            }

        } else {
            if ($valueLength == 0)
                return "";
            return "<div id='resizeDiv' class='lead'>Введите больше 2 символов.</div>";
        }
    }

    static function searchDocument($param)
    {
        $valueLength = mb_strlen($param['value'], "UTF-8");

        switch ($param["type"]) {
            case "name":
            case "rvkArticle":
            case "birthDate":
            case "creationDate":
                if($valueLength >= 3)
                    $additionQuery = "WHERE " . $param["type"] . " LIKE '%" . $param['value'] . "%'";
                elseif ($param["type"] == "rvkArticle" && $valueLength > 0) 
                    $additionQuery = "WHERE " . $param["type"] . " LIKE '" . $param['value'] . "%'";
                else
                    $additionQuery = null;

                $conscriptsWithDocuments = Helper::getConscriptsWithDocuments($param["documentType"], Profile::isHavePermission("viewForAll") ? null : Profile::$user["id"], $additionQuery);
                break;
            
            default:
                if($valueLength > 0)
                    $additionQuery = "AND " . $param["type"] . " LIKE '%" . $param['value'] . "%'";
                else 
                    $additionQuery = null;

                $conscriptsWithDocuments = Helper::getConscriptsWithDocuments($param["documentType"], Profile::isHavePermission("viewForAll") || $param["type"] == "id" ? null : Profile::$user["id"], null, $additionQuery);
                break;
        }

        $result = "<div id='resizeDiv' class='d-grid gap-2'>";

        if($valueLength < 3 && $valueLength != 0 && $param["type"] != "rvkArticle" && $param["type"] != "id" && $param["type"] != "article")
            $result .= "<div class='lead'>Введите больше 2 символов.</div>";

        if(count($conscriptsWithDocuments) > 0) {
            foreach ($conscriptsWithDocuments as $conscript)
                $result .= DocumentBuilder::getConscriptWithDocumentsCard($conscript);
        } else {
            $result .= "<div class='lead'>Документ не найден.</div>";
        }
        $result .= "</div>";

        return $result;
    }


    static function getConscriptInfoForModal($param)
    {
        if ($param['conscriptID'] === "")
            return "Ошибка загрузки. Призывник по ID не найден.";
        else {
            $conscript = Database::execute("SELECT * FROM `conscript` WHERE id=:id", ["id" => $param['conscriptID']], "current");

            if (count($conscript) > 0) {
                $documentsInfo = Database::execute("SELECT * FROM `documents` WHERE conscriptID=:id", ["id" => $param['conscriptID']], "current");

                return ConscriptBuilder::getConscriptModalInfo($conscript[0], $documentsInfo);
            } else {
                return "Ошибка. Призывник по ID " . $param['conscriptID'] . " не найден.";
            }
        }
    }
    //<-------------------------------------Поиск на сайте------------------------------------->//

    //<-------------------------------------Страница добавления/редактирования документов------------------------------------->//

    static function getPatternByID($param)
    {
        $pattern = Database::execute("SELECT * FROM `patternList` WHERE id=:id", ["id" => $param['patternID']])[0];

        if (count($pattern) > 0) {
            return json_encode($pattern);
        } else {
            return "Ошибка. Шаблон " . $param['patternID'] . " не найден.";
        }
    }

    static function addDocument($param)
    {
        $query = "INSERT INTO `documents` (conscriptID, article, healthCategory, creatorID, complaint, anamnez, objectData, specialResult, diagnosis, documentDate, documentType) VALUES (:conscriptID, :article, :healthCategory, :creatorID, :complaint, :anamnez, :objectData, :specialResult, :diagnosis, :documentDate, :documentType);";
        $dataArr = [
            "conscriptID" => $param['conscriptID'],
            "article" => $param['articleInput'],
            "healthCategory" => $param['healthCategorySelect'],
            "creatorID" => Profile::$user['id'],
            "complaint" => $param['complaintTextarea'],
            "anamnez" => $param['anamnezTextarea'],
            "objectData" => $param['objectDataTextarea'],
            "specialResult" => $param['specialResultTextarea'],
            "diagnosis" => $param['diagnosisTextarea'],
            "documentDate" => Helper::formatDateToView(date("Y-m-d")),
            "documentType" => $param['documentType']
        ];
        Database::execute($query, $dataArr, "current");
        return "reloadPage";
    }

    static function editDocument($param)
    {
        $query = "UPDATE `documents` SET  article=:article, healthCategory=:healthCategory, complaint=:complaint, anamnez=:anamnez, objectData=:objectData, specialResult=:specialResult, diagnosis=:diagnosis, documentDate=:documentDate WHERE id = :id;";
        $dataArr = [
            "article" => $param['articleInput'],
            "healthCategory" => $param['healthCategorySelect'],
            "complaint" => $param['complaintTextarea'],
            "anamnez" => $param['anamnezTextarea'],
            "objectData" => $param['objectDataTextarea'],
            "specialResult" => $param['specialResultTextarea'],
            "diagnosis" => $param['diagnosisTextarea'],
            "documentDate" => Helper::formatDateToView(date("Y-m-d")),
            "id" => $param['documentID']
        ];
        Database::execute($query, $dataArr, "current");
        return "reloadPage";
    }

    static function deleteDocument($param)
    {
        if ($param['documentID'] === "")
            return "Ошибка удаления документа. ID не найден.";
        else {
            $query = "DELETE FROM `documents` WHERE id=:id";
            $dataArr = [
                "id" => $param['documentID'],
            ];
            Database::execute($query, $dataArr, "current");
            return "reloadPage";
        }
    }

    //<-------------------------------------Страница добавления/редактирования документов------------------------------------->//
}