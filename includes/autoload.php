<?php

spl_autoload_register(function ($className) {
    $baseDir = __DIR__ . '/../';
    $directories = ['models', 'controllers', 'services'];
    
    foreach ($directories as $directory) {
        $file = $baseDir . $directory . '/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Загружаем конфиги
require_once __DIR__ . '/functions.php';


