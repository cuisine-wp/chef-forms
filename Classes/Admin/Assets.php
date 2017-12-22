<?php

	namespace CuisineForms\Admin;

	use Cuisine\Utilities\Url;
	use \CuisineForms\Wrappers\StaticInstance;

	class Assets extends StaticInstance{


		/**
		 * Init admin events & vars
		 */
		function __construct(){

			$this->enqueue();

		}

	
		/**
		 * All enqueques
		 * 
		 * @return void
		 */
		private function enqueue(){

			add_action( 'admin_menu', function(){

				$url = Url::plugin( 'cuisine-forms/Assets' );
				
				wp_enqueue_script( 
					'multifield', 
					$url.'/js/Multifield.js', 
					array( 'backbone' )
				);

				wp_enqueue_script( 
				    'chosen', 
				    $url.'/js/libs/chosen.min.js', 
				    array( 'jquery' ),
				    false,
				    true
				);

				wp_enqueue_script( 
					'field_block', 
					$url.'/js/Field.js', 
					array( 'backbone', 'chosen' )
				);

				wp_enqueue_script( 
					'form_manager', 
					$url.'/js/Manager.js', 
					array( 'backbone' )
				);
							
				wp_enqueue_style( 'form-builder', $url.'/css/admin.css' );
							
			});

		}
	}
