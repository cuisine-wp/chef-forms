<?php

	namespace ChefForms\Front;

	use Cuisine\Utilities\Url;


	class Assets{

		/**
		 * \ChefForms\Front\Assets instance
		 *
		 * @var \ChefForms\Front\Assets
		 */
		private static $instance = null;


		/**
		 * Init admin events & vars
		 */
		function __construct(){

			$this->enqueue();

		}

		/**
		 * Init the Assets Class
		 *
		 * @return \ChefForms\Front\Assets
		 */
		public static function getInstance(){

		    if ( is_null( static::$instance ) ){
		        static::$instance = new static();
		    }
		    return static::$instance;
		}


	

		/**
		 * All enqueques
		 * 
		 * @return void
		 */
		private function enqueue(){


		}

	}

	\ChefForms\Front\Assets::getInstance();
