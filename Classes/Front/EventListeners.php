<?php

	namespace ChefForms\Front;

	use Cuisine\Utilities\Url;
	use Cuisine\Wrappers\PostType;
	use ChefForms\Wrappers\StaticInstance;

	class EventListeners extends StaticInstance{


		/**
		 * Init admin events & vars
		 */
		function __construct(){

			$this->listen();
			$this->hooks();

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





		/**
		 * Load custom hooks for this plugin
		 * 
		 * @return mixed
		 */
		private function hooks(){

			//custom column:
			add_filter( 'chef_sections_column_types', function( $types ){

				$base = Url::path( 'plugin', 'chef-forms', true );

				$types['form'] = array(
							'name'		=> 'Formulier',
							'class'		=> 'ChefForms\Hooks\Column',
							'template'	=> $base.'Templates/Column.php'
				);

				return $types;

			});


			//custom field type:
			add_filter( 'cuisine_field_types', function( $types ){


				$types['multi'] = array(
							'name'		=> 'MultiField',
							'class'		=> 'ChefForms\Hooks\MultiField'
				);

				return $types;

			});


			//load files
			add_action( 'chef_sections_loaded', function(){

				$base = Url::path( 'plugin', 'chef-forms', true );
				include( $base.'Classes/Hooks/Column.php' );
				include( $base.'Classes/Hooks/MultiField.php' );

			});
		}

	}

	\ChefForms\Front\EventListeners::getInstance();
