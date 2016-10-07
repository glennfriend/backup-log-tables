<?php
/**
 * test get google sheet
 */
require_once __DIR__ . '/core/bootstrap.php';

// --------------------------------------------------------------------------------
//  start
// --------------------------------------------------------------------------------

$controller = new App\Shell\Home\Basic();
$controller->backup();
