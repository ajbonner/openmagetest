<?php
// Include class dependencies that can not loaded by the autoloader
// when running tests outside a Magento project
// Include Magento from the location on your machine
// TODO Change to include Mage.php adding it to the include path making to project portable
require_once '/mnt/Sites/magento.development.local/public/app/Mage.php'; 

$includePath = array(
    __DIR__,
    __DIR__ . '/src/app/code/community/',
    __DIR__ . '/src/lib/',
    get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $includePath));

spl_autoload_register(function ($class) {
    $file = str_replace('_', '/', $class) . '.php';
    require_once $file;
});