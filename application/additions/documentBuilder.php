<?
class DocumentBuilder
{
    public static function getConscriptWithDocumentsCard($conscriptWithDocuments)
    {
        $result = '
        <div class="card">
        <div class="card-header lead d-flex" style="border-bottom: 0;">
            <div class="col">
                <a style="cursor: pointer;" onclick="openConscriptModal(' . $conscriptWithDocuments['id'] . ')"><b>' . $conscriptWithDocuments["name"] . '</b>' . (!$conscriptWithDocuments['inProcess'] && !empty($conscriptWithDocuments['protocolNumber']) && !empty($conscriptWithDocuments['protocolDate']) ? " по протоколу от " . $conscriptWithDocuments['protocolDate'] . " №" . $conscriptWithDocuments['protocolNumber'] : "") . '</a> ' . (!empty($conscriptWithDocuments['birthDate']) ? '[' . Helper::formatDateToView($conscriptWithDocuments['birthDate']) . ']' : '') . '
            </div>
            <div class="col-auto">'
            . (!empty($conscriptWithDocuments['vk']) ? Helper::getVKNameById($conscriptWithDocuments['vk'])["name"] : 'Нет информации').
            '</div>
        </div>';

        foreach ($conscriptWithDocuments["documents"] as $value) {
            $authorProfile = Helper::getProfileByUserID($value['creatorID']);
            $author_position = Config::getValue("userType")[$authorProfile["position"]][0];


            switch ($value["documentType"]) {
                case 'changeCategory':
                    $documentText = "Изменение категории " . (empty($value["article"]) ? "" : " по статье <b>" . $value["article"] . "</b> ") . "(" . $conscriptWithDocuments["healthCategory"] . " <i class='fa fa-angle-double-right' aria-hidden='true'></i> <b>" . $value["healthCategory"] . "</b>)" . ($conscriptWithDocuments["showCreator"] == true ? " от " . mb_strtolower($author_position) . "а " : "");
                    break;
                case 'control':
                    $documentText = "Контроль " . (empty($value["article"]) ? "" : " по статье <b>" . $value["article"] . "</b> ") . "от " . $value["documentDate"] . " (" . $conscriptWithDocuments["healthCategory"] . " <i class='fa fa-angle-double-right' aria-hidden='true'></i> <b>" . $value["healthCategory"] . "</b>)" . ($conscriptWithDocuments["showCreator"] == true ? " от " . mb_strtolower($author_position) . "а " : "");
                    break;
                case 'return':
                    $documentText = "Возврат " . (empty($value["article"]) ? "" : " по статье <b>" . $value["article"] . "</b> ") . ($conscriptWithDocuments["showCreator"] == true ? " от " . mb_strtolower($author_position) . "а " : "");
                    break;
                case 'complaint':
                    $documentText = "Жалоба " . (empty($value["article"]) ? "" : " по статье <b>" . $value["article"] . "</b> ") . "от " . $value["documentDate"] . " (" . $conscriptWithDocuments["healthCategory"] . " <i class='fa fa-angle-double-right' aria-hidden='true'></i> <b>" . $value["healthCategory"] . "</b>)" . ($conscriptWithDocuments["showCreator"] == true ? " от " . mb_strtolower($author_position) . "а " : "");
                    break;
                case 'confirmation':
                    $documentText = "Утверждение от " . $value["documentDate"] . " (<b>" . $value["healthCategory"] . "</b>)" . ($conscriptWithDocuments["showCreator"] == true ? " от " . mb_strtolower($author_position) . "а " : "");
                    break;
                default:
                    $documentText = "Ошибка выбора документа...";
                    break;
            }


            $result .= ' <div class="card-header lead d-flex" style="border-top: var(--bs-card-border-width) solid var(--bs-card-border-color); border-radius: 0;">

            <div class="col-auto d-flex" style="width: 25px;">
                <div data-bs-toggle="collapse" class="me-2 ' . ($conscriptWithDocuments["inProcess"] ? "" : "collapsed") . '" style="align-self: center; cursor: pointer;" data-bs-target="#collapse' . $conscriptWithDocuments['id'] . '-' . $value["id"] . '" aria-expanded="true"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
            </div>

            <div class="col" style="align-self: center;">' . $documentText . '</div>
            <div class="col-auto">';

            if($value["countable"] && ($value["creatorID"] == Profile::$user["id"] || Profile::isHavePermission("viewForAll"))) {
                $result .= '<a class="btn btn-outline-dark me-2" href="/document?documentType=' . $value["documentType"] . '&id=' . $value["id"] . '">Редактировать документ</a>';
            }    
            
            $result .= '<a class="btn btn-success" href="/print?template=examination&id=' . $value["id"] . '">Распечатать документ</a></div>
            </div>


            <div class="card-body row collapse ' . ($conscriptWithDocuments["inProcess"] ? "show" : "") . '" id="collapse' . $conscriptWithDocuments['id'] . '-' . $value["id"] . '" style="padding: 0.25rem 1rem; overflow-wrap: break-word;">
                <div class="col w-25">
                    <p class="card-text mb-1 lead "><b>Жалобы: </b></p>
                    ' . (empty($value["complaint"]) ? "Не заполнено" : Helper::getShortenString($value["complaint"])) . '
                    <p class="card-text mb-1 lead mt-2"><b>Диагноз: </b></p>
                    ' . (empty($value["diagnosis"]) ? "Не заполнено" : Helper::getShortenString($value["diagnosis"])) . '
                </div>
                <div class="col w-25">
                    <p class="card-text mb-1 lead"><b>Анамнез: </b></p>
                    ' . (empty($value["anamnez"]) ? "Не заполнено" : Helper::getShortenString($value["anamnez"])) . '
                </div>
                <div class="col w-25">
                    <p class="card-text mb-1 lead"><b>Данные объективного исследования: </b></p>
                    ' . (empty($value["objectData"]) ? "Не заполнено" : Helper::getShortenString($value["objectData"])) . '
                </div>
                <div class="col w-25">
                    <p class="card-text mb-1 lead"><b>Результаты специальных исследований: </b></p>
                    ' . (empty($value["specialResult"]) ? "Не заполнено" : Helper::getShortenString($value["specialResult"])) . '
                </div>

            </div>';
        }

        $result .= '</div>';
        return $result;
    }

    public static function getPatternCard($pattern)
    {
        $result = '
        <div class="card">
        <div class="card-header lead d-flex">
            <div class="col-auto d-flex" style="width: 25px;">
                <div data-bs-toggle="collapse" class="me-2 collapsed" style="align-self: center; cursor: pointer;" data-bs-target="#collapse' . $pattern['id'] . '" aria-expanded="true"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
            </div>
            <div class="col d-flex">
                <b style="align-self: center;">' . $pattern["name"] . '</b>
            </div>
            <div class="col-auto">
                <button onclick="editPattern(' . $pattern['id'] . ')" class="btn btn-outline-dark">Редактировать шаблон</button>
            </div>
        </div> <div class="collapse" id="collapse' . $pattern['id'] . '">
            <div class="card-body row" style="padding: 0.25rem 1rem; overflow-wrap: break-word;">
                <div class="col w-20">
                    <p class="card-text mb-1 lead "><b>Жалобы: </b></p>
                    ' . (empty($pattern["complaint"]) ? "Не заполнено" : Helper::getShortenString($pattern["complaint"])) . '
                    <p class="card-text mb-1 lead mt-2"><b>Категория годности: </b></p>
                    ' . (empty($pattern["healthCategory"]) ? "Не указана" : $pattern["healthCategory"]) . '
                    <p class="card-text mb-1 lead mt-2"><b>Статья: </b></p>
                    ' . (empty($pattern["article"]) ? "Не указана" : $pattern["article"]) . '
                </div>
                <div class="col w-20">
                    <p class="card-text mb-1 lead"><b>Анамнез: </b></p>
                    ' . (empty($pattern["anamnez"]) ? "Не заполнено" : Helper::getShortenString($pattern["anamnez"])) . '
                </div>
                <div class="col w-20">
                    <p class="card-text mb-1 lead"><b>Данные объективного исследования: </b></p>
                    ' . (empty($pattern["objectData"]) ? "Не заполнено" : Helper::getShortenString($pattern["objectData"])) . '
                </div>
                <div class="col w-20">
                    <p class="card-text mb-1 lead"><b>Результаты специальных исследований: </b></p>
                    ' . (empty($pattern["specialResult"]) ? "Не заполнено" : Helper::getShortenString($pattern["specialResult"])) . '
                </div>
                <div class="col w-20">
                    <p class="card-text mb-1 lead mt-2"><b>Диагноз: </b></p>
                    ' . (empty($pattern["diagnosis"]) ? "Не заполнено" : Helper::getShortenString($pattern["diagnosis"])) . '
                </div>
            </div>';

        $result .= '</div></div>';
        return $result;
    }
}