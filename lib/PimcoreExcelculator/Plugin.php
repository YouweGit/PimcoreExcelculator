<?php

namespace PimcoreExcelculator;

use Pimcore\API\Plugin as PluginLib;

class Plugin extends PluginLib\AbstractPlugin implements PluginLib\PluginInterface
{

    /**
     * @var Zend_Translate
     */
    protected static $_translate;

    
    public function init() {

        parent::init();

        $cnf = self::getConfig();
    }

    public static function getConfig()
    {
        $customconfig_file = PIMCORE_CONFIGURATION_DIRECTORY . '/pimcore-excelculator-config.php';
        $defaultconfig_file = PIMCORE_PLUGINS_PATH . '/PimcoreExcelculator/pimcore-excelculator-config.default.php';

        if(file_exists($customconfig_file))
        {
            // weird patch necessary for specific servers that return
            // an integer instead of an array from the "require" function
            // every X json calls ?!?!
            $stuff = false;
            while (!is_array($stuff)) {
                $stuff = (require $customconfig_file);
            }
            return new \Zend_Config($stuff, true);
        }

        return new \Zend_Config((require $defaultconfig_file), true);
    }

    public static function install()
    {
        // implement your own logic here
        return true;
    }
    
    public static function uninstall()
    {
        // implement your own logic here
        return true;
    }

    public static function isInstalled()
    {
        // implement your own logic here
        return true;
    }

    /**
     * @return string
     */
    public static function getTranslationFileDirectory()
    {
        return PIMCORE_PLUGINS_PATH . '/PimcoreExcelculator/static/texts';
    }

    /**
     * @param string $language
     * @return string path to the translation file relative to plugin direcory
     */
    public static function getTranslationFile($language)
    {
        if (is_file(self::getTranslationFileDirectory() . "/$language.csv")) {
            return "/PimcoreExcelculator/static/texts/$language.csv";
        } else {
            return '/PimcoreExcelculator/static/texts/en.csv';
        }
    }

    /**
     * @return Zend_Translate
     */
    public static function getTranslate()
    {
        if(self::$_translate instanceof \Zend_Translate) {
            return self::$_translate;
        }

        try {
            $lang = \Zend_Registry::get('Zend_Locale')->getLanguage();
        } catch (\Exception $e) {
            $lang = 'en';
        }

        self::$_translate = new \Zend_Translate(
            'csv',
            PIMCORE_PLUGINS_PATH . self::getTranslationFile($lang),
            $lang,
            array('delimiter' => ',')
        );
        return self::$_translate;
    }

}
