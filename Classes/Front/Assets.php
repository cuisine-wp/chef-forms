<?php

	namespace CuisineForms\Front;

	use Cuisine\Utilities\Url;
	use Cuisine\Wrappers\Script;
	use Cuisine\Wrappers\Sass;


	class Assets{

		/**
		 * \CuisineForms\Front\Assets instance
		 *
		 * @var \CuisineForms\Front\Assets
		 */
		private static $instance = null;


		/**
		 * Init admin events & vars
		 */
		function __construct(){

			$this->enqueue();

		}

		/**
		 * Init the Assets Class
		 *
		 * @return \CuisineForms\Front\Assets
		 */
		public static function getInstance(){

		    if ( is_null( static::$instance ) ){
		        static::$instance = new static();
		    }
		    return static::$instance;
		}


	

		/**
		 * All enqueques
		 * 
		 * @return void
		 */
		private function enqueue(){

			add_action( 'init', function(){

				
				Script::register( 'core', Url::wp( 'jquery/ui/core.min' ), false );
				Script::register( 'datepicker', Url::wp( 'jquery/ui/datepicker.min' ), false );



				$url = Url::plugin( 'chef-forms', true ).'Assets/js/';
				
				Script::register( 'wysiwyg', $url.'libs/trumbowyg.min', false );
				Script::register( 'send-form', $url.'front/Form', true );

				//set sass files:
				if( !Sass::ignore() ){
					
					$url = 'chef-forms/Assets/sass/';
					Sass::register( 'form_styling', $url.'_form', false );
				
				}else{

					//we need to ignore sass and enqueue a regular css file:
					add_action( 'wp_enqueue_scripts', function(){

						wp_enqueue_style( 'chef_forms', Url::plugin( 'chef-forms', true ).'Assets/css/compiled.css' );

					});

				}

				//set validation errors:
				$vars = array(
					'required' 	=> __( 'This field is required', 'CuisineForms' ),
					'email'		=> __( 'Invalid e-mailaddress', 'CuisineForms' ),
					'number'	=> __( 'Invalid number', 'CuisineForms' ),
					'equalHigherZero'	=> __( 'Must be zero or higher', 'CuisineForms' ),
					'equalLowerZero'	=> __( 'Mast be zero or lower', 'CuisineForms' ),
					'higherZero'		=> __( 'Must be higer then zero', 'CuisineForms' ),
					'lowerZero'			=> __( 'Must be lower then zero', 'CuisineForms' ),
					'notchecked'		=> __( 'This should be checked', 'CuisineForms' ),
					'address'	=> __( 'Don\'t forget your house number', 'CuisineForms' ),
					'zipcode'	=> __( 'Invalid zipcode', 'CuisineForms' ),
					'slug'		=> __( 'This is not a valid domain', 'CuisineForms' )
				);


				$vars = apply_filters( 'cuisine_forms_validation_errors', $vars );
	
				Script::variable( 'ValidationErrors', $vars );

			});
		}

	}
