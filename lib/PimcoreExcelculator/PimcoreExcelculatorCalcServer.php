<?php

namespace PimcoreExcelculator;

class PimcoreExcelculatorCalcServer
{
    var $cnf;
    var $values;
    var $files;
    var $logger;

    var $service;

    public function __construct($service = true) {
        $this->service = $service;
        $this->logger = new PimcoreExcelculatorLogger($service);
        $this->logger->log('Server class initializing, memory limit: ' . ini_get('memory_limit'));
        $this->logger->log('Loading config');
        $this->cnf = \PimcoreExcelculator\Plugin::getConfig()->toArray();
        $this->logger->log('Loaded config: ' . var_export($this->cnf, true));
        $this->loadfiles();
    }

    private function loadfiles() {
        $this->logger->log('About to load ' . count($this->cnf['files']) . ' excel files');
        foreach($this->cnf['files'] as $label => $file) {
            $this->logger->log('Loading start: ' . $label . ' (' . $file . ')');
            if(!file_exists($file) || !is_readable($file)) {
                $errmsg = 'File does not exist or is not readable (' . $file . ')';
                $this->logger->log($errmsg);
                throw new \Exception($errmsg, 800);
            }
            $this->files[$label] = \PHPExcel_IOFactory::load($file);
            $this->logger->log('Loading complete: ' . $label . ' (' . $file . ')');
        }
    }

    public function get($fileLabel, $input, $output)
    {
        $this->logger->log('Processing request for ' . $fileLabel . ' start');
        $objPHPExcel = $this->files[$fileLabel];
        foreach ($input as $cell => $value) {
            // make the fields formatted like [sheetname].[field] work as well
            if(strstr($cell, '.') !== FALSE) {
                list($sheet, $cell) = explode('.', $cell);
                $objPHPExcel->setActiveSheetIndexByName($sheet);
            }
            $objPHPExcel->getActiveSheet()->SetCellValue($cell, $value);
        }
        $result = [];
        \PHPExcel_Calculation::getInstance($objPHPExcel)->disableCalculationCache();
        foreach($output as $outputcell) {
            // make the fields formatted like [sheetname].[field] work as well
            if(strstr($outputcell, '.') !== FALSE) {
                list($outputsheet, $outputcell) = explode('.', $outputcell);
                $objPHPExcel->setActiveSheetIndexByName($outputsheet);
            }
            $result[$outputcell] = $objPHPExcel->getActiveSheet()->getCell($outputcell)->getCalculatedValue();
        }
        $this->logger->log('Processing request for ' . $fileLabel . ' complete ' . var_export($result, true));
        return $result;
    }

    public function status()
    {
        $this->logger->log('Processing status request');
        $filesCount = count($this->files);
        $result = [
            'status'        => true,
            'statusString'  => 'Service running. Files loaded: ' . $filesCount
        ];
        $this->logger->log('Processing status request complete: ' . var_export($result, true));
        return $result;
    }

}

