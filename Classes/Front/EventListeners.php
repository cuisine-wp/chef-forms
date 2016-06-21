<?php

	namespace ChefForms\Front;

	use Cuisine\Utilities\Url;
	use Cuisine\Wrappers\PostType;
	use ChefForms\Wrappers\StaticInstance;
	use ChefForms\Wrappers\Form;

	class EventListeners extends StaticInstance{


		/**
		 * Init admin events & vars
		 */
		function __construct(){

			$this->listen();
			$this->hooks();
			$this->shortcodes();

		}
	

		/**
		 * All EventListeners
		 * 
		 * @return [type] [description]
		 */
		private function listen(){

			//creating post types:
			add_action( 'init', function(){

				PostType::make( 
					
					'form', 
					__( 'Forms', 'chefforms' ),
					__( 'Form', 'chefforms' )

				)->set( array( 
					'supports' => array( 'title' ),
					'menu_icon' => 'dashicons-editor-quote'
				) );


				PostType::make( 
					
					'form-entry', 
					__( 'Entries', 'chefforms' ),
					__( 'Entry', 'chefforms' )

				)->set( array( 'public' => false ) );	


			}, 100, 0 );




			//Submitting forms, without ajax
			add_action( 'init', function(){

				//first, check if we're dealing with a non-ajax form submit:
				if( 
				
					!defined( 'DOING_AJAX' ) &&
					!empty( $_POST ) && 
					isset( $_POST['_chef_form_submit'] )

				){

					$confirm = Form::save( $_POST['_fid'] );
					$response = json_decode( $confirm );

					//redirect, if needed:
					if( isset( $response->redirect ) && $response->redirect == true ){

						Header( 'Location: '.$response->redirect_url );
						exit();

					}else{

						//else save the message to a session,
						//so we can display it on the form:
						if( !isset( $_SESSION['form_messages'] ) )
							$_SESSION['form_messages'] = array();

						$_SESSION['form_messages'][] = array(
							'type' => ( $response->error ? 'error' : 'msg' ),
							'text' => $response->message
						);

					}

				}

			}, 200, 0 );
		}



		/**
		 * All code for the shortcodes
		 * 
		 * @return void
		 */
		private function shortcodes(){

			add_shortcode( 'cuisine_form', function( $atts ){

				if( !isset( $atts['id'] ) )
					return '';

				return Form::make( $atts['id'] )->render();

			});

		}





		/**
		 * Load custom hooks for this plugin
		 * 
		 * @return mixed
		 */
		private function hooks(){

			//custom column:
			add_filter( 'chef_sections_column_types', function( $types ){

				$base = Url::path( 'plugin', 'chef-forms', true );

				$types['form'] = array(
							'name'		=> __( 'Form', 'chefforms' ),
							'class'		=> 'ChefForms\Hooks\Column',
							'template'	=> $base.'Templates/Column.php'
				);

				return $types;

			});


			//custom field type:
			add_filter( 'cuisine_field_types', function( $types ){


				$types['multifield'] = array(
							'name'		=> 'MultiField',
							'class'		=> 'ChefForms\Hooks\MultiField'
				);

				$types['mapper'] = array(
							'name'		=> 'MapperField',
							'class'		=> 'ChefForms\Hooks\MapperField'
				);

				return $types;

			});


			//load column
			add_action( 'chef_sections_loaded', function(){

				$base = Url::path( 'plugin', 'chef-forms/Classes/Hooks/ChefSections', true );
				include( $base.'Column.php' );

			});

		}

	}

	\ChefForms\Front\EventListeners::getInstance();
