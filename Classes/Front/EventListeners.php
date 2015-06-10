<?php

	namespace ChefForms\Front;


	use Cuisine\Wrappers\PostType;

	class EventListeners{

		/**
		 * EventListeners instance
		 *
		 * @var \ChefForms\Front\EventListeners
		 */
		private static $instance = null;


		/**
		 * Init admin events & vars
		 */
		function __construct(){

			$this->listen();

		}

		/**
		 * Init the EventListeners Class
		 *
		 * @return \ChefForms\Front\EventListeners
		 */
		public static function getInstance(){

		    if ( is_null( static::$instance ) ){
		        static::$instance = new static();
		    }
		    return static::$instance;
		}


	

		/**
		 * All EventListeners
		 * 
		 * @return [type] [description]
		 */
		private function listen(){

			//creating post types:
			add_action( 'init', function(){

				PostType::make( 
					
					'form', 
					__( 'Formulieren', 'chefforms' ),
					__( 'Formulier', 'chefforms' )

				)->set( array( 
					'supports' => array( 'title' ),
					'menu_icon' => 'dashicons-editor-quote'
				) );


				PostType::make( 
					
					'form-entry', 
					__( 'Entries', 'chefforms' ),
					__( 'Entry', 'chefforms' )

				)->set( array( 'public' => false ) );				

			});


			//sending:

		}

	}

	\ChefForms\Front\EventListeners::getInstance();
