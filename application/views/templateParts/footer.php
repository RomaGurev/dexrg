<? 
/*
Файл, подключаемый template.php.
> содержит разметку footer'а страницы.
*/
?>

<footer class="footer mt-auto py-3 bg-light border-top shadow-lg">
    <div class="container">
        <span class="badge bg-secondary">ВВК <?=Config::getValue('version')?> от <?=Config::getValue('versionDate')?></span> 
        
        <div class="float-end text-muted"><?=base64_decode("UkdOU0s=")?></div>
    </div>
</footer>