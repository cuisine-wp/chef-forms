<?php
/**
 * Plugin Name: Cuisine Forms
 * Plugin URI: https://get-cuisine.cooking/forms
 * Description: Create easy-to-use forms in seconds
 * Version: 3.0.0
 * Author: Luc Princen
 * Author URI: https://get-cuisine.cooking/
 * License: GPLv2
 *
 * Text Domain: cuisineforms
 * Domain Path: /Languages/
 *
 * @package CuisineForms
 * @category Core
 * @author Cuisine
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
defined('DS') ? DS : define('DS', DIRECTORY_SEPARATOR);


/**
 * Main class that bootstraps the framework.
 */
if (!class_exists('CuisineForms')) {

    class CuisineForms {

        /**
         * CuisineForms bootstrap instance.
         *
         * @var \CuisineForms
         */
        private static $instance = null;


        /**
         * Plugin directory name.
         *
         * @var string
         */
        private static $dirName = '';



        private function __construct(){

            static::$dirName = static::setDirName(__DIR__);

            // Load plugin.
            $this->load();
        }


        /**
         * Load the chef forms classes.
         *
         * @return void
         */
        private function load(){

            //load text-domain:
            $path = dirname( plugin_basename( __FILE__ ) ).'/Languages/';
            load_plugin_textdomain( 'cuisineforms', false, $path );

            //require the autoloader:
            require( __DIR__ . DS . 'autoloader.php');

            //initiate the autoloader:
            ( new \CuisineForms\Autoloader() )->register()->load();

            //new-up a deprecated class, to catch old filters & hooks:
            new \CuisineForms\Deprecated();

            do_action( 'cuisine_forms_loaded' );

        }




        /*=============================================================*/
        /**             Getters & Setters                              */
        /*=============================================================*/


        /**
         * Init the forms classes
         *
         * @return \CuisineForms
         */
        public static function getInstance(){

            if ( is_null( static::$instance ) ){
                static::$instance = new static();
            }
            return static::$instance;
        }

        /**
         * Set the plugin directory property. This property
         * is used as 'key' in order to retrieve the plugins
         * informations.
         *
         * @param string
         * @return string
         */
        private static function setDirName($path) {

            $parent = static::getParentDirectoryName(dirname($path));

            $dirName = explode($parent, $path);
            $dirName = substr($dirName[1], 1);

            return $dirName;
        }

        /**
         * Check if the plugin is inside the 'mu-plugins'
         * or 'plugin' directory.
         *
         * @param string $path
         * @return string
         */
        private static function getParentDirectoryName($path) {

            // Check if in the 'mu-plugins' directory.
            if (WPMU_PLUGIN_DIR === $path) {
                return 'mu-plugins';

            }

            // Install as a classic plugin.
            return 'plugins';
        }


        public static function getPluginPath(){
        	return __DIR__.DS;
        }

        /**
         * Returns the directory name.
         *
         * @return string
         */
        public static function getDirName(){
            return static::$dirName;
        }

    }
}


/**
 * Load the main class.
 *
 */
add_action('cuisine_loaded', function(){

	CuisineForms::getInstance();

}, 0, 400 );