<?php

	namespace CuisineForms\Admin;

	use \CuisineForms\Wrappers\FormManager;
	use \CuisineForms\Wrappers\StaticInstance;
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
			//$this->setMetabox();
			$this->setSettingsPage();

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
		private function setMetabox(){


		}


		/**
		 * The settingspage used by this plugin
		 * 
		 * @return void
		 */
		private function setSettingsPage(){

			$options = array(
				'parent'		=> 'form',
				'menu_title'	=> __( 'Entries', 'cuisineforms' )
			);

			SettingsPage::make(

				__( 'Form Entries', 'cuisineforms' ),
				'form-entries',
				$options
				
			)->set( 'CuisineForms\\Wrappers\\EntriesManager::build' );

			$options['menu_title'] = __( 'Settings', 'cuisineforms' );
			SettingsPage::make(

				__( 'Form Settings', 'cuisineforms' ), 
				'form-settings', 
				$options

			)->set( $this->getSettingFields() );

		}

		/**
		 * Returns an array of settng fields
		 * 
		 * @return array
		 */
		private function getSettingFields(){

			$fields = array(

				Field::checkbox( 
					'use_mandrill', 
					__( 'Use Mandrill', 'cuisineforms' ),
					array(
						'defaultValue' => 'true'
					)
				),

				Field::text(
					'host',
					__( 'Mandrill Host', 'cuisineforms' ),
					array(
						'defaultValue'	=> 'smtp.mandrillapp.com'
					)
				),

				Field::text(
					'user',
					__( 'Mandrill User', 'cuisineforms' ),
					array(
						'defaultValue'	=> ''
					)
				),

				Field::text(
					'password',
					__( 'Mandrill Password', 'cuisineforms' ),
					array(
						'defaultValue'	=> ''
					)
				)
			);

			$fields = apply_filters( 'cuisine_forms_setting_fields', $fields );
			return $fields;
		}

	}