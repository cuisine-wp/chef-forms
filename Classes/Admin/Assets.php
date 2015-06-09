<?php

	namespace ChefForms\Admin;

	use Cuisine\Utilities\Url;
	use \ChefForms\Wrappers\StaticInstance;

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

				$url = Url::plugin( 'chef-forms', true ).'Assets';
				
				wp_enqueue_script( 
					'field_block', 
					$url.'/js/FieldBlock.js', 
					array( 'backbone' )
				);

				wp_enqueue_script( 
					'form_manager', 
					$url.'/js/FormManager.js', 
					array( 'backbone' )
				);
							
				wp_enqueue_style( 'form-builder', $url.'/css/admin.css' );
							
			});

		}

	}

	\ChefForms\Admin\Assets::getInstance();
