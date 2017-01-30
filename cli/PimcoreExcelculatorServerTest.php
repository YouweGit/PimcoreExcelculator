
<?php

require_once __DIR__ . '/bootstrap.php';

//this is optional, memory limit could be increased further (pimcore default is 1024M)
ini_set('memory_limit', '1024M');
ini_set("max_execution_time", "-1");

$time = microtime(true);
$memory = memory_get_usage();

//execute in admin mode
define("PIMCORE_ADMIN", true);

// Set time limit to indefinite execution
set_time_limit (0);

echo "Testing:\n";

// set some source values to the excel service
// get some return values from the excel service

$calcey = new \PimcoreExcelculator\PimcoreExcelculatorCalc('included-demo-file');
$calcey->set([
    'A3' => 420,
    'A4' => 246
]);

$results = $calcey->get(['A5']);

echo 'result: ' . var_export($results, true);

$calcey = new \PimcoreExcelculator\PimcoreExcelculatorCalc('included-demo-file');
$calcey->set([
    'A3' => 421,
    'A4' => 246
]);

$results2 = $calcey->get(['A5']);

echo 'result: ' . var_export($results2, true);

if($results['A5'] == 666 && $results2['A5'] == 667) {
    echo "\r\ntest result: success\r\n";
} else {
    echo "\r\ntest result: failed\r\n";
}








