<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ecom/core/init.php';
unset($_SESSION['ECOMuser']);
header ('Location: login.php');
?>