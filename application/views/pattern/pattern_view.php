<?
/*
Файл, подключаемый controller_pattern.php.
> содержит разметку страницы /pattern.
*/
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 mb-3 col">Шаблоны</h3>
        <a href="/pattern/editor" class="btn btn-outline-success mb-3 col-auto">Добавить шаблон</a>
    </div>
    
    <input type="text" id="searchPatternInput" class="form-control mb-2" placeholder="Введите запрос...">

    <div id="searchResult" style="overflow: hidden;" class="mt-2">
        <div id="resizeDiv">
            <?
            if (count($data['userPatternList']) > 0) {
                foreach ($data['userPatternList'] as $value) {
                    echo DocumentBuilder::getPatternCard($value);
                } 
            } else {
                echo '<p class="lead mb-3 text-center">Список шаблонов пуст.</p>';
            }
            ?>
        </div>
    </div>
</div>