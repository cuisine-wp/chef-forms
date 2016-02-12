<?php

	namespace ChefForms\Admin;

	use Cuisine\Utilities\Url;
	use Cuisine\Utilities\Session;
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
					$url.'/js/FieldBlock.js', 
					array( 'backbone' )
				);

				wp_enqueue_script( 
					'form_manager', 
					$url.'/js/FormManager.js', 
					array( 'backbone' )
				);
							
				wp_enqueue_style( 'form-builder', $url.'/css/admin.css' );

				if( isset( $_GET['post'] ) && get_post_type( Session::postId() ) === 'form' ){

					$fields = get_post_meta( Session::postId(), 'fields', true );

					foreach( $fields as $id => $field ){
						$fields[ $id ]['id'] = $id;
					}

					wp_localize_script( 'form_manager', 'FormFields', $fields ); 

				}
							
			});

		}

	}

	\ChefForms\Admin\Assets::getInstance();
