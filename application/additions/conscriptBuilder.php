<?
class ConscriptBuilder
{

    public static function getConscriptCard($conscript)
    {
        $result = '<div class="card">
        <div class="card-header lead">
        <b>' . $conscript["name"] . '</b> ' . (!empty($conscript['birthDate']) ? '[' . Helper::formatDateToView($conscript['birthDate']) . ']' : '') . '
        </div>
        <div class="card-body d-flex">
            <div class="col">
                <p class="card-text mb-1 lead"><b>Военный комиссариат: </b>' . (!empty($conscript['vk']) ? Helper::getVKNameById($conscript['vk'])["name"] : 'Нет информации'). '</p>
                <p class="card-text mb-1 lead"><b>Период призыва: </b>' . Helper::convertAdventPeriodToString($conscript["adventPeriod"]) . '</p>
            </div>
            <div class="col">
                <p class="card-text mb-1 lead"><b>Статья РВК: </b>' . (!empty($conscript['rvkArticle']) ? $conscript["rvkArticle"] : 'Нет информации') . '</p>
                <p class="card-text mb-1 lead"><b>Категория годности: </b>' . (!empty($conscript['healthCategory']) ? $conscript["healthCategory"] : 'Нет информации') . '</p>
            </div>
            <div class="col-auto d-flex">
                <button type="button" data-bs-toggle="modal" data-bs-target="#RGModal" class="btn btn-outline-primary" style="align-self: center;">Открыть УКП</button>
            </div>
        </div>
      </div>';
        return $result;
    }

}