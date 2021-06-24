<?php

if (defined('VENDOR_PATH')) {
    $vendorDir = require VENDOR_PATH;
} else {
    $vendorDir = require __DIR__ . '/../../../../app/etc/vendor_path.php';
}
$vendorAutoload = __DIR__ . "/../../../../{$vendorDir}/autoload.php";

if (file_exists($vendorAutoload)) {
    $composerAutoloader = include $vendorAutoload;
    $libDirectory = dirname(dirname(__DIR__));
// set the Lib folder path to saferpay nameSpace in Composer Auto loader
    $composerAutoloader->set('Pits', $libDirectory);
}else {
    throw new \Exception(
        'Vendor autoload is not found. Please run \'composer install\' under application root directory.'
    );
}
