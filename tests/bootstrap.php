<?php

/**
 * Basic PSR-0 autoloader, except that it always loads from the ../lib directory.
 * 
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 */
function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';

    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    // Always load classes from the ../lib directory
    $filePath = __DIR__ .'/../lib/'. $fileName;

    require $filePath;
}