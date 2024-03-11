<?
/*
Файл, подключаемый template.php.
> содержит разметку основной части страницы.
> подключает разметку $content_view, на которую ссылается контроллер страницы.
*/
?>


<main class="mt-4 mb-4">
    <div class="container">
        <div id="alertResult" class="d-none"></div>

        <div class="loading-position d-flex p-2 border shadow d-none" id="spinner">
            <div class="spinner-border text-primary me-3" role="status">
                <span class="visually-hidden"></span>
            </div>
            <p class="lead mb-0"> Загрузка...</p>
        </div>

        <? include 'application/views/' . $content_view; ?>
    </div>
</main>