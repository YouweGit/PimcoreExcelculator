<?php

require_once __DIR__ . '/bootstrap.php';

//this is optional, memory limit could be increased further (pimcore default is 1024M)
ini_set('memory_limit', '3048M');
ini_set("max_execution_time", "-1");

$time = microtime(true);
$memory = memory_get_usage();

// execute in admin mode
define("PIMCORE_ADMIN", true);

// remove the socket file from last abort/crashed run
unlink('/tmp/excel.sock');

$_ = $_SERVER['_'];
function onShutdown() {
    global $_, $argv;
    echo "I am restarting...\n";
    pcntl_exec($_, $argv);
}
// restart is done by the cron job running every minute.
// register_shutdown_function(onShutdown);

echo "PimcoreExcelculator plugin server\n";

$calcServer = new \PimcoreExcelculator\PimcoreExcelculatorCalcServer();

$calcServer->logger->log('PimcoreExcelculatorPlugin service listener starting');

$config = $calcServer->cnf;

$binding = 'unix:///tmp/excel.sock';
$binding = $config['binding']?:$binding;

$server = stream_socket_server($binding);
if (false === $server) {
    throw new \Exception('Could not listen');
}
$calcServer->logger->log('PimcoreExcelculatorPlugin service listener started');

$memory_reporting = 0;

while (true) {
    if($memory_reporting-- <= 0) {
        $memory_reporting = 33;
        $mem_pure = memory_get_usage() / (1024 * 1024);
        $mem_real = memory_get_usage(true) / (1024 * 1024);
        $mem_peak = memory_get_peak_usage() / (1024 * 1024);
        $mem_peak_real = memory_get_peak_usage(true) / (1024 * 1024);
        $calcServer->logger->log(sprintf(
            'Memory usage: pure %01.2f real %01.2f peak %01.2f realpeak %01.2f',
            $mem_pure, $mem_real, $mem_peak, $mem_peak_real));
    }
    $client = stream_socket_accept($server, -1);
    if (false !== $client) {
        $input = '';
//        while(!strstr($input,chr(0))) {
            $input .= fgets($client, 2048);
//        }
        echo 'Received request [' . $input . ']';
        $input = json_decode($input, true);

        if($input['action'] == 'get') {
            $return = $calcServer->get($input['file'], $input['input'], $input['output']);
        }
        elseif($input['action'] == 'status') {
            $return = $calcServer->status();
        }
        elseif($input['action'] == 'stop') {
            throw new \Exception('Excel service stopped by client', 887);
        }

        fwrite($client, json_encode($return). "\r\n" . chr(0));
        fflush($client);
    }
}
