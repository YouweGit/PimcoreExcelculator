<?php

namespace PimcoreExcelculator;

class PimcoreExcelculatorLogger
{
    var $instance_id;
    var $service;

    public function __construct($service) {
        $this->service = $service;
        $this->instance_id = substr(uniqid(),0,5);
    }

    public function log($message) {

        // strip newlines
        $message = explode("\n", $message);
        $message = implode(' ' , $message);

        $entry = $this->instance_id . ' ' . $message;
        \Pimcore\Log\Simple::log('PimcoreExcelculator', $entry);
        if ($this->service) {
            echo $entry . "\r\n";
        }
    }

}

