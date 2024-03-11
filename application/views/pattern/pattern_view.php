<?
/*
Файл, подключаемый controller_pattern.php.
> содержит разметку страницы /pattern.
*/
?>

<div class="p-4 align-items-center rounded-3 border shadow">
    <div class="d-flex">
        <h3 class="display-6 lh-1 mb-3 col-9 col-lg-10">Шаблоны</h3>
        <a href="/pattern/editor" class="btn btn-outline-success mb-3 float-end col-3 col-lg-2">Добавить шаблон</a>
    </div>

    
    <?
    $patternList = $data['userPatternList'];
    if (count($patternList) > 0) {
        ?>

        <table class="table table-striped table-bordered table-hover">
            <thead class="text-center">
                <tr>
                    <th scope="col" class="lead">Название шаблона</th>
                    <th scope="col" class="lead col-7">Краткое содержание</th>
                    <th scope="col" class="lead">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?
                for ($i = 0; $i < count($patternList); $i++) {
                    $id = $patternList[$i]["id"];
                    $name = $patternList[$i]["name"];
                    $diagnosis = $patternList[$i]["diagnosis"];

                    echo "<tr>
                    <td class='lead fs-6 text-center'>$name</td>
                    <td class='lead fs-6'>Диагноз: $diagnosis</td>
                    <th scope='row' class='text-center'>
                        <button onclick='editPattern($id)' class='btn btn-outline-dark w-75 mb-1'>Редактировать</button>
                        <button onclick='deletePattern($id)' class='btn btn-outline-danger w-75 text-center'>Удалить</button>
                    </th>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    <?
    } else {
        echo '<p class="lead mb-3 text-center">Список шаблонов пуст.</p>';
    }
    ?>
</div>