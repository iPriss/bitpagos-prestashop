<?php

$_POST['id_shop']  = 1;
$_POST['fc']  = 'module';
$_POST['controller']  = 'bitpagos';
$_POST['module']  = 'bitpagos';
require(dirname(__FILE__).'/config/config.inc.php');
Dispatcher::getInstance()->dispatch();

?>