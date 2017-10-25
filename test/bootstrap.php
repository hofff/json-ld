<?php

date_default_timezone_set('UTC');

Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

return require __DIR__.'/../vendor/autoload.php';
