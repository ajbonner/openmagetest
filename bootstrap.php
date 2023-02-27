<?php

$includePath = [
    __DIR__,
    __DIR__ . '/app/code/community/',
    __DIR__ . '/app/',
    __DIR__ . '/lib/',
    __DIR__ . '/src/',
    __DIR__ . '/src/app/code/community/',
    __DIR__ . '/src/app/',
    __DIR__ . '/src/lib',
    get_include_path()
];
set_include_path(implode(PATH_SEPARATOR, $includePath));
require_once 'vendor/autoload.php';
//require_once 'lib/MageTest/autoload.php';
