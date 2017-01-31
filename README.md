PIMCORE EXCEL EXTENSION
----------------------------

Version: Pimcore 4.x
 
Note: NOT compatible with Pimcore versions under 4

Developed by: Youwe (Roelf)




Excerpt
-------

* Always wanted to perform extensive calculations simply using excel sheets?
* Would you like a service in the background to make sure the excel sheets are ready to calculate when you need it?

... then this extension is for you!



Description
-----------

The pimcore deployment extension as the following general functionalities:

* Provide results of excel calculations as long as the service is running



Usage and examples
------------------

Enable the plugin in Pimcore!

Run the service / make sure its running when your server boots up:

    php ./plugins/PimcoreExcelculator/cli/PimcoreExcelculatorServer.php

Try the test to make sure the service works:

    php ./plugins/PimcoreExcelculator/cli/PimcoreExcelculatorServerTest.php

Configure your own excel files (and socket-binding) to perform calculations with:

    cp ./plugins/PimcoreExcelculator/pimcore-excel-config.default.php ./website/var/config/pimcore-excel-config.php
    vi ./website/var/config/pimcore-excel-config.php



Example code without error handling
-----------------------------------

    // simple example

    $calcey = new \PimcoreExcelculator\PimcoreExcelculatorCalc('included-demo-file');
    $calcey->set([
        'A3' => 420,
        'A4' => 246
    ]);
    
    $results = $calcey->get(['A5']);
    
    echo 'result: ' . var_export($results, true);



Example code with error handling
--------------------------------

    // you want to catch possible errors, because this functionality
    // depends on a service that should be running in the background,
    // and there is always the possibility that the service is busy
    // or has not been started, or has crashed

    $calcey = new \PimcoreExcelculator\PimcoreExcelculatorCalc('included-demo-file');
    $calcey->set([
        'A3' => 420,
        'A4' => 246
    ]);
    
    try {
        $results = $calcey->get(['A5']);
    }
    catch (\Throwable $t) {
        if($t->getCode() == 888) {
            // could not connect to the service!
        } 
        else {
            // something else is wrong  ( check $t->getMessage() )
        }
    }
    
    echo 'result: ' . var_export($results, true);


Example code with error handling and different sheets in one file
-----------------------------------------------------------------

    $calcey = new \PimcoreExcelculator\PimcoreExcelculatorCalc('some-not-included-file');
    $calcey->set([
        'sheetOne.A3' => 420,
        'sheetTwo.A4' => 246
    ]);
    
    try {
        $results = $calcey->get(['sheetThree.A5', 'sheetFour.C6']);
    }
    catch (\Throwable $t) {
        if($t->getCode() == 888) {
            // could not connect to the service!
        } 
        else {
            // something else is wrong  ( check $t->getMessage() )
        }
    }
    
    echo 'result: ' . var_export($results, true);



Deployment to server
--------------------

Run the service / make sure its running when your server boots up:

    php ./plugins/PimcoreExcelculator/cli/PimcoreExcelculatorServer.php

You can do this, for example, using a cron job:

    */1 * * * * flock -n /tmp/pimcore.excel.lockfile -c "/usr/bin/php /var/www/html/plugins/PimcoreExcelculator/cli/PimcoreExcelculatorServer.php"


Troubleshooting
---------------

Before importing the definitions, you might need to set the correct permissions, in order for this script to be able to
write to the definition files. In case of local development, a low security solution like the following could be used:

    sudo chmod -R 777 .
    
Make sure you have your local apache running as root or your user, or it might not be able to connect to the service:

On macOS/OSX:

    in httpd.conf
    
    User my-username
    Group _www
    
    or
    
    User root
    Group _www



Installation  
------------

Plugin can be installed through composer. Add json to your composer.json:

    composer require youwe/pimcore-excelculator

Activate/enable the plugin in pimcore's extras->extensions list.

Also, add this to your .gitignore:

    /plugins/PimcoreExcelculator
    
    
 
Plugin development
------------------

To create a new version, check out the master branch somewhere and go:

    git tag
    git tag 0.x.x         (minor update = latest tag + 0.0.1)
    git push origin --tags


