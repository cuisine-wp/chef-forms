<?php

	namespace ChefForms\Admin;

	use \ChefForms\Wrappers\FormManager;
	use \ChefForms\Wrappers\StaticInstance;
	use \Cuisine\Utilities\Url;
	use \Cuisine\Wrappers\Metabox;

	class EventListeners extends StaticInstance{

		/**
		 * Init admin events & vars
		 */
		function __construct(){

			$this->listen();
			$this->metaBoxEvents();

		}


		/**
		 * All admin event listeners
		 * 
		 * @return void
		 */
		private function listen(){


			add_action( 'edit_form_after_editor', function(){

				global $post;

				if( isset( $post ) ){

					FormManager::build();
				
				}

			});


			add_action( 'save_post', function( $post_id ){

				FormManager::save( $post_id );

			});

		}

		/**
		 * All metaboxes used by this plugin
		 * 
		 * @return void
		 */
		private function metaBoxEvents(){



			$options = array( 'context' => 'side' );
			
			//standard fields
			Metabox::make( 

				__( 'Standaard velden', 'chefforms' ), 
				'form', 
				$options

			)->set( '\\ChefForms\\Wrappers\\FieldControls::standard' );

			//advanced fields
			Metabox::make( 
				__( 'Geavanceerde velden', 'chefforms' ), 
				'form', 
				$options

			)->set( '\\ChefForms\\Wrappers\\FieldControls::advanced' );

		}

	}


	if( is_admin() )
		\ChefForms\Admin\EventListeners::getInstance();


