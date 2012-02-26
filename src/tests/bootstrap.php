<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));

require_once APPLICATION_PATH.'/Mage.php';
// Update setting so for correct behaviour under test
$_SERVER['SCRIPT_NAME'] = 'index.php';
$_SERVER['SCRIPT_FILENAME'] = 'index.php';
$_SESSION = array();
session_id(uniqid());

// Global scope param to identfy that the application is under test
$_SERVER['MAGE_TEST'] = true;
// Standard Magento configuration
$_SERVER['MAGE_IS_DEVELOPER_MODE'] = true;