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
	<link rel="stylesheet" href="/css/main_style.css" />
	<link rel="stylesheet" href="/bootstrap/bootstrap.css" />

	<title><?= $title ?></title>
</head>

<body style="width: 840px; margin: 8px;">

	<div id="print_panel" class="p-2 bg-white rounded-3 border shadow" style="display: block;position: fixed;width: 840px;">
		<div class="d-flex">
			<a class="text-dark text-decoration-none d-flex">
				<svg width="36" height="36">
					<image xlink:href="/images/logo.svg" src="/images/logo.svg" width="36" height="36"></image>
				</svg>
				<p class="mb-0 ms-2 me-2" style="font-size: 1.5rem;">ВВК - ПЕЧАТЬ</p>
			</a>
			<div class="m-auto w-25 d-flex me-2">
				<a onclick="document.getElementById('print_panel').style.display='none'; window.print(); document.getElementById('print_panel').style.display='block';" class="btn btn-outline-success w-100 align-self-end">Печать</a>
			</div>
			<div class="w-25 d-flex">
				<a onclick="history.back()" class="btn btn-outline-dark w-100 align-self-end">Назад</a>
			</div>
		</div>
	</div>

	<? print_r($data) ?>

	<? echo $content_view; ?>
	<script src="/js/jquery-3.5.1.min.js" type="text/javascript"></script>
</body>

</html>