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
					'multifield', 
					$url.'/js/Multifield.js', 
					array( 'backbone' )
				);

				wp_enqueue_script( 
					'field_block', 
					$url.'/js/Field.js', 
					array( 'backbone' )
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

	\ChefForms\Admin\Assets::getInstance();
