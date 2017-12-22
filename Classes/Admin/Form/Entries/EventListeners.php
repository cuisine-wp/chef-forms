<?php

	namespace CuisineForms\Admin\Form\Entries;

	use CuisineForms\Wrappers\StaticInstance;

	class EventListeners extends StaticInstance{

		/**
		 * Constructor
		 */
		public function __construct()
		{
			$this->listen();
		}


		/**
		 * Listeners
		 * 
		 * @return void
		 */
		public function listen()
		{
			
			//check if entries are deleted:
			add_action( 'admin_init', function(){

				if( 
					isset( $_POST['entry_nonce'] ) &&
					wp_verify_nonce( $_POST['entry_nonce'], 'delete_entry' )
				){

					$url = admin_url('edit.php');
					$url = add_query_arg([ 
						'post_type' => 'form', 
						'page' => 'form-entries',
						'entry_page' => ( isset( $_POST['entry_page'] ) ? $_POST['entry_page'] : 1 ),
						'message' => urlencode( __( 'Entry deleted', 'CuisineForms' ) )
					], $url );


					if( isset( $_POST['parent'] ) )
						$url = add_query_arg( 'parent', $_POST['parent'], $url ); 

					wp_delete_post( $_POST['entry_id'] );

					wp_redirect( $url );
					die();
				}
			});


			add_action( 'admin_notices', function(){

				if( isset( $_GET['page'] ) && $_GET['page'] == 'form-entries' ){

					if( isset( $_GET['message'] ) ){
						echo '<div class="notice notice-success is-dismissible">';
        					echo '<p>'.$_GET['message'].'</p>';
    					echo '</div>';
    				}else if( isset( $_GET['error'] ) ){
    					echo '<div class="notice notice-error is-dismissible">';
        					echo '<p>'.$_GET['error'].'</p>';
    					echo '</div>';
    				}
				}
			});

		}
	}