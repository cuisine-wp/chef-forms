<?php

	namespace ChefForms\Front;

	use ChefForms\Wrappers\Field;
	use Cuisine\Utilities\Sort;
	use Cuisine\Utilities\Url;
	use Cuisine\Utilities\Session;
	use Cuisine\Wrappers\Template;
	use ChefForms\Wrappers\Notification as FormNotification;
	use ChefForms\Wrappers\Entry as FormEntry;
	
	class Form {
	
		/**
		 * The ID for this form
		 * 
		 * @var integer
		 */
		public $id = 0;


		/**
		 * Store the html for this form
		 * 
		 * @var string
		 */
		private $html = '';


		/**
		 * Array containing all the fields
		 * 
		 * @var array
		 */
		public $fields = array();


		/**
		 * Array containing all notifications
		 * 
		 * @var array
		 */
		private $notifications = array();


		/**
		 * Array of errors to display before the form
		 * 
		 * @var array
		 */
		public $messages = array();


		/**
		 * Json object with the callback message
		 * 
		 * @var array
		 */
		public $message = array();


		/**
		 * Allow plugins to pass redirects for this form
		 * 
		 * @var array
		 */
		public $redirect = array();

		/**
		 * All of this forms settings
		 * 
		 * @var array
		 */
		private $settings = array();


		/**
		 * This forms return link (IE8 & IE9 only, unless you choose to hard refresh)
		 * @var string
		 */
		public $returnLink = '';


		/**
		 * Method of submitting, defaults to post
		 * 
		 * @var string
		 */
		public $submitMethod = 'post';


		/**
		 * Default enctype:
		 * 
		 * @var string
		 */
		public $enctype = 'multipart/form-data';




		/**
		 * Check if this form can be filled in
		 * 
		 * @var boolean / string
		 */
		public $notValid = false;


		/**
		 * Init this form
		 * 
		 * @return void
		 */
		private function init(){

			//allow plugins to change the ID of this form on-the-fly
			do_action( 'chef_forms_init_form', $this );

			$this->setSettings();
			$this->setMessages();
			$this->setValidity();
			$this->setFields();

			do_action( 'chef_forms_after_init', $this );
			
		}



		/*=============================================================*/
		/**             MAKE functions                                 */
		/*=============================================================*/



		/**
		 * Build this form for frontend use
		 * 
		 * @param  int $id form id
		 * @return \ChefForms\Front\Form
		 */
		public function make( $id = null ){

			if( $id !== null )
				$this->id = $id;

			$this->init();

			$this->render();
			
			return $this;
		}


		/**
		 * Render the actual form and store it in the html variable
		 * 
		 * @return string (html)
		 */
		public function render(){
			
			ob_start();

			//if the form can be filled in:
			if( $this->notValid === false ){

				//show messages, if needed:
				$this->showMessages();

				do_action( 'chef_forms_before_form', $this );

				//render the form-tag, with all attributes
				$this->renderFormTag();

				$this->renderNonce();

					do_action( 'chef_forms_before_fields', $this );

					//anchor for message showing:
					echo '<span class="form-anchor" id="f'.$this->id.'"></span>';
					echo '<div class="form-fields">';
							
						foreach( $this->fields as $field ){
				
							$field->render();
				
						}
				
					echo '</div>';
	
					if( apply_filters( 'chef_forms_show_footer', true, $this ) ){	
						
						echo '<div class="form-footer">';
				
							echo '<button class="submit-form">';
				
								echo $this->getSetting( 'btn-text', 'Verstuur' );
				
							echo '</button>';
				
						echo '</div>';
	
					}
							
					
					$default = Url::path( 'plugin', 'chef-forms/Templates/Loader' );
					Template::element( 'loader', $default )->display();
				
				//close the form-tag
				echo '</form>';

				do_action( 'chef_forms_after_form', $this );

			}else{

				$default = Url::path( 'plugin', 'chef-forms/Templates/Confirmations/'.$this->notValid.'-error' );
				Template::element( 'forms/'.$this->notValid.'-error', $default )->display();

			}

			$this->html = ob_get_clean();
		}



		/**
		 * Show messages in the message-array, if they are set.
		 * 
		 * @return string ( html, echoed )
		 */
		public function showMessages(){

			if( !empty( $this->messages ) ){

				$default = Url::path( 'plugin', 'chef-forms/Templates/Message' );

				foreach( $this->messages as $_msg ){

					if( !is_array( $_msg ) ){
						$_msg = array(
							'type'	=> 'msg',
							'text'	=> $_msg
						);
					}

					$args = array( 'msg' => $_msg );
					Template::element( 'forms/Message', $default )->display( $args );
				}
			}
		}


		/**
		 * Add the form tag, with all attributes
		 * 
		 * @return string (html, echoed)
		 */
		public function renderFormTag(){

			echo '<form class="'.$this->getClasses().'" id="form_'.$this->id.'"';
			

			//message stickyness
			if( 
				$this->getSetting( 'maintain_msg' ) === 'true' ||
				apply_filters('chef_forms_maintain_msg', false, $this )
			){
				echo ' data-maintain-msg="true"';
			}

			//no ajax
			if(
				$this->getSetting( 'no_ajax' ) === 'true' ||
				apply_filters( 'chef_forms_no_ajax', false, $this )
			){
				echo ' data-no-ajax="true"';
			
				//hard refresh settings:
				echo ' action="'.$this->returnLink.'"';
				echo ' method="'.$this->submitMethod.'"';
				echo ' enctype="'.$this->enctype.'"';
			}
			
			echo '>';

		}

		/**
		 * Render the nonce-tag, and various hidden fields
		 * 
		 * @return string (html,echoed)
		 */
		public function renderNonce(){

			//add the post-nonce:
			wp_nonce_field( 'form_'.$this->id.'_submit', '_chef_form_submit' );
			echo '<input type="hidden" name="_fid" value="'.$this->id.'"/>';
			echo '<input type="hidden" name="_rootPid" value="'.Session::rootPostId().'"/>';


		}


		/**
		 * Render this form:
		 * 
		 * @return void
		 */
		public function display(){

			echo $this->html;

		}


		/**
		 * Return the form object
		 *
		 * 
		 * @return \ChefForms\Front\Form
		 */
		public function get( $name ){

			$allForms = get_option( 'existingForms', array() );
			$_id = false;

			//get from the options table:
			foreach( $allForms as $id => $formTitle ){

				if( strtolower( $formTitle ) == strtolower( $name ) )
					$_id = $id;
				
			}

			//try a query on the name:
			if( !$_id ){

				global $wpdb;
				$query = "SELECT ID FROM {$wpdb->posts}  WHERE post_name = %s";
				$_id = $wpdb->get_var( $wpdb->prepare( $query, $name ) );

			}

			//if there's an id found, make the form
			if( $_id ){

				$this->id = $_id;

				//return the made form:
				return self::make();	
			}

			//else return false
			return false;

		}


		/**
		 * Store the values of this form in a session, so we can do a redirect
		 *
		 * @return void
		 */
		public function store(){

			if( !isset( $_SESSION['form'] ) )
				$_SESSION['form'] = array();
			

			$_SESSION['form'] = array_merge(

				$_SESSION['form'], 

				array(
				
					'id'		=> $this->id,
					'entry'		=> $_POST['entry'],
					'entry_id'	=> $_POST['entry_id']
				
				)
			);
		}


		/**
		 * Retrieves this form from an existing session.
		 * 
		 * @return ChefForms\Front\Form
		 */
		public function retrieve(){

			if( isset( $_SESSION['form'] ) ){
				
				//reset all used vars:				
				$this->id = $_SESSION['form']['id'];
				$_POST['entry'] = $_SESSION['form']['entry'];
				$_POST['entry_id'] = $_SESSION['form']['entry_id'];

				//init form
				$this->init();

				//return the functioning form object
				return $this;

			}

			return false;
		}

		/**
		 * Kill the form session
		 * 
		 * @return void
		 */
		public function flush(){
			//kill the session
			unset( $_SESSION['form'] );
		}



		/*=============================================================*/
		/**             Saving & Notifiying                            */
		/*=============================================================*/


		/**
		 * Save an entry to this form
		 * 
		 * @param   Int $id ( the id for this form )
		 * @return  \ChefForms\Front\Form
		 */
		public function save( $id ){
		
			$this->id = $id;

			//first, check if the nonce is valid:
			if( !wp_verify_nonce( $_POST['_chef_form_submit'], 'form_'.$id.'_submit' ) ){

				$this->message = array(
						'error'		=> 	true,
						'message'	=> 	__( 'Geen geldige Nonce.', 'chefforms' )
				);

				return json_encode( $this->message );
			}


			//init the Form object:
			$this->init();

			//create the entry:
			$entry = FormEntry::make( $this );

			//allow plugins to hook into this event:
			do_action( 'form_submitted', $this, $entry );

			//check if a redirect has been set
			if( !empty( $this->redirect ) ){

				//store this form-session in a php session:
				$this->store();

				//return the redirect data
				return json_encode( $this->redirect );
			
			}

			//else, carry on to notifications:
			do_action( 'before_notification', $this, $entry );

				//notify everybody
				$this->notify();

			//after notifying
			do_action( 'after_notification', $this, $entry );

			//set the message, if it's empty
			if( empty( $this->message ) ){
				$this->message = array(

						'error'		=> 	false,
						'message'	=> 	$this->getSetting( 'confirm' )
				);
			}

			//return the message
			return json_encode( $this->message );
		}

		
		/**
		 * Notify about this form:
		 * 
		 * @return void
		 */
		public function notify(){

			$this->notifications = $this->setNotifications();


			if( !empty( $this->notifications ) ){

				foreach( $this->notifications as $notification ){

					$notification->send();
				}
			}

		}




		/*=============================================================*/
		/**             Getters                                        */
		/*=============================================================*/


		/**
		 * Get a setting
		 *
		 * @param string $name 
		 * @param mixed $default
		 * @return mixed
		 */
		public function getSetting( $name, $default = false ){

			if( isset( $this->settings[ $name ] ) )
				return $this->settings[ $name ];

			return $default;
		}


		/**
		 * Get the value of a single field in this form
		 * 
		 * @param  string  $field   
		 * @param  mixed $default
		 * @return mixed
		 */
		private function getFieldValue( $name, $default = false ){

			if( isset( $this->fields[ $name ] ) )
				$val = $this->fields[ $name ]->getValue();

			if( !$val )
				return $default;

		}


		/**
		 * Returns all classes for this form, and makes them filterable
		 * 
		 * @return string
		 */
		public function getClasses(){

			$classes = array(
				'form',
				'form-'.$this->getSetting( 'slug' )
			);

			$classes = apply_filters( 'chef_forms_classes', $classes, $this );

			return implode( ' ', $classes );
		}


		/**
		 * Returns the arguments for field generation:
		 * 
		 * @param  array $field
		 * @return array
		 */
		private function sanitizeFieldArgs( $id, $field ){

			if( !isset( $field['placeholder' ] ) )
				$field['placeholder'] = false;

			if( !isset( $field['defaultValue'] ) )
				$field['defaultValue'] = false;

			if( !isset( $field['required'] ) )
				$field['required'] = false;

			if( isset( $field['name'] ) ){
				$field['name'] = $field['name'];
			}else{
				$field['name'] = 'field_'.$this->id.'_'.$id;
			}

			if( isset( $field['choices'] ) ){

				$choices = array();
				foreach( $field['choices'] as $val ){

					$choices[ $val['key'] ] = $val['label'];

				}

				$field['choices'] = $choices;
			}


			//get the label value from the settings:
			$field['labelPosition'] = $this->getSetting( 'labels', 'top' );

			return $field;
		}


		/**
		 * Get the default settings of a form:
		 * 
		 * @return void
		 */
		private function getDefaultSettings(){

			return array(
				'max_entries' => '',
				'entry_start_unix' => '',
				'entry_start' => '',
				'entry_end_unix' => '',
				'entry_end' => '',
				'no_ajax' => 'false'
			);
		}


		/**
		 * Get the amount of entries currently tied to this form:
		 * 
		 * @return int
		 */
		private function getEntriesCount(){

			global $wpdb;
			$query = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_parent = %s AND post_type = 'form-entry'";
			$post_count = $wpdb->get_var( $wpdb->prepare( $query, $this->id ) );
			return $post_count;

		}



		/*=============================================================*/
		/**             Setters                                        */
		/*=============================================================*/


		/**
		 * Set all settings for this form
		 *
		 * @return void
		 */
		private function setSettings(){

			//regular settings:
			$settings = get_post_meta( $this->id, 'settings', true );
			
			if( !$settings )
				$settings = array();

			$settings = wp_parse_args( $settings, $this->getDefaultSettings() );

			//combined with the post:
			$formPost = get_post( $this->id );
			$post_values = array(

				'title'	=> $formPost->post_title,
				'date'	=> $formPost->post_date,
				'slug'	=> $formPost->post_name

			);

			//check the max_entries field:
			if( $settings['max_entries'] !== '' ){
				$settings['max_entries'] = Tag::postMeta( $settings['max_entries'] );
				$settings['entries_count'] = $this->getEntriesCount();
 			}


			//populate the settings field:
			$this->settings = array_merge( $settings, $post_values );

			//allow filters on all all hard refresh variables:
			$this->submitMethod = apply_filters( 
				'chef_forms_submit_method', 
				$this->submitMethod, 
				$this 
			);

			$this->enctype = apply_filters( 
				'chef_forms_enctype', 
				$this->enctype, 
				$this
			);

			$this->returnLink = get_permalink( Session::rootPostId() ).'#f'.$this->id;
			$this->returnLink = apply_filters( 
				'chef_forms_return_link', 
				$this->returnLink, 
				$this
			);


		}

		/**
		 * Set messages for this form
		 *
		 * @return void
		 */
		private function setMessages(){

			if( !empty( $_SESSION['form_messages'] ) ){

				$this->messages = array_merge( $this->messages, $_SESSION['form_messages'] );
				unset( $_SESSION['form_messages'] );

			}
		}



		/**
		 * Set the validity of this form
		 *
		 * @return void
		 */
		private function setValidity(){

			if( $this->settings['entry_start_unix'] !== '' || $this->settings['entry_end_unix'] !== '' ){

				if( $this->settings['entry_start_unix'] < time() )
					$this->notValid = 'time';

				if( $this->settings['entry_end_unix'] > time() )
					$this->notValid = 'time';

			}else if( $this->settings['max_entries'] !== '' && is_numeric( $this->settings['max_entries'] ) ){

				if( $this->settings['max_entries'] <= $this->settings['entries_count'] ){
					$this->notValid = 'max-entries';
				}


			}

		}


		/**
		 * Set all fields for this form
		 *
		 * @return void
		 */
		private function setFields(){

			$fields = get_post_meta( $this->id, 'fields', true );
			$array = array();

			if( is_array( $fields ) ){
			
				$fields = Sort::byField( $fields, 'position', 'ASC' );
			
				if( $fields ){

					foreach( $fields as $id => $field ){
						
						$type = $field['type'];

						$field = $this->sanitizeFieldArgs( $id, $field );
						
						if( !isset( $field['choices'] ) ){


							$array[] = Field::$type(
									
								$field['name'], 
								$this->id, 
								$field
								
							);
					
						}else{
							
							$array[] = Field::$type(

								$field['name'],
								$this->id,
								$field['choices'],
								$field
							);

						}
				
					}
				}
			}

			$this->fields = $array;
		}




		/**
		 * Set all notifications for this form
		 *
		 * @return void
		 */
		private function setNotifications(){

			$notifications = array();
			$datas = get_post_meta( $this->id, 'notifications', true );

			//if( !empty( $this->files ) ) 
			//	$data['attachments'] = $this->files;

			if( !empty( $datas ) ){
				foreach( $datas as $data ){

					$notifications[] = FormNotification::make( $data, $this->fields );
				
				}
			}
			
			//allow other plugins to filter this stuff:
			$notifications = apply_filters( 'chef_forms_notifications', $notifications, $this );	
			return $notifications;
		}


	
	}