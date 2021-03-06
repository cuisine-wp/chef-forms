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
				'menu_title'	=> __( 'Entries', 'chefforms' )
			);

			SettingsPage::make(

				__( 'Form Entries', 'chefforms' ),
				'form-entries',
				$options
				
			)->set( 'ChefForms\\Wrappers\\EntriesManager::build' );

			$options['menu_title'] = __( 'Settings', 'chefforms' );
			SettingsPage::make(

				__( 'Form Settings', 'chefforms' ), 
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
					__( 'Use Mandrill', 'chefforms' ),
					array(
						'defaultValue' => 'true'
					)
				),

				Field::text(
					'host',
					__( 'Mandrill Host', 'chefforms' ),
					array(
						'defaultValue'	=> 'smtp.mandrillapp.com'
					)
				),

				Field::text(
					'user',
					__( 'Mandrill User', 'chefforms' ),
					array(
						'defaultValue'	=> ''
					)
				),

				Field::text(
					'password',
					__( 'Mandrill Password', 'chefforms' ),
					array(
						'defaultValue'	=> ''
					)
				)
			);

			$fields = apply_filters( 'chef_forms_setting_fields', $fields );
			return $fields;
		}

	}


	if( is_admin() )
		\ChefForms\Admin\EventListeners::getInstance();


