<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta name="description" content="" />
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="robots" content="noindex,nofollow" />
<title><?php print(SITE_NAME); ?></title>
<link href="/common/css/smart/reset.css?<?php print(date("YmdHis")); ?>" rel="stylesheet" type="text/css" />
<link href="/common/css/smart/layout.css?<?php print(date("YmdHis")); ?>" rel="stylesheet" type="text/css" />
<link href="/common/css/smart/style.css?<?php print(date("YmdHis")); ?>" rel="stylesheet" type="text/css" />
<link href="/common/css/smart/option.css?<?php print(date("YmdHis")); ?>" rel="stylesheet" type="text/css" />
<?php if($device_type == "iPhone"){ ?>
<link href="/common/css/smart/iphone.css?<?php print(date("YmdHis")); ?>" rel="stylesheet" type="text/css" />
<?php }else{ ?>
<link href="/common/css/smart/android.css?<?php print(date("YmdHis")); ?>" rel="stylesheet" type="text/css" />
<?php } ?>
<?php if(defined("SYSTEM_CHECK")){ ?>
<link href="/common/css/<?php print($default_device); ?>/debug.css" rel="stylesheet" type="text/css" />
<? } ?>
<?php if($default_device == "pc"){  ?>
<link href="/common/css/smart/preview.css" rel="stylesheet" type="text/css" />
<?php } ?>


<script src="https://spgm.nijiyome.jp/js/touch.js" type="text/javascript"></script>
<script src="/common/js/jquery/jquery.min.js" type="text/javascript"></script>

<script src="/common/js/jquery/jquery.easing.1.3.js"></script>
<script src="/common/js/jquery/jquery.smoothScroll.js"></script>
<script src="/common/js/script.js" type="text/javascript"></script>
<script src="/common/js/common.js" type="text/javascript"></script>
<script src="/common/js/cookie.js" type="text/javascript"></script>
<script src="/common/js/modernizr/modernizr.custom.js"></script>

<script src="/plugins/jquery-ui/jquery-ui.min.js" ></script>
<link href="/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" media="all" />
<link href="/plugins/jquery-ui/jquery-ui.theme.css" rel="stylesheet" type="text/css" media="all" />
<link href="/plugins/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet" type="text/css" media="all" />


</head>
<body class="cbp-spmenu-push">
<div id="debug-comment">DEBUG SERVER</div>
