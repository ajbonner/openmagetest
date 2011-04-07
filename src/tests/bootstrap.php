<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));
    
$_SERVER['MAGE_TEST'] = true;

require_once APPLICATION_PATH.'/Mage.php';