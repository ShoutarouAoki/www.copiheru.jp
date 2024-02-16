<?php
require_once(dirname(__FILE__)."/autoload.php");
session_start();
$github_keys = require(dirname(__FILE__)."/app-keys.php");
$provider = new League\OAuth2\Client\Provider\Github([
    'clientId'          => $github_keys['clientId'],
    'clientSecret'      => $github_keys['clientSecret'],
]);

$title = "PHP GitHub Login Sample";
?>