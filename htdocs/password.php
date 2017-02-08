<?php


error_reporting(E_ALL);
ini_set('display_errors', '1');


$pass = $_GET['pass'];
require_once('../acl/User.class.php');

$hash = User::createPasswordHash($pass);

echo $hash;

?>