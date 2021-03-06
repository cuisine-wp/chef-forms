<?php
/**
 * Plugin Name: Chef Forms
 * Plugin URI: http://chefduweb.nl/plugins/chef-forms
 * Description: Create easy-to-use forms in seconds
 * Version: 2.2.0
 * Author: Luc Princen
 * Author URI: http://www.chefduweb.nl/
 * License: GPLv2
 * Bitbucket Plugin URI: https://bitbucket.org/chefduweb/chef-forms
 * Bitbucket Branch:     master
 *
 * Text Domain: chefforms
 * Domain Path: /Languages/
 *
 * @package ChefForms
 * @category Core
 * @author Chef du Web
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
defined('DS') ? DS : define('DS', DIRECTORY_SEPARATOR);


/**
 * Main class that bootstraps the framework.
 */
if (!class_exists('ChefForms')) {

    class ChefForms {

        /**
         * ChefForms bootstrap instance.
         *
         * @var \ChefForms
         */
        private static $instance = null;

        /**
         * ChefForms version.
         *
         * @var float
         */
        const VERSION = '2.2.0';


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
            load_plugin_textdomain( 'chefforms', false, $path );

            //auto-loads all .php files in these directories.
            $includes = array(
                'Classes/Wrappers',
                'Classes/Hooks/Cuisine',

                'Classes/Fields',
                'Classes/Fields/Tabs',

                'Classes/Front',
                'Classes/Front/Form',

                'Classes/Admin/Form/Builder',
                'Classes/Admin/Form/Entries',
                'Classes/Admin/Form/Notifications',
                'Classes/Admin/Form/Settings',
                'Classes/Admin/Form',
                'Classes/Admin'
            );

            $includes = apply_filters( 'chef_forms_autoload_dirs', $includes );


            foreach( $includes as $inc ){

                $root = static::getPluginPath();
                $files = glob( $root.$inc.'/*.php' );

                foreach ( $files as $file ){

                    require_once( $file );

                }
            }

            do_action( 'chef_forms_loaded' );

        }




        /*=============================================================*/
        /**             Getters & Setters                              */
        /*=============================================================*/


        /**
         * Init the forms classes
         *
         * @return \ChefForms
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

	ChefForms::getInstance();

}, 0, 400 );