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
	<link rel="stylesheet" href="/bootstrap/bootstrap.css" />
	<link rel="stylesheet" href="/css/font-awesome.min.css" />
	<link rel="stylesheet" href="/css/main_style.css" />

	<title>
		<?= $title ?>
	</title>
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
	<script src="/js/jquery.autogrow-textarea.js" type="text/javascript"></script>
	<script src="/bootstrap/bootstrap.bundle.js" type="text/javascript"></script>
	<script src="/js/snippets.js" type="text/javascript"></script>
	<script src="/js/chart.js" type="text/javascript"></script>

	<script src="/js/custom_scripts/commonPage.js?ct=<? echo filemtime("js\\custom_scripts\\commonPage.js") ?>"
		type="text/javascript"></script>
	<? include 'application/core/custom-scripts.php' ?>
	<?
	if (isset($_GET["conscript"])) {
		$url = $_SERVER['REQUEST_URI'];
		$url = explode('?', $url);
		$url = $url[0];

		if ($url != "/document") {
			echo "<script>
					document.addEventListener('DOMContentLoaded', () => {
						openConscriptModal(" . $_GET["conscript"] . ");
					});
				  </script>";
		}
	}
	?>
</body>

</html>