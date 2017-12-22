<?php

	namespace CuisineForms\Fields\Tabs;

	use Cuisine\Wrappers\Field;
	use CuisineForms\Wrappers\StaticInstance;
	use CuisineForms\Wrappers\TabBuilder as Tab;

	class AdvancedSettingsTab extends StaticInstance{


		/**
		 * Init admin events & vars
		 */
		public function __construct(){

			$this->listen();

		}

		/**
		 * Listen for SettingsPanels
		 * 
		 * @return void
		 */
		private function listen(){


			add_action( 'admin_init', function(){

				$fields = $this->getAdvancedFields();

				Tab::make( 

					__( 'Advanced options', 'CuisineForms' ),
					'advanced',
					array(
						'position' 	=> 1,
						'icon'		=> 'dashicons-admin-tools'
					)

				)->set( $fields );

			});
		}

		/**
		 * Returns an array of field objects
		 * 
		 * @return array
		 */
		private function getAdvancedFields(){
			
			$validate = array(

				'required' 		=> __( 'Filled in', 'CuisineForms' ),
				'email'			=> __( 'Valid email', 'CuisineForms' ),
				'address'		=> __( 'Valid address', 'CuisineForms' ),
				'zipcode'		=> __( 'Valid zipcode', 'CuisineForms' ),
				'number'		=> __( 'Valid number', 'CuisineForms' ),
				'not-negative'	=> __( 'Higher than zero', 'CuisineForms' ),
				'not-positive'	=> __( 'Lower than zero', 'CuisineForms' )
			);

			$fields = array(

				Field::select(
					'validation',
					__( 'Validate as', 'CuisineForms' ),
					$validate,
					array(
						'class' => array( 'validate-selector' ),
						'multiple' => true
					)
				),

				Field::text( 
					'classes',
					__( 'CSS Classes', 'CuisineForms' ),
					array( 
						'placeholder'  => __( 'Seperate with commas\'s', 'CuisineForms' )
					)
				)

			);

			$fields = apply_filters( 'cuisine_forms_advanced_field_settings', $fields, $this );

			return $fields;
		}
	}