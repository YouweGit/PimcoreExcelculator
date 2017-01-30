<?php

namespace PimcoreExcelculator;

class PimcoreExcelculatorCalc
{
    var $cnf;
    var $values;
    var $file;

    public function __construct($file = null, $values = null) {
        $this->file = $file;
        $this->cnf = \PimcoreExcelculator\Plugin::getConfig()->toArray();
        if($values) $this->set($values);
    }

    public function set($values) {
        $this->values = $values;
    }

    public function get($fields) {
        if(!$this->file) {
            throw new \Exception('Get method called without having set a file');
        }
        // make a call with the
        // * file-reference
        // * values
        // * return fields wanted

        // =============

        return $this->makeRequest([
            'action'    => 'get',
            'file'      => $this->file,
            'input'     => $this->values,
            'output'    => $fields
        ]);
    }

    public function status() {
        return $this->makeRequest(['action' => 'status']);
    }

    public function stop() {
        return $this->makeRequest(['action' => 'stop']);
    }

    private function makeRequest($params)
    {
        $binding = 'unix:///tmp/excel.sock';
        $binding = $this->cnf['binding'] ?: $binding;

        $retryCounter = 10;
        $server = false;

        while ($server === false && $retryCounter--) {
            $server = @stream_socket_client($binding);
            if ($server === false) {
                // wait half a second before retrying
                usleep(500000);
            }
        }

        if (false === $server) {
            throw new \Exception('Could not listen', 888);
        }

        $client = $server;
        if (false !== $client) {

            $t = $params;

            $t = json_encode($t);
            fwrite($client, $t . "\r\n" . chr(0));
            fflush($client);

            $output = '';
//        while(!strstr($input,chr(0))) {
            $output .= fgets($client, 2048);
//        }
            $output = json_decode($output, true);
            return $output;
        }
    }
}

