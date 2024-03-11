<?
/*
Файл, подключаемый view.php.
> содержит разметку страницы печати.
*/
?>

<!DOCTYPE html class="h-100">
<html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="/css/main_style.css" />
	<link rel="stylesheet" href="/bootstrap/bootstrap.css" />

	<title><?= $title ?></title>
</head>

<body class="d-flex flex-column h-100">
	<? print_r($data) ?>
	<script src="/js/jquery-3.5.1.min.js" type="text/javascript"></script>
</body>

</html>