<?php
return [
    'binding' => 'unix:///tmp/excel.sock',
    'files' =>
        [
            // leave the demo file in when copying this file if you want the plugin test to work
            'included-demo-file'   =>  PIMCORE_PLUGINS_PATH . '/PimcoreExcelculator/data/pimcore-excelculator-demo.xlsx'
        ]
];
