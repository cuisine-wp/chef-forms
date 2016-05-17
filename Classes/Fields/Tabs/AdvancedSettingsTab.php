<?php

	namespace ChefForms\Fields\Tabs;

	use Cuisine\Wrappers\Field;
	use ChefForms\Wrappers\StaticInstance;
	use ChefForms\Wrappers\TabBuilder as Tab;

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

					__( 'Advanced options', 'chefforms' ),
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

				'required' 		=> __( 'Filled in', 'chefforms' ),
				'email'			=> __( 'Valid email', 'chefforms' ),
				'address'		=> __( 'Valid address', 'chefforms' ),
				'zipcode'		=> __( 'Valid zipcode', 'chefforms' ),
				'number'		=> __( 'Valid number', 'chefforms' ),
				'not-negative'	=> __( 'Higher than zero', 'chefforms' ),
				'not-positive'	=> __( 'Lower than zero', 'chefforms' )
			);

			$fields = array(

				Field::select(
					'validation',
					__( 'Validate as', 'chefforms' ),
					$validate,
					array(
						'class' => array( 'validate-selector' ),
						'multiple' => true
					)
				),

				Field::text( 
					'classes',
					__( 'CSS Classes', 'chefforms' ),
					array( 
						'placeholder'  => __( 'Seperate with commas\'s', 'chefforms' )
					)
				)

			);

			$fields = apply_filters( 'chef_forms_advanced_field_settings', $fields, $this );

			return $fields;
		}

	}

	\ChefForms\Fields\Tabs\AdvancedSettingsTab::getInstance();
