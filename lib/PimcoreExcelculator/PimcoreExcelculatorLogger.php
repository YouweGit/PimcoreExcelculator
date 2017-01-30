<?php

namespace PimcoreExcelculator;

class PimcoreExcelculatorLogger
{
    var $instance_id;

    public function __construct() {
        $this->instance_id = substr(uniqid(),0,5);
    }

    public function log($message) {

        // strip newlines
        $message = explode("\n", $message);
        $message = implode(' ' , $message);

        $entry = $this->instance_id . ' ' . $message;
        \Pimcore\Log\Simple::log('PimcoreExcelculator', $entry);
        echo $entry . "\r\n";
    }

}

