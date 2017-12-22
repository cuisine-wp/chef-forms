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

					__( 'Advanced options', 'cuisineforms' ),
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

				'required' 		=> __( 'Filled in', 'cuisineforms' ),
				'email'			=> __( 'Valid email', 'cuisineforms' ),
				'address'		=> __( 'Valid address', 'cuisineforms' ),
				'zipcode'		=> __( 'Valid zipcode', 'cuisineforms' ),
				'number'		=> __( 'Valid number', 'cuisineforms' ),
				'not-negative'	=> __( 'Higher than zero', 'cuisineforms' ),
				'not-positive'	=> __( 'Lower than zero', 'cuisineforms' )
			);

			$fields = array(

				Field::select(
					'validation',
					__( 'Validate as', 'cuisineforms' ),
					$validate,
					array(
						'class' => array( 'validate-selector' ),
						'multiple' => true
					)
				),

				Field::text( 
					'classes',
					__( 'CSS Classes', 'cuisineforms' ),
					array( 
						'placeholder'  => __( 'Seperate with commas\'s', 'cuisineforms' )
					)
				)

			);

			$fields = apply_filters( 'cuisine_forms_advanced_field_settings', $fields, $this );

			return $fields;
		}
	}