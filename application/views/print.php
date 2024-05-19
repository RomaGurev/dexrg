<?
/*
Файл, подключаемый view.php.
> содержит разметку страницы печати.
*/
?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="/bootstrap/bootstrap.css" />
	<link rel="stylesheet" href="/css/main_style.css?ct=<? echo filemtime("css\\main_style.css") ?>"/>
	<link rel="stylesheet" href="/css/print.css?ct=<? echo filemtime("css\\print.css") ?>" />

	<title><?= $title ?></title>
</head>

<body style="width: 840px; margin: 8px;">

	<div id="print_panel" class="p-3 bg-white rounded-3 border shadow" style="display: block;position: fixed;width: 840px; mb-5">
		<div class="d-flex">
			<a class="text-dark text-decoration-none d-flex">
				<svg width="36" height="36">
					<image xlink:href="/images/logo.svg" src="/images/logo.svg" width="36" height="36"></image>
				</svg>
				<p class="mb-0 ms-2 me-2" style="font-size: 1.5rem;">ВВК ПЕЧАТЬ</p>
			</a>
			<div class="m-auto w-25 d-flex me-2">
				<a onclick="<? if($_GET["template"] == "protocol") echo "saveProtocolValuesChanges(" . $_GET["id"] . ");" ?> window.print();" class="btn btn-outline-success w-100 align-self-end">Печать</a>
			</div>
			<div class="w-25 d-flex">
				<a onclick="history.back();" class="btn btn-outline-dark w-100 align-self-end">Назад</a>
			</div>
		</div>
		<div id="protocolChanges" <? if (!(count(Helper::getProtocolChanges($_GET["id"])) > 0 && $_GET["template"] == "protocol")) echo 'class="d-none"' ?>>
        	<div class="mt-3 mb-3" style="margin: 0 -1rem;border-top: 1px dashed #C0C0C0;"></div>
			<div class="d-flex">
				<div class="col align-self-center">
                	<p class="card-text mb-1 lead">Протокол содержит изменения, значения из документов (кроме статьи и категории годности) применяться не будут.</p>
				</div>
				<div class="col-auto align-self-center ms-3">
					<a onclick="openAreYouSureModal('Вы действительно хотите удалить сохраненные изменения протокола?', deleteProtocolValuesChanges, <? echo $_GET['id'] ?>);" class="btn btn-outline-danger w-100 align-self-middle">Очистить изменения</a>
				</div>
			</div>
        </div>
	</div>
	<?
	if($_GET["template"] == "protocol" && Helper::getInProccesStatus($_GET["id"])) {
		echo "<script>
			window.onafterprint = function () 
			{
				openAreYouSureModal('Печать была завершена. Добавить УКП в завершенные?', setInProcessFalse, " . $_GET["id"] . ");
        	}
			</script>";
	}
	?>


	<div id="print_content" class="print-padding <? if(count(Helper::getProtocolChanges($_GET["id"])) > 0 && $_GET["template"] == "protocol") echo "protocol-padding" ?>">
		<? echo $content_view; ?>
	</div>

	<!-- ADDITIONS -->
	<? include 'application/views/templateParts/additions.php' ?>
	<script src="/js/jquery-3.5.1.min.js" type="text/javascript"></script>
	<script src="/bootstrap/bootstrap.bundle.js" type="text/javascript"></script>
	<script src="/js/print.js?ct=<? echo filemtime("js\\print.js") ?>" type="text/javascript"></script>
	<script src="/js/petrovich.js" type="text/javascript"></script>
	<script src="/js/custom_scripts/commonPage.js?ct=<? echo filemtime("js\\custom_scripts\\commonPage.js") ?>"
		type="text/javascript"></script>
</body>

</html>