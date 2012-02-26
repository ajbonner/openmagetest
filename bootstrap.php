<?php
$includePath = array(
    __DIR__,
    __DIR__ . '/src/app/code/community/',
    __DIR__ . '/src/lib/',
    __DIR__ . '/vendor/magento/app',
    get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $includePath));
require_once 'src/lib/MageTest/autoload.php';