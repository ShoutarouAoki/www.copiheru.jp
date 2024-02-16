<?php
setcookie('PHPSESSID', time(), time() + 2592000);
header('Location: ' . $_GET['callback_url']);
//mail("takai@k-arat.co.jp","test","cookie","From:info@mailanime.net");
exit();
?>