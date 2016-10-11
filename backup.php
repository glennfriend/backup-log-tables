<?php
/**
 * test get google sheet
 */
require_once __DIR__ . '/core/bootstrap.php';

// --------------------------------------------------------------------------------
//  start
// --------------------------------------------------------------------------------

if (in_array(getParam(0), ['debug', 'yes'])) {
    $controller = new App\Shell\Home\Todo();
    $controller->perform();
}
elseif (getParam(0)) {
    echo "找不到這個參數";
    echo "\n";
}
else  {
    $controller = new App\Shell\Home\Preview();
    $controller->perform();
}
