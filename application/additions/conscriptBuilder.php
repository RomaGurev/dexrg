<?
class ConscriptBuilder
{

    public static function getConscriptCard($conscript, $showSelectButton)
    {
        $result = '<div class="card">
        <div class="card-header lead">
        <b><a ' . ($showSelectButton == "true" ? 'onclick="select(' . $conscript['id'] . ', \'' . $conscript['name'] . (!empty($conscript['birthDate']) ? ' [' . Helper::formatDateToView($conscript['birthDate']) . ']' : '') . '\')"' : 'onclick="openConscriptModal(' . $conscript['id'] . ')"') . 'style="cursor: pointer;">' . $conscript["name"] . '</a></b> ' . (!empty($conscript['birthDate']) ? '[' . Helper::formatDateToView($conscript['birthDate']) . ']' : '') . '
        </div>
        <div class="card-body d-flex" style="padding: 0.25rem 1rem">
            <div class="col">
                <p class="card-text mb-1 lead"><b>Военный комиссариат: </b>' . (!empty($conscript['vk']) ? Helper::getVKNameById($conscript['vk'])["name"] : 'Нет информации'). '</p>
                <p class="card-text mb-1 lead"><b>Период призыва: </b>' . Helper::convertAdventPeriodToString($conscript["adventPeriod"]) . '</p>
            </div>
            <div class="col">
                <p class="card-text mb-1 lead"><b>Статья РВК: </b>' . (!empty($conscript['rvkArticle']) ? $conscript["rvkArticle"] : 'Нет информации') . '</p>
                <p class="card-text mb-1 lead"><b>Категория годности: </b>' . (!empty($conscript['healthCategory']) ? $conscript["healthCategory"] : 'Нет информации') . '</p>
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

    public static function getConscriptModalInfo($conscript, $changeCategoryInfo = null, $complaintInfo = null, $returnInfo = null) {

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

        //Добавление в массив документов о изменении категории
        foreach ($changeCategoryInfo as $changeCategory) {
            $authorProfile = Helper::getProfileByUserID($changeCategory['creatorID']);
            array_push($documents, [
                "authorPosition" => Config::getValue("userType")[$authorProfile["position"]][0],
                "authorName" => $authorProfile["name"],
                "documentType" => "Изменение категории " . $conscript['rvkArticle'] . "→" . $changeCategory['article'],
                "documentDate" => "",
                "documentLink" => "#"
            ]);
        }
        //Добавление в массив документов о изменении категории

        //Вывод результатов в переменную result
        $result .= '
        <div class="d-flex">
            <div class="col">
                <p class="card-text mb-1 lead"><b>ФИО: </b>' . $conscript['name'] . '</p>
                <p class="card-text mb-1 lead"><b>Дата рождения: </b>' . (!empty($conscript['birthDate']) ? Helper::formatDateToView($conscript['birthDate']) : 'Информация не указана'). '</p>
                <p class="card-text mb-1 lead"><b>Уникальный номер: </b>' . $conscript['id'] . '</p>
                <p class="card-text mb-1 lead"><b>Военный комиссариат: </b>' . (!empty($conscript['vk']) ? Helper::getVKNameById($conscript['vk'])["name"] : 'Информация не указана'). '</p>
                <p class="card-text mb-1 lead"><b>Период призыва: </b>' . Helper::convertAdventPeriodToString($conscript["adventPeriod"]) . '</p>
                <p class="card-text mb-1 lead"><b>Статья РВК: </b>' . (!empty($conscript['rvkArticle']) ? $conscript["rvkArticle"] : 'Информация не указана') . '</p>
                <p class="card-text mb-1 lead"><b>Категория годности: </b>' . (!empty($conscript['healthCategory']) ? $conscript["healthCategory"] : 'Информация не указана') . '</p>
            </div>

            <div class="col">
                <div class="d-flex gap-2">
                    <button type="button" onclick="addChangeCategory(' . $conscript['id'] . ');" class="btn btn-outline-primary flex-fill">Изменить категорию</button>
                    <button type="button" class="btn btn-outline-primary flex-fill">Добавить возврат</button>
                    <button type="button" class="btn btn-outline-primary flex-fill">Добавить жалобу</button>
                </div>
        
                <h3 class="card-text mb-1 lead mt-3"><b>История изменений:</b></h3>

                <div class="documentLine mt-2">';

        foreach ($documents as $document) {
            $result .= '<div class="documentItem">
                        <div class="point"></div>
                        <a href="' . $document["documentLink"] . '">' . $document["documentType"] . ' ' . $document["documentDate"] . ' <span style="color: black;" data-toggle="tooltip" title="' . $document["authorName"] . '"> [' . $document["authorPosition"] . ']</span></a>
                    </div>';
        }

        $result .= '             
                </div>
            </div>
        </div>
        ';

        $result .= '
        <div class="d-flex mt-2">
            <div class="col">
                <button type="button" onclick="editConscript(' . $conscript['id'] . ');" class="btn btn-outline-dark">Редактировать УКП</button>
                <button type="button" onclick="deleteConscript(' . $conscript['id'] . ');" class="btn btn-outline-danger">Удалить УКП</button>
            </div>
            <div class="col-auto">
                <button type="button" onclick="printProtocol(' . $conscript['id'] . ');" class="btn btn-primary">Печать (протокол)</button>
            </div>
        </div>
        ';

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