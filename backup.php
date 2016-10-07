<?php
/**
 * test get google sheet
 */
require_once __DIR__ . '/core/bootstrap.php';

// --------------------------------------------------------------------------------
//  start
// --------------------------------------------------------------------------------

if (in_array(getParam(0), ['show', 'yes'])) {
    $controller = new App\Shell\Home\Todo();
    $controller->perform();
}
else  {
    $controller = new App\Shell\Home\Preview();
    $controller->perform();
}
