<?php

// spl_autoload_register(function ($classname) {
//     if (str_ends_with($classname, 'Controller')) {
//         $classname = substr($classname, 0, -10);
//     }
//     $filename = "../app/models/" . ucfirst($classname) . ".php";
//     if (file_exists($filename)) {
//         require_once $filename;
//     }
// });

spl_autoload_register(function ($classname) {
    $paths = [
        "./app/models/",
        "./app/controllers/",
        "./app/core/",
    ];

    foreach ($paths as $path) {
        $file = $path . ucfirst($classname) . ".php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require 'App.php';
require 'config.php';
require 'Flash.php';
require 'Database.php';
require 'Model.php';
require 'functions.php';
require 'Controller.php';
require './vendor/autoload.php';
