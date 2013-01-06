<?php

/**
 * If it does not find its own autoloader,
 * use the autoloader from the application that the module is coupled.
 */

if (@!include __DIR__ . '/../vendor/autoload.php') {
    require __DIR__ . '/../../../vendor/autoload.php';
}
