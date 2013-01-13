<?php

// Make sure errors are output
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;

// Load the default autoloader
$loader = require __DIR__ .'/../vendor/autoload.php';

// Add test namespace to the loader
$loader->add('TheArtOfLogic\DoctrineTreeExtensionTest', __DIR__);

// Register default doctrine annotations
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// Register custom annotations
TheArtOfLogic\DoctrineTreeExtension\Annotation\Loader::registerAnnotations();

// Initialize the global annotation reader
$annotationReader = new AnnotationReader();
$annotationReader = new CachedReader($annotationReader, new ArrayCache());
$GLOBALS['annotationReader'] = $annotationReader;