<?php

namespace TheArtOfLogic\DoctrineTreeExtension\Annotation;

use Doctrine\Common\Annotations\AnnotationRegistry;

class Loader
{
    public static function registerAnnotations()
    {
        AnnotationRegistry::registerFile(__DIR__ .'/All.php');
    }
}