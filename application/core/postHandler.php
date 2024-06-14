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

    //Метод изменения имени
    static function changeUserName($param)
    {
        if ($param['name'] == "" || empty($param['name']))
            return "Ошибка изменения имени.";
        else {
            $query = "UPDATE staff SET name=:name WHERE id=:id";
            $dataArr = [
                "name" => $param['name'],
                "id" => Profile::$user["id"]
            ];
            Database::execute($query, $dataArr);
            $_SESSION['name'] = $param['name'];
            return true;
        }
    }

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

    //Выбор базы данных для пользователя (вход/выход режим архива)
    static function selectUserDatabase($param)
    {
        if ($param['database'] == "")
            return "Ошибка выбора базы данных.";
        else {
            $_SESSION['selectedBase'] = $param['database'];
            return "reloadPage";
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
            $query = "INSERT INTO `patternList` (name, complaint, anamnez, objectData, specialResult, diagnosis, healthCategory, article, reasonForCancel, ownerID) VALUES (:name, :complaint, :anamnez, :objectData, :specialResult, :diagnosis, :healthCategory, :article, :reasonForCancel, :ownerID);";
            $dataArr = [
                "name" => $param['patternName'],
                "complaint" => $param['complaintTextarea'],
                "anamnez" => $param['anamnezTextarea'],
                "objectData" => $param['objectDataTextarea'],
                "specialResult" => $param['specialResultTextarea'],
                "diagnosis" => $param['diagnosisTextarea'],
                "healthCategory" => $param['healthCategorySelect'],
                "article" => $param['articleInput'],
                "reasonForCancel" => $param['reasonForCancelTextarea'],
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

            $query = "UPDATE patternList SET name = :name, complaint = :complaint, anamnez = :anamnez, objectData = :objectData, specialResult = :specialResult, diagnosis = :diagnosis, healthCategory = :healthCategory, reasonForCancel = :reasonForCancel, article = :article WHERE id = :id;";
            $dataArr = [
                "name" => $param['patternName'],
                "complaint" => $param['complaintTextarea'],
                "anamnez" => $param['anamnezTextarea'],
                "objectData" => $param['objectDataTextarea'],
                "specialResult" => $param['specialResultTextarea'],
                "diagnosis" => $param['diagnosisTextarea'],
                "healthCategory" => $param['healthCategorySelect'],
                "article" => $param['articleInput'],
                "reasonForCancel" => $param['reasonForCancelTextarea'],
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
                $query = "INSERT INTO `conscript` (creatorID, creationDate, name, birthDate, rvkArticle, rvkDiagnosis, vk, healthCategory, adventPeriod, postPeriod, rvkProtocolDate, rvkProtocolNumber) VALUES (:creatorID, :creationDate, :name, :birthDate, :rvkArticle, :rvkDiagnosis, :vk, :healthCategory, :adventPeriod, :postPeriod, :rvkProtocolDate, :rvkProtocolNumber);";
                $dataArr = [
                    "creatorID" => Profile::$user['id'],
                    "creationDate" => Helper::formatDateToView($param['creationDate']),
                    "name" => preg_replace('/\s\s+/', ' ', $param['fullName']),
                    "birthDate" => Helper::formatDateToView($param['birthDate']),
                    "rvkArticle" => $param['rvkArticle'],
                    "rvkDiagnosis" => $param['rvkDiagnosis'],
                    "vk" => $param['vk'],
                    "healthCategory" => $param['healthCategory'],
                    "adventPeriod" => $param['adventTime'],
                    "postPeriod" => $param['postPeriodSelect'],
                    "rvkProtocolDate" => Helper::formatDateToView($param['rvkProtocolDate']),
                    "rvkProtocolNumber" => $param['rvkProtocolNumber']
                ];
                Database::execute($query, $dataArr, "current");

                return Database::pdoCurrentBase()->lastInsertId();
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
            $query = "UPDATE `conscript` SET creatorID = :creatorID, creationDate = :creationDate, name = :name, birthDate = :birthDate, rvkArticle = :rvkArticle, rvkDiagnosis = :rvkDiagnosis, vk = :vk, healthCategory = :healthCategory, adventPeriod = :adventPeriod, postPeriod = :postPeriod, rvkProtocolDate = :rvkProtocolDate, rvkProtocolNumber = :rvkProtocolNumber WHERE id = :id;";
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
                "adventPeriod" => $param['adventTime'],
                "postPeriod" => $param['postPeriodSelect'],
                "rvkProtocolDate" => Helper::formatDateToView($param['rvkProtocolDate']),
                "rvkProtocolNumber" => $param['rvkProtocolNumber']
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

    static function getDocumentDiagnosisByConscriptID($param)
    {
        $resultDocuments = Helper::getResultDocuments($param['conscriptID']);
        $diagnosis = $resultDocuments[0]["diagnosis"];

        if (mb_strlen($diagnosis) > 0) {
            return $diagnosis;
        }

        return "";
    }
    //<-------------------------------------Страницы шаблонов (conscription)------------------------------------->//

    //<-------------------------------------Поиск на сайте------------------------------------->//

    static function searchConscript($param)
    {
        $valueLength = mb_strlen($param['value'], "UTF-8");

        if ($valueLength >= 3) {

            switch ($param['type']) {
                case 'vk':
                    $keys = array_column(Database::execute("SELECT id, name FROM vkList"), 'name');
                    $keys = array_map('mb_strtolower', $keys);
                    $matches = preg_grep('#((?i)' . trim(mb_strtolower($param['value'])) . '(\W*))#i', $keys);
                    $param['value'] = empty(key($matches)) ? "'RGNSK'" : key($matches) + 1;

                    $query = "SELECT * FROM `conscript` WHERE `vk`= " . $param['value'] . " ORDER BY id DESC LIMIT 5";
                    break;
                case 'article':
                    $tr = Database::execute("SELECT conscriptID FROM documents WHERE article=:article", ["article" => $param['value']], "current");

                    $conscripts = array();
                    foreach ($tr as $value)
                        array_push($conscripts, $value["conscriptID"]);

                    $query = "SELECT * FROM `conscript` WHERE id REGEXP '" . (count($conscripts) > 0 ? implode("|", $conscripts) : "RGNSK") . "' ORDER BY id DESC LIMIT 5";
                    break;
                default:
                    $query = "SELECT * FROM `conscript` WHERE " . $param['type'] . " LIKE '" . $param['value'] . "%' ORDER BY id DESC LIMIT 5";
                    break;
            }
            $ans = Database::execute($query, null, "current");

            if (count($ans) == 0)
                return "<div id='resizeDiv' class='lead'>Учетная карта не найдена." . (Profile::isHavePermission("canAdd") && !Profile::isArchiveMode() ? " Необходимо <a style='text-decoration: none;' href='/conscription/editor?back='>зарегистрировать</a> призывника.</div>" : "</div>");
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
                if ($valueLength >= 3)
                    $additionQuery = "AND " . $param["type"] . " LIKE '%" . $param['value'] . "%'";
                elseif ($param["type"] == "rvkArticle" && $valueLength > 0)
                    $additionQuery = "AND " . $param["type"] . " LIKE '" . $param['value'] . "%'";
                else
                    $additionQuery = null;

                $conscriptsWithDocuments = Helper::getConscriptsWithDocuments($param["documentType"], $param["inProcess"], Profile::isHavePermission("viewForAll") ? null : Profile::$user["id"], $additionQuery);
                break;

            case "healthCategory":
                if ($valueLength > 0)
                    $additionQuery = "AND " . $param["type"] . " LIKE '" . $param['value'] . "%'";
                else
                    $additionQuery = null;
                $conscriptsWithDocuments = Helper::getConscriptsWithDocuments($param["documentType"], $param["inProcess"], Profile::isHavePermission("viewForAll") ? null : Profile::$user["id"], null, $additionQuery);
                break;

            case "healthCategoryRVK":
                if ($valueLength > 0)
                    $additionQuery = "AND healthCategory LIKE '" . $param['value'] . "%'";
                else
                    $additionQuery = null;
                $conscriptsWithDocuments = Helper::getConscriptsWithDocuments($param["documentType"], $param["inProcess"], Profile::isHavePermission("viewForAll") ? null : Profile::$user["id"], $additionQuery);
                break;

            default:
                if ($valueLength > 0)
                    $additionQuery = "AND " . $param["type"] . " LIKE '" . $param['value'] . "'";
                else
                    $additionQuery = null;

                $conscriptsWithDocuments = Helper::getConscriptsWithDocuments($param["documentType"], null, Profile::isHavePermission("viewForAll") || $param["type"] == "id" ? null : Profile::$user["id"], null, $additionQuery);
                break;
        }

        $result = "<div id='resizeDiv' class='d-grid gap-2'>";

        if ($valueLength < 3 && $valueLength != 0 && $param["type"] != "rvkArticle" && $param["type"] != "id" && $param["type"] != "article" && $param["type"] != "healthCategory" && $param["type"] != "healthCategoryRVK")
            $result .= "<div class='lead'>Введите больше 2 символов.</div>";

        if (count($conscriptsWithDocuments) > 0) {
            foreach ($conscriptsWithDocuments as $conscript)
                $result .= DocumentBuilder::getConscriptWithDocumentsCard($conscript);
        } else {
            $result .= "<div class='lead'>Документы не найдены.</div>";
        }

        $result .= "</div>";

        return $result;
    }

    static function searchPattern($param)
    {
        $valueLength = mb_strlen($param['value'], "UTF-8");

        $patternList = Helper::getUserPatternList($param["value"]);

        $result = "<div id='resizeDiv' class='d-grid gap-2'>";

        if ($valueLength < 3 && $valueLength != 0)
            $result .= "<div class='lead'>Введите больше 2 символов.</div>";

        if (count($patternList) > 0) {
            foreach ($patternList as $pattern)
                $result .= DocumentBuilder::getPatternCard($pattern);
        } else {
            $result .= "<div class='lead'>Шаблоны не найдены.</div>";
        }
        $result .= "</div>";

        return $result;
    }

    static function saveProtocolChanges($param)
    {
        if ($param['conscriptID'] === "")
            return "Ошибка сохранения информации протокола. ID призывника не найден.";
        else {
            $query = "UPDATE `conscript` SET letterNumber=:letterNumber, protocolNumber=:protocolNumber, protocolDate=:protocolDate WHERE id=:id";
            $dataArr = [
                "id" => $param['conscriptID'],
                "letterNumber" => $param['letterNumber'],
                "protocolNumber" => $param['protocolNumber'],
                "protocolDate" => Helper::formatDateToView($param['protocolDate'])
            ];
            Database::execute($query, $dataArr, "current");
            return "continue";
        }
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

    static function getRvkDiagnosisByConscriptID($param)
    {
        $diagnosis = Database::execute("SELECT rvkDiagnosis FROM `conscript` WHERE id=:id", ["id" => $param['conscriptID']], "current")[0];

        if (count($diagnosis) > 0) {
            return $diagnosis['rvkDiagnosis'];
        } else {
            return "";
        }
    }

    static function addDocument($param)
    {
        $query = "INSERT INTO `documents` (conscriptID, article, healthCategory, creatorID, complaint, anamnez, objectData, specialResult, diagnosis, documentDate, documentType, postPeriod, reasonForCancel, destinationPoints) VALUES (:conscriptID, :article, :healthCategory, :creatorID, :complaint, :anamnez, :objectData, :specialResult, :diagnosis, :documentDate, :documentType, :postPeriod, :reasonForCancel, :destinationPoints);";
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
            "documentDate" => Helper::formatDateToView($param["documentDate"]),
            "documentType" => $param['documentType'],
            "postPeriod" => $param['postPeriodSelect'],
            "reasonForCancel" => $param['reasonForCancelTextarea'],
            "destinationPoints" => $param['destinationPointsInput']
        ];
        Database::execute($query, $dataArr, "current");
        return "reloadPage";
    }

    static function editDocument($param)
    {
        $query = "UPDATE `documents` SET documentType=:documentType, article=:article, healthCategory=:healthCategory, complaint=:complaint, anamnez=:anamnez, objectData=:objectData, specialResult=:specialResult, diagnosis=:diagnosis, documentDate=:documentDate, postPeriod=:postPeriod, reasonForCancel=:reasonForCancel, destinationPoints=:destinationPoints WHERE id = :id;";
        $dataArr = [
            "documentType" => $param['documentType'],
            "article" => $param['articleInput'],
            "healthCategory" => $param['healthCategorySelect'],
            "complaint" => $param['complaintTextarea'],
            "anamnez" => $param['anamnezTextarea'],
            "objectData" => $param['objectDataTextarea'],
            "specialResult" => $param['specialResultTextarea'],
            "diagnosis" => $param['diagnosisTextarea'],
            "documentDate" => Helper::formatDateToView($param["documentDate"]),
            "postPeriod" => $param['postPeriodSelect'],
            "reasonForCancel" => $param['reasonForCancelTextarea'],
            "destinationPoints" => $param['destinationPointsInput'],
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

    //<-------------------------------------УКП и страница печати------------------------------------->//
    static function setInProcessFalse($param)
    {
        if ($param['conscriptID'] === "")
            return "Ошибка изменения статуса. ID призывника не найден.";
        else {
            $query = "UPDATE `conscript` SET inProcess=0 WHERE id=:id";
            $dataArr = [
                "id" => $param['conscriptID'],
            ];
            Database::execute($query, $dataArr, "current");
            return "reloadPage";
        }
    }

    static function unlockCard($param)
    {
        if ($param['conscriptID'] === "")
            return "Ошибка изменения статуса. ID призывника не найден.";
        else {
            $query = "UPDATE `conscript` SET inProcess=1 WHERE id=:id";
            $dataArr = [
                "id" => $param['conscriptID'],
            ];
            Database::execute($query, $dataArr, "current");

            $query = "UPDATE `documents` SET countable=0 WHERE conscriptID=:id";
            $dataArr = [
                "id" => $param['conscriptID'],
            ];
            Database::execute($query, $dataArr, "current");

            $query = "DELETE FROM `protocolChanges` WHERE conscriptID=:id";
            $dataArr = [
                "id" => $param['conscriptID'],
            ];
            Database::execute($query, $dataArr, "current");

            return "reloadPage";
        }
    }

    static function changeDocumentCountable($param)
    {
        if ($param['documentID'] === "")
            return "Ошибка изменения статуса документа. ID документа не найден.";
        else {
            $query = "UPDATE `documents` SET countable=:state WHERE id=:id";
            $dataArr = [
                "id" => $param['documentID'],
                "state" => $param['state']
            ];
            Database::execute($query, $dataArr, "current");

            return "reloadPage";
        }
    }

    static function saveProtocolValuesChanges($param)
    {
        if ($param['conscriptID'] === "")
            return "Ошибка сохранения изменений. ID призывника не найден.";
        else {

            if (!empty($param['conscriptID']) && !empty(Helper::formatDateToView($param['birthDate'])) && !empty($param['rvkDiagnosis'])) {
                $queryConscript = "UPDATE `conscript` SET name=:name, birthDate=:birthDate, rvkDiagnosis=:rvkDiagnosis WHERE id=:id";
                $dataArrConscript = [
                    "id" => $param['conscriptID'],
                    "name" => preg_replace('/\s\s+/', ' ', trim($param['name'])),
                    "birthDate" => Helper::formatDateToView($param['birthDate']),
                    "rvkDiagnosis" => trim($param['rvkDiagnosis'])
                ];
                Database::execute($queryConscript, $dataArrConscript, "current");
            }

            $protocolChanges = Helper::getProtocolChanges($param['conscriptID']);
            if (count($protocolChanges) > 0) {
                $queryProtocol = "UPDATE `protocolChanges` SET complaint=:complaint, anamnez=:anamnez, objectData=:objectData, specialResult=:specialResult, diagnosis=:diagnosis WHERE id=:id";
                $dataArrProtocol = [
                    "id" => $protocolChanges[0]["id"],
                    "complaint" => trim($param['complaint']),
                    "anamnez" => trim($param['anamnez']),
                    "objectData" => trim($param['objectData']),
                    "specialResult" => trim($param['specialResult']),
                    "diagnosis" => trim($param['diagnosis'])
                ];
                Database::execute($queryProtocol, $dataArrProtocol, "current");
                return "Изменения внесены в запись.";
            } else {
                $queryProtocol = "INSERT INTO `protocolChanges` (conscriptID, complaint, anamnez, objectData, specialResult, diagnosis) VALUES (:conscriptID, :complaint, :anamnez, :objectData, :specialResult, :diagnosis);";
                $dataArrProtocol = [
                    "conscriptID" => $param['conscriptID'],
                    "complaint" => trim($param['complaint']),
                    "anamnez" => trim($param['anamnez']),
                    "objectData" => trim($param['objectData']),
                    "specialResult" => trim($param['specialResult']),
                    "diagnosis" => trim($param['diagnosis'])
                ];
                Database::execute($queryProtocol, $dataArrProtocol, "current");

                return "Изменения добавлены.";
            }

        }
    }

    static function deleteProtocolValuesChanges($param)
    {
        if ($param['conscriptID'] === "")
            return "Ошибка удаления документа. ID не найден.";
        else {
            $query = "DELETE FROM `protocolChanges` WHERE conscriptID=:conscriptID";
            $dataArr = [
                "conscriptID" => $param['conscriptID'],
            ];
            Database::execute($query, $dataArr, "current");
            return "reloadPage";
        }
    }

    //<-------------------------------------УКП и страница печати------------------------------------->//
}