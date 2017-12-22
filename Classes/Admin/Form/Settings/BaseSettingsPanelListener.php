<?php

	namespace CuisineForms\Admin\Form\Settings;

	use Cuisine\Utilities\Session;
	use Cuisine\Utilities\Sort;
	use Cuisine\Wrappers\Field;
	use CuisineForms\Wrappers\StaticInstance;
	use CuisineForms\Wrappers\SettingsPanel;

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
				__( 'Main Settings', 'cuisineforms' ),
				$options
			)->set( $this->mainSettingFields() );

			$options = [ 'icon' => 'dashicons dashicons-yes' ];
			SettingsPanel::make(
				'confirmation',
				__( 'Confirmation', 'cuisineforms' ),
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
					__( 'Button text', 'cuisineforms' ),
					array(
						'defaultValue'	=> __( 'Send', 'cuisineforms' )
					)
				),
				Field::select( 
					'labels',
					'Labels',
					array(
						false 	=> __( 'No labels', 'cuisineforms' ),
						'top'	=> __( 'Labels on top', 'cuisineforms' ),
						'left'	=> __( 'Labels left', 'cuisineforms' )
					),
					array(
						'defaultValue'	=> 'top'
					)
				),
				Field::text(
					'max_entries',
					__( 'Maximal amount of entries', 'cuisineforms' )
				),

				Field::date(
					'entry_start',
					__( 'Valid from', 'cuisineforms' )
				),

				Field::date(
					'entry_end',
					__( 'Valid through', 'cuisineforms' )
				),

				Field::checkbox(
					'no_ajax',
					__( 'Never use ajax for this form', 'cuisineforms' ),
					array(
						'defaultValue' => 'false'
					)
				)
			];

			$fields = apply_filters( 'cuisine_forms_main_settings_fields', $fields );
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
					__( 'Confirmation message', 'cuisineforms' ),
					array(
						'defaultValue' => __( 'Thank you very much for your message. We\'ll contact you as soon as possible.', 'CuisineForms' )
					)
				),

				Field::checkbox(
					'maintain_msg',
					__( 'Don\'t automatically remove the confirmation message', 'cuisineforms' ),
					array(
						'defaultValue' => 'false'
					)
				),

				Field::checkbox(
					'redirect',
					__( 'Redirect after sending a form', 'cuisineforms' ),
					array(
						'defaultValue' => 'false'
					)
				),
				Field::select(
					'redirect_to',
					__( 'Redirect to', 'cuisineforms' ),
					$this->getPages(),
					array(
						'defaultValue' => 'none'
					)
				)
			];


			$fields = apply_filters( 'cuisine_forms_confirmation_settings_fields', $fields );
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
			$pages = array_combine( Sort::pluck( $pages, 'ID' ), Sort::pluck( $pages, 'post_title' ) );
			$pages = [ 'none' => __( 'Don\'t redirect to a page', 'cuisineforms') ] + $pages;
			return $pages;
		}

	}