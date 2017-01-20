<?php

	namespace ChefForms\Admin\Form\Settings;

	use Cuisine\Utilities\Session;
	use Cuisine\Utilities\Sort;
	use Cuisine\Wrappers\Field;
	use ChefForms\Wrappers\StaticInstance;
	use ChefForms\Wrappers\SettingsPanel;

	class BaseSettingsPanelListener extends StaticInstance{

		/**
		 * Init admin events & vars
		 */
		function __construct(){

			$this->listen();

		}


		/**
		 * All admin event listeners
		 * 
		 * @return void
		 */
		private function listen(){

			$options = [ 'icon' => 'dashicons dashicons-admin-generic', 'classes' => ['active'] ];
			SettingsPanel::make(
				'settings',
				__( 'Main Settings', 'chefforms' ),
				$options
			)->set( $this->mainSettingFields() );

			$options = [ 'icon' => 'dashicons dashicons-yes' ];
			SettingsPanel::make(
				'confirmation',
				__( 'Confirmation', 'chefforms' ),
				$options
			)->set( $this->confirmationSettingsFields() );

		}


		/**
		 * Returns the main setting fields
		 * 
		 * @return array
		 */
		public function mainSettingFields()
		{
			$fields = [
				Field::text( 
					'btn-text',
					__( 'Button text', 'chefforms' ),
					array(
						'defaultValue'	=> __( 'Send', 'chefforms' )
					)
				),
				Field::select( 
					'labels',
					'Labels',
					array(
						false 	=> __( 'No labels', 'chefforms' ),
						'top'	=> __( 'Labels on top', 'chefforms' ),
						'left'	=> __( 'Labels left', 'chefforms' )
					),
					array(
						'defaultValue'	=> 'top'
					)
				),
				Field::text(
					'max_entries',
					__( 'Maximal amount of entries', 'chefforms' )
				),

				Field::date(
					'entry_start',
					__( 'Valid from', 'chefforms' )
				),

				Field::date(
					'entry_end',
					__( 'Valid through', 'chefforms' )
				),

				Field::checkbox(
					'no_ajax',
					__( 'Never use ajax for this form', 'chefforms' ),
					array(
						'defaultValue' => 'false'
					)
				)
			];

			$fields = apply_filters( 'chef_forms_main_settings_fields', $fields );
			return $fields;
		}


		/**
		 * Returns an array of confirmation settings fields
		 * 
		 * @return array
		 */
		public function confirmationSettingsFields()
		{

			$fields = [
				Field::editor(
					'confirm',
					__( 'Confirmation message', 'chefforms' ),
					array(
						'defaultValue' => __( 'Thank you very much for your message. We\'ll contact you as soon as possible.', 'chefforms' )
					)
				),

				Field::checkbox(
					'maintain_msg',
					__( 'Don\'t automatically remove the confirmation message', 'chefforms' ),
					array(
						'defaultValue' => 'false'
					)
				),

				Field::checkbox(
					'redirect',
					__( 'Redirect after sending a form', 'chefforms' ),
					array(
						'defaultValue' => 'false'
					)
				),
				Field::select(
					'redirect_to',
					__( 'Redirect to', 'chefforms' ),
					$this->getPages(),
					array(
						'defaultValue' => 'none'
					)
				)
			];


			$fields = apply_filters( 'chef_forms_confirmation_settings_fields', $fields );
			return $fields;	
		}


		/**
		 * Return all the pages to select in the dropdown for redirects
		 * 
		 * @return array
		 */
		public function getPages()
		{
			$pages = get_pages();
			return array_combine( Sort::pluck( $pages, 'ID' ), Sort::pluck( $pages, 'post_title' ) );
		}

	}

	if( is_admin() )
		\ChefForms\Admin\Form\Settings\BaseSettingsPanelListener::getInstance();


