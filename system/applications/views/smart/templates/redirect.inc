<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta name="description" content="" />
<title><?php print(SITE_NAME); ?></title>
<script type="text/javascript">
function redirectTop()
{
	top.location.href="<?php print($return_path); ?>";
}
</script>
</head>
<body onload="redirectTop();"></body>
</html>