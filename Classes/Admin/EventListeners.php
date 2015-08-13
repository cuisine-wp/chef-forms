<?php

	namespace ChefForms\Admin;

	use \ChefForms\Wrappers\FormManager;
	use \ChefForms\Wrappers\StaticInstance;
	use \Cuisine\Utilities\Url;
	use \Cuisine\Wrappers\Field;
	use \Cuisine\Wrappers\Metabox;
	use \Cuisine\Wrappers\SettingsPage;

	class EventListeners extends StaticInstance{

		/**
		 * Init admin events & vars
		 */
		function __construct(){

			$this->listen();
			$this->metaBoxEvents();
			$this->settingsPage();

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


		/**
		 * The settingspage used by this plugin
		 * 
		 * @return void
		 */
		private function settingsPage(){

			$options = array(
				'parent'		=> 'form',
				'menu_title'	=> 'Instellingen'
			);

			$fields = array(

				Field::checkbox( 
					'use_mandrill', 
					'Gebruik Mandrill',
					array(
						'defaultValue' => 'true'
					)
				),

				Field::text(
					'host',
					'Mandrill Host',
					array(
						'defaultValue'	=> 'smtp.mandrillapp.com'
					)
				),

				Field::text(
					'user',
					'Mandrill User',
					array(
						'defaultValue'	=> 'luc.princen@gmail.com'
					)
				),

				Field::text(
					'password',
					'Mandrill Password',
					array(
						'defaultValue'	=> '_gEwO60stNDpGZFyrYaadQ'
					)
				)
			);

			SettingsPage::make(

				'Formulier instellingen', 
				'form-settings', 
				$options

			)->set( $fields );

		}

	}


	if( is_admin() )
		\ChefForms\Admin\EventListeners::getInstance();


