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
            ' . ($conscript['inProcess'] == true ? 'В работе' : '<span class="text-danger">Завершен</span>') .  '
        </div>
        </div>
        <div class="card-body d-flex" style="padding: 0.25rem 1rem">
            <div class="col">
                <p class="card-text mb-1 lead"><b>Военный комиссариат: </b>' . (!empty($conscript['vk']) ? Helper::getVKNameById($conscript['vk'])["name"] : 'Нет информации') . '</p>
                <p class="card-text mb-1 lead"><b>Период призыва: </b>' . Helper::convertAdventPeriodToString($conscript["adventPeriod"]) . '</p>
            </div>
            <div class="col">
                <p class="card-text mb-1 lead"><b>Статья РВК: </b>' . (!empty($conscript['rvkArticle']) ? $conscript["rvkArticle"] : 'Нет информации') . '</p>
                <p class="card-text mb-1 lead"><b>Категория годности РВК: </b>' . (!empty($conscript['healthCategory']) ? $conscript["healthCategory"] : 'Нет информации') . '</p>
            </div>
            <div class="col me-2">
                <p class="card-text lead"><b>Диагноз РВК: </b>' . (!empty($conscript['rvkDiagnosis']) ? Helper::getShortenString($conscript["rvkDiagnosis"], 45) : 'Информация не указана') . '</p>
            </div>
            <div class="col-auto d-flex">
        ';

        if($showSelectButton == "true"){
            $result .=  '<button type="button" onclick="select(' . $conscript['id'] . ', \'' . $conscript['name'] . (!empty($conscript['birthDate']) ? ' [' . Helper::formatDateToView($conscript['birthDate']) . ']' : '') . " - " . (!empty($conscript['vk']) ? Helper::getVKNameById($conscript['vk'])["name"] : "") . '\')" class="btn btn-outline-success" style="align-self: center;">Выбрать призывника</button>';
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
            "documentLink" => "#",
            "documentCountable" => true
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
                        "documentLink" => "/changeCategory?id=" . $document['id'],
                        "documentCountable" => $document['countable']
                    ]);
                    break;
                
                case 'control':
                    array_push($documents, [
                        "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
                        "authorName" => $authorProfile["name"],
                        "documentType" => "Контроль от " . $document["documentDate"] . " (" . $conscript['healthCategory'] . " <i class='fa fa-angle-double-right' aria-hidden='true'></i> <b>" . $document['healthCategory'] . "</b>)",
                        "documentDate" => "",
                        "documentLink" => "/control?id=" . $document['id'],
                        "documentCountable" => $document['countable']
                    ]);
                    break;
                
                case 'return':
                    array_push($documents, [
                        "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
                        "authorName" => $authorProfile["name"],
                        "documentType" => "Возврат по статье " . $document["article"] . " от " . $document["documentDate"],
                        "documentDate" => "",
                        "documentLink" => "/return?id=" . $document['id'],
                        "documentCountable" => $document['countable']
                    ]);
                    break;

                case 'complaint':
                    array_push($documents, [
                        "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
                        "authorName" => $authorProfile["name"],
                        "documentType" => "Жалоба от " . $document["documentDate"] . " (" . $conscript['healthCategory'] . " <i class='fa fa-angle-double-right' aria-hidden='true'></i> <b>" . $document['healthCategory'] . "</b>)",
                        "documentDate" => "",
                        "documentLink" => "/complaint?id=" . $document['id'],
                        "documentCountable" => $document['countable']
                    ]);
                    break;

                case 'confirmation':
                    array_push($documents, [
                        "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
                        "authorName" => $authorProfile["name"],
                        "documentType" => "Утверждение от " . $document["documentDate"] . " (<b>" . $document['healthCategory'] . "</b>)",
                        "documentDate" => "",
                        "documentLink" => "/confirmation?id=" . $document['id'],
                        "documentCountable" => $document['countable']
                    ]);
                    break;

                default:
                    array_push($documents, [
                        "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
                        "authorName" => $authorProfile["name"],
                        "documentType" => "Неизвестный документ",
                        "documentDate" => "",
                        "documentLink" => "#",
                        "documentCountable" => $document['countable']
                    ]);
                    break;
            }
        }
        //Добавление в массив документов

        //Запрос на финальную категорию
        $finalHealthResult = Helper::getFinalHealthResult($conscript['id']);
        //Запрос на финальную категорию

        //Вывод результатов в переменную result
        $result .= '
        <div class="d-flex">
            <div class="row col me-3" style="word-break: break-all;">
                <p class="card-text mb-0 lead"><b>ФИО: </b>' . $conscript['name'] . ' [' . $conscript['id'] . ']</p>
                <p class="card-text mb-0 lead"><b>Дата рождения: </b>' . (!empty($conscript['birthDate']) ? Helper::formatDateToView($conscript['birthDate']) : 'Информация не указана'). '</p>
                <p class="card-text mb-0 lead"><b>Статус УКП: </b>' . ($conscript['inProcess'] == true ? 'В работе' : '<span class="text-danger">Завершен</span>') . '</p>
                <p class="card-text mb-0 lead"><b>Военный комиссариат: </b>' . (!empty($conscript['vk']) ? Helper::getVKNameById($conscript['vk'])["name"] : 'Информация не указана'). '</p>
                <p class="card-text mb-0 lead"><b>Период призыва: </b>' . Helper::convertAdventPeriodToString($conscript["adventPeriod"]) . '</p>
                <p class="card-text mb-0 lead"><b>Статья РВК: </b>' . (!empty($conscript['rvkArticle']) ? $conscript["rvkArticle"] : 'Информация не указана') . '</p>
                <p class="card-text mb-0 lead"><b>Диагноз РВК: </b> <br>' . (!empty($conscript['rvkDiagnosis']) ? Helper::getShortenString($conscript["rvkDiagnosis"], 100) : 'Информация не указана') . '</p>
                <p class="card-text mb-0 lead"><b>Категория годности РВК: </b> <br>' . (!empty($conscript['healthCategory']) ? "«" . $conscript['healthCategory'] . "» - " . Helper::getHealthCategoryNameByID($conscript["healthCategory"]) : 'Информация не указана') . '</p>
            
            
                <div class="align-self-end mt-2">
                    <button type="button" onclick="editConscript(' . $conscript['id'] . ');" class="btn btn-outline-dark">Редактировать УКП</button>
                </div>
            
            </div>

            <div class="col-7" style="border-left: 1px dashed #C0C0C0;padding: 1rem;background-color: rgba(33, 37, 41, 0.03);margin: -1rem;">';

        if(Profile::isHavePermission("canAdd")) {
            if($conscript['inProcess']) {
                $result .= '<div class="d-flex">
                    <div class="col me-2">
                        <select id="addDocumentType" class="form-control form-select" style="cursor:pointer;">';

                foreach (Config::getValue("documentType") as $key => $value) {
                    if($key == "confirmation" && !Profile::isHavePermission("confirmation"))
                        continue;
                    
                        $result .= "<option value='" . $key . "'>" . $value . "</option>";
                }
                $result .=  '</select>
                    </div>

                    <div class="col">
                        <button type="button" onclick="addDocument(' . $conscript['id'] . ');" class="btn btn-outline-primary w-100">Добавить документ</button>
                    </div>
                </div>';
            } else {
                if($finalHealthResult["healthCategory"] == "О" && !Profile::isArchiveMode()) {
                    $result .= '<div class="col">
                    <button type="button" onclick="unlockCard(' . $conscript['id'] . '); location.reload();" class="btn btn-outline-success w-100">Разблокировать учетную карту</button>
                    </div>';
                }
            }
        }


        $result .= '<h3 class="card-text mb-1 mt-2 lead"><b>История документов:</b></h3>
                <div class="documentLine mt-2">';

        foreach ($documents as $document) {
            if($document["documentCountable"])
                $countableDocumentsCount++;
            $result .= '<div class="documentItem">
                        <div class="' . ($document["documentCountable"] ? "point" : "point-uncountable") . '"></div>
                        <a href="' . $document["documentLink"] . '">' . ($document["documentCountable"] ? $document["documentType"] : '<span style="color: black;" data-toggle="tooltip" title="Документ не учитывается, в связи с повторной явкой призывника">' . $document["documentType"] . '</span>') . ' ' . $document["documentDate"] . ' <span style="color: black;" data-toggle="tooltip" title="' . $document["authorName"] . '"> [' . $document["authorPosition"] . ']</span></a>
                    </div>';
        }

        $result .= '</div>';
               
        if($finalHealthResult != null) {
            $result .= '<div>
            <div class="mt-3 mb-2" style="margin: 0 -1rem;border-top: 1px dashed #C0C0C0;"></div>
                    <p class="card-text mb-1 lead"><b>Итоговый документ: </b></p>
                    <p class="card-text mb-1 lead">' . Config::getValue("documentType")[$finalHealthResult["documentType"]] . (empty($finalHealthResult["healthCategory"]) ? "" : " с категорией «" . $finalHealthResult["healthCategory"] . "» - " . Helper::getHealthCategoryNameByID($finalHealthResult["healthCategory"])) . '
                    ' . (empty($finalHealthResult["postPeriod"]) ? "" : " сроком на " . Config::getValue("postPeriod")[$finalHealthResult["postPeriod"]]) . (empty($finalHealthResult["article"]) ? "" : " по статье " . $finalHealthResult["article"]) . '
                    </p>
                </div>';
        }

        $healthCategory = mb_substr($finalHealthResult["healthCategory"], 0, 1);
        $rvkHealthCategory = mb_substr($conscript['healthCategory'], 0, 1);
        $resultBeChanged = $conscript['healthCategory'] == $finalHealthResult["healthCategory"] || $healthCategory == "А" && $rvkHealthCategory == "Б" || $healthCategory == "Б" && $rvkHealthCategory == "А";

        if(Profile::isHavePermission("viewForAll") && $countableDocumentsCount > 1) {
            $result .= '
                <div class="d-flex mt-4 mb-3" style="flex-wrap: wrap;">
                    <input id="protocolConscriptID" class="d-none" type="text" value="' . $conscript['id'] . '">
                    <div class="col me-2">
                        <label for="letterNumber" class="form-label"><b>Номер письма</b></label>
                        <input type="text" autocomplete="off" class="form-control" id="letterNumber" value="' . $conscript['letterNumber'] . '" ' . ($resultBeChanged ? "disabled" : "") . '>
                    </div>
                    <div class="col me-2">
                        <label for="protocolNumber" class="form-label"><b>Номер протокола</b></label>
                        <input type="text" autocomplete="off" class="form-control" id="protocolNumber" value="' . $conscript['protocolNumber'] . '">
                    </div>
                    <div class="col align-self-end">
                        <label for="protocolDate" class="form-label"><b>Дата протокола</b></label>
                        <input type="date" autocomplete="off" class="form-control" value="' . (empty($conscript['protocolDate']) ? date("Y-m-d") : date('Y-m-d', strtotime($conscript['protocolDate']))) . '" id="protocolDate">
                    </div>
                </div>

                <div class="d-flex" style="flex-wrap: wrap;">
                    <div class="col me-2">

                    ' . ($resultBeChanged ? '<span style="color: black;" data-toggle="tooltip" title="Служебное письмо недоступно без изменения решения призывной комиссии">
                    <button type="button" class="btn btn-dark w-100" disabled>Письмо</button>
                    </span>' : '<button type="button" onclick="printLetter(' . $conscript['id'] . ');" class="btn btn-dark w-100">Письмо</button>') . '

                    </div>
                    <div class="col me-0 me-lg-2 mb-2 mb-lg-0"><button type="button" onclick="printExtract(' . $conscript['id'] . ');" class="btn btn-success w-100">Выписка</button></div>
                    <div class="col"><button type="button" onclick="printProtocol(' . $conscript['id'] . ');" class="btn btn-primary w-100">Протокол</button></div>
                </div>';
        }

        $result .= '</div></div>';

        $result .= '
        <script>
        $(function () {
            $(\'[data-toggle="tooltip"]\').tooltip();
        });

        $("#protocolNumber").on("change", function () {
            saveProtocolChanges(null);
        });
        
        $("#protocolDate").on("change", function () {
            saveProtocolChanges(null);
        });

        $("#letterNumber").on("change", function () {
            saveProtocolChanges(null);
        });
        </script>
        ';
        return $result;
    }

}