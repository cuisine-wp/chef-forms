<?php

	namespace ChefForms\Front;

	use Cuisine\Utilities\Url;
	use Cuisine\Wrappers\Script;
	use Cuisine\Wrappers\Sass;


	class Assets{

		/**
		 * \ChefForms\Front\Assets instance
		 *
		 * @var \ChefForms\Front\Assets
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
		 * @return \ChefForms\Front\Assets
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
				Script::register( 'send-form', $url.'Form', true );

				$url = 'chef-forms/Assets/sass/';
				Sass::register( 'form_styling', $url.'_form', false );


				//set validation errors:
				$vars = array(
					'required' 	=> __( 'Dit is een verplicht veld', 'chefforms' ),
					'email'		=> __( 'Dit is geen geldig e-mailadres', 'chefforms' ),
					'number'	=> __( 'Dit is geen geldig nummer', 'chefforms' ),
					'equalHigherZero'	=> __( 'Dit moet minimaal nul zijn', 'chefforms' ),
					'equalLowerZero'	=> __( 'Dit mag maximaal nul zijn', 'chefforms' ),
					'higherZero'		=> __( 'Dit moet hoger dan nul zijn', 'chefforms' ),
					'lowerZero'			=> __( 'Dit moet lager dan nul zijn', 'chefforms' ),
					'address'	=> __( 'Vergeet je het huisnummer niet?', 'chefforms' ),
					'zipcode'	=> __( 'Dit is geen geldige postcode', 'chefforms' ),
					'slug'		=> __( 'Dit is geen geldig domein', 'chefforms' )
				);
	
				Script::variable( 'ValidationErrors', $vars );

			});
		}

	}

	\ChefForms\Front\Assets::getInstance();
