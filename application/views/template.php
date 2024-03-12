<?
/*
Файл, подключаемый view.php.
> содержит основную разметку страницы.
*/
?>

<!DOCTYPE html class="h-100">
<html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="icon" href="/images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="/css/main_style.css" />
	<link rel="stylesheet" href="/bootstrap/bootstrap.css" />

	<title><?= $title ?></title>
</head>

<body class="d-flex flex-column h-100">
	<!-- HEADER -->
	<? include 'application/views/templateParts/header.php' ?>
	<!-- ОСНОВНОЙ КОНТЕНТ -->
	<? include 'application/views/templateParts/content.php' ?>
	<!-- FOOTER -->
	<? include 'application/views/templateParts/footer.php' ?>
	<!-- ADDITIONS -->
	<? include 'application/views/templateParts/additions.php' ?>

	<script src="/js/jquery-3.5.1.min.js" type="text/javascript"></script>
	<script src="/bootstrap/bootstrap.bundle.js" type="text/javascript"></script>
	<script src="/js/snippets.js" type="text/javascript"></script>
	<script src="/js/chart.js" type="text/javascript"></script>
	<script src="/js/doubleScroll.js" type="text/javascript"></script>
	
	<script src="/js/custom_scripts/custom-scripts.js" type="text/javascript"></script>
	<? include 'application/core/custom-scripts.php' ?>

</body>

</html>