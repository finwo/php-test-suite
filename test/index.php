<?php

// Initialize composer
require(__DIR__.'/../vendor/autoload.php');

// Define some stuff to make our life easier
if (!defined('DS'))      define('DS'     , DIRECTORY_SEPARATOR);
if (!defined('APPROOT')) define('APPROOT', rtrim(dirname(__DIR__), '/'));

// Run all tests in this folder
$_test_files = glob(__DIR__.DS.'*.php');
sort($_test_files);
foreach ($_test_files as $filename) {
    if ($filename == __FILE__) continue;
    require($filename);
}
