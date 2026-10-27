<?php
function autoLoad($class)
{
    $arPrefix = [
        'Classes\\' => $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/',
    ];
    foreach ($arPrefix as $prefix => $base_dir){
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
}