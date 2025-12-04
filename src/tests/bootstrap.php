<?php

declare(strict_types=1);

// Try and recurse up to find openmage root dir, will recurse up to 10 dirs
for ($i = 0, $d = './'; ! file_exists($d.'app/Mage.php') && $i++ < 10; $d .= '../');

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath($d.'/app'));

if (!file_exists(APPLICATION_PATH . '/Mage.php')) {
    echo "Could not locate application directory containing Mage.php" . PHP_EOL;
    exit(1);
}

require_once APPLICATION_PATH . '/Mage.php';

// Update setting so for correct behaviour under test
$_SERVER['SCRIPT_NAME'] = 'index.php';
$_SERVER['SCRIPT_FILENAME'] = 'index.php';
// Global scope param to identify that the application is under test
$_SERVER['MAGE_TEST'] = true;
// Standard Magento configuration
$_SERVER['MAGE_IS_DEVELOPER_MODE'] = false;
$_SERVER['HTTP_HOST'] = 'localhost';

// Prevent session_start() from being called during tests
// (Magento's session handler skips startup if $_SESSION is already set)
$_SESSION = [];
session_id(uniqid());

require_once 'MageTest/autoload.php';

$transport = new Zend_Mail_Transport_File([
    'path' => BP . '/var/log/'
]);

Zend_Mail::setDefaultTransport($transport);

// Initialize Mage_Log in case of an exception during autoloading a class
Zend_Log::INFO;
