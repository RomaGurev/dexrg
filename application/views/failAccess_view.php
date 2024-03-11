<?
/*
Файл, подключаемый контроллерами страниц в случае отсутствия доступа к контенту.
> содержит разметку страницы Ошибки доступа.
*/
?>

<div class="p-4 align-items-center rounded-3 border shadow d-flex">
    <div class="col-3">
        <svg class="m-auto" width="100%" height="20%">
            <image xlink:href="/images/icons/info-circle.svg" width="100%" height="100%" />
        </svg>
    </div>
    <div class="col-9 ps-3">
        <h3 class="display-6 lh-1 mb-3">Ошибка доступа</h3>
        <p class="lead">
            Вы не имеете прав для доступа к данной странице.<br> В случае проблем, обратитесь в 117 кабинет.
        </p>
    </div>
</div>