<?php
    $path = realpath(__DIR__ . '/../');
    define('APP_PATH', $path);
    $app = new Yaf_Application(APP_PATH . '/conf/application.ini');
    $app->run();