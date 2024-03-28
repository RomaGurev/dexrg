<?
class ConscriptBuilder
{
    public static function getConscriptCard($conscript, $showSelectButton)
    {
        $result = '<div class="card">
        <div class="card-header lead d-flex">
        <div class="col">
        <b><a ' . ($showSelectButton == "true" ? 'onclick="select(' . $conscript['id'] . ', \'' . $conscript['name'] . (!empty($conscript['birthDate']) ? ' [' . Helper::formatDateToView($conscript['birthDate']) . ']' : '') . '\')"' : 'onclick="openConscriptModal(' . $conscript['id'] . ')"') . 'style="cursor: pointer;">' . $conscript["name"] . '</a></b> ' . (!empty($conscript['birthDate']) ? '[' . Helper::formatDateToView($conscript['birthDate']) . ']' : '') . '
        </div>
        <div class="col-auto">
            ' . ($conscript['inProcess'] == true ? 'В работе' : 'Завершен') .  '
        </div>
        </div>
        <div class="card-body d-flex" style="padding: 0.25rem 1rem">
            <div class="col">
                <p class="card-text mb-1 lead"><b>Военный комиссариат: </b>' . (!empty($conscript['vk']) ? Helper::getVKNameById($conscript['vk'])["name"] : 'Нет информации'). '</p>
                <p class="card-text mb-1 lead"><b>Период призыва: </b>' . Helper::convertAdventPeriodToString($conscript["adventPeriod"]) . '</p>
            </div>
            <div class="col">
                <p class="card-text mb-1 lead"><b>Статья РВК: </b>' . (!empty($conscript['rvkArticle']) ? $conscript["rvkArticle"] : 'Нет информации') . '</p>
                <p class="card-text mb-1 lead"><b>Категория годности РВК: </b>' . (!empty($conscript['healthCategory']) ? $conscript["healthCategory"] : 'Нет информации') . '</p>
            </div>
            <div class="col">
                <p class="card-text lead"><b>Диагноз РВК: </b>' . (!empty($conscript['rvkDiagnosis']) ? Helper::getShortenString($conscript["rvkDiagnosis"], 45) : 'Информация не указана') . '</p>
            </div>
            <div class="col-auto d-flex">
        ';

        if($showSelectButton == "true"){
            $result .=  '<button type="button" onclick="select(' . $conscript['id'] . ', \'' . $conscript['name'] . (!empty($conscript['birthDate']) ? ' [' . Helper::formatDateToView($conscript['birthDate']) . ']' : '') . '\')" class="btn btn-outline-success" style="align-self: center;">Выбрать призывника</button>';
        } else {
            $result .= '<button type="button" onclick="openConscriptModal(' . $conscript['id'] . ')" class="btn btn-outline-primary" style="align-self: center;">Открыть УКП</button>';
        }

        $result .= '
        </div>
        </div>
        </div>';
        return $result;
    }

    public static function getConscriptModalInfo($conscript, $documentsInfo = null) {

        //Создание массива, содержащего документы для вывода
        $documents = array();

        //Добавление в массив документа о создании УКП (регистрации призывника)
        $authorProfile = Helper::getProfileByUserID($conscript['creatorID']);
        array_push($documents, [
            "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
            "authorName" => $authorProfile["name"],
            "documentType" => "Регистрация призывника",
            "documentDate" => $conscript["creationDate"],
            "documentLink" => "#"
        ]);
        //Добавление в массив документа о создании УКП (регистрации призывника)

        //Добавление в массив документов
        foreach ($documentsInfo as $document) {
            $authorProfile = Helper::getProfileByUserID($document['creatorID']);

            switch ($document["documentType"]) {
                case 'changeCategory':
                    array_push($documents, [
                        "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
                        "authorName" => $authorProfile["name"],
                        "documentType" => "Изменение категории (" . $conscript['healthCategory'] . " <i class='fa fa-angle-double-right' aria-hidden='true'></i> <b>" . $document['healthCategory'] . "</b>)",
                        "documentDate" => "",
                        "documentLink" => "/changeCategory?id=" . $document['id']
                    ]);
                    break;
                
                case 'control':
                    array_push($documents, [
                        "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
                        "authorName" => $authorProfile["name"],
                        "documentType" => "Контроль от " . $document["documentDate"] . " (" . $conscript['healthCategory'] . " <i class='fa fa-angle-double-right' aria-hidden='true'></i> <b>" . $document['healthCategory'] . "</b>)",
                        "documentDate" => "",
                        "documentLink" => "/control?id=" . $document['id']
                    ]);
                    break;
                
                case 'return':
                    array_push($documents, [
                        "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
                        "authorName" => $authorProfile["name"],
                        "documentType" => "Возврат по статье " . $document["article"] . " от " . $document["documentDate"],
                        "documentDate" => "",
                        "documentLink" => "/return?id=" . $document['id']
                    ]);
                    break;

                case 'complaint':
                    array_push($documents, [
                        "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
                        "authorName" => $authorProfile["name"],
                        "documentType" => "Жалоба от " . $document["documentDate"] . " (" . $conscript['healthCategory'] . " <i class='fa fa-angle-double-right' aria-hidden='true'></i> <b>" . $document['healthCategory'] . "</b>)",
                        "documentDate" => "",
                        "documentLink" => "/complaint?id=" . $document['id']
                    ]);
                    break;

                default:
                    array_push($documents, [
                        "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
                        "authorName" => $authorProfile["name"],
                        "documentType" => "Неизвестный документ",
                        "documentDate" => "",
                        "documentLink" => "#"
                    ]);
                    break;
            }
        }
        //Добавление в массив документов

        //Вывод результатов в переменную result
        $result .= '
        <div class="d-flex">
            <div class="col">
                <p class="card-text mb-1 lead"><b>ФИО: </b>' . $conscript['name'] . '</p>
                <p class="card-text mb-1 lead"><b>Дата рождения: </b>' . (!empty($conscript['birthDate']) ? Helper::formatDateToView($conscript['birthDate']) : 'Информация не указана'). '</p>
                <p class="card-text mb-1 lead"><b>Уникальный номер: </b>' . $conscript['id'] . '</p>
                <p class="card-text mb-2 lead"><b>Статус УКП: </b>' . ($conscript['inProcess'] == true ? 'В работе' : 'Завершен') . '</p>
                <p class="card-text mb-1 lead"><b>Военный комиссариат: </b>' . (!empty($conscript['vk']) ? Helper::getVKNameById($conscript['vk'])["name"] : 'Информация не указана'). '</p>
                <p class="card-text mb-1 lead"><b>Период призыва: </b>' . Helper::convertAdventPeriodToString($conscript["adventPeriod"]) . '</p>
                <p class="card-text mb-1 lead"><b>Статья РВК: </b>' . (!empty($conscript['rvkArticle']) ? $conscript["rvkArticle"] : 'Информация не указана') . '</p>
                <p class="card-text mb-1 lead"><b>Диагноз РВК: </b>' . (!empty($conscript['rvkDiagnosis']) ? Helper::getShortenString($conscript["rvkDiagnosis"], 100) : 'Информация не указана') . '</p>
                <p class="card-text mb-1 lead"><b>Категория годности РВК: </b>' . (!empty($conscript['healthCategory']) ? "«" . $conscript['healthCategory'] . "» - " . Helper::getHealthCategoryNameByID($conscript["healthCategory"]) : 'Информация не указана') . '</p>
            </div>

            <div class="col-7">';

        if(Profile::isHavePermission("canAdd") && $conscript['inProcess'] == true) {

        $result .='<div class="row">
                        <div class="col-lg mb-2 pe-lg-0"><button type="button" onclick="addChangeCategory(' . $conscript['id'] . ');" class="btn btn-outline-success w-100">Изменить категорию</button></div>
                        <div class="col-lg mb-2"><button type="button" onclick="addControl(' . $conscript['id'] . ');" class="btn btn-outline-primary w-100">Добавить контроль</button></div>
                        <div class="w-100"></div>
                        <div class="col-lg mb-2 pe-lg-0"><button type="button" onclick="addReturn(' . $conscript['id'] . ');" class="btn btn-outline-dark w-100">Добавить возврат</button></div>
                        <div class="col-lg"><button type="button" onclick="addComplaint(' . $conscript['id'] . ');" class="btn btn-outline-dark w-100">Добавить жалобу</button></div>
                    </div>';
        }
        
        $result .= '<h3 class="card-text mb-1 lead mt-3"><b>История документов:</b></h3>

                <div class="documentLine mt-2">';

        foreach ($documents as $document) {
            $result .= '<div class="documentItem">
                        <div class="point"></div>
                        <a href="' . $document["documentLink"] . '">' . $document["documentType"] . ' ' . $document["documentDate"] . ' <span style="color: black;" data-toggle="tooltip" title="' . $document["authorName"] . '"> [' . $document["authorPosition"] . ']</span></a>
                    </div>';
        }

        $finalHealthResult = Helper::getFinalHealthResult($conscript['id']);

        $result .= '</div>';
               
        if($finalHealthResult != null) {
            $result .= '<div class="finalResults">
                    <p class="card-text mb-1 lead mt-2" style="border-top: 1px dashed #C0C0C0;"><b>Итоговая категория годности: </b>' . Helper::getHealthCategoryNameByID($finalHealthResult["healthCategory"]) . '</p>
                    <p class="card-text mb-1 lead"><b>Итоговая статья: </b>' . (empty($finalHealthResult["article"]) ? "отсутствует" : $finalHealthResult["article"]) . '</p>
                </div>';
        }
        $result .= '</div></div>';

        $result .= '
        <div class="d-flex mt-2">
            <div class="col">
                <button type="button" onclick="editConscript(' . $conscript['id'] . ');" class="btn btn-outline-dark">Редактировать УКП</button>
            </div>';

        if(Profile::isHavePermission("viewForAll")) {
            $result .= '<div class="col-auto">
                <button type="button" onclick="printLetter(' . $conscript['id'] . ');" class="btn btn-outline-primary">Служебное письмо</button>
                <button type="button" onclick="printExtract(' . $conscript['id'] . ');" class="btn btn-success">Выписка</button>
                <button type="button" onclick="printProtocol(' . $conscript['id'] . ');" class="btn btn-primary">Печать протокола</button>
            </div>';
        }

        $result .= '</div>';

        $result .= '
        <script>
        $(function () {
            $(\'[data-toggle="tooltip"]\').tooltip()
        })
        </script>
        ';
        return $result;
    }

}