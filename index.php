<?php

define('IN_TINY', true);
define('APP_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);


require_once(APP_ROOT . 'tiny/Tiny.class.php');
require_once(APP_ROOT . 'Cascade.php');

$one = new Tiny();
$one::execute();

?>
