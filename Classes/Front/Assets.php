<?php

	namespace ChefForms\Front;

	use Cuisine\Utilities\Url;
	use Cuisine\Wrappers\Script;
	use Cuisine\Wrappers\Sass;


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

			add_action( 'init', function(){

				$url = Url::plugin( 'chef-forms', true ).'Assets/js/';
				Script::register( 'send-form', $url.'Form', true );

				$url = 'chef-forms/Assets/sass/';
				Sass::register( 'form_styling', $url.'_form', true );

			});
		}

	}

	\ChefForms\Front\Assets::getInstance();
