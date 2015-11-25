<?php

	namespace ChefForms\Front;

	use ChefForms\Wrappers\Field;
	use Cuisine\Utilities\Sort;
	use Cuisine\Utilities\Url;
	use Cuisine\Wrappers\Template;
	use ChefForms\Wrappers\Notification;
	
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
		 * Array to be converted to a json object with the callback message
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
		 * Check if this form can be filled in
		 * 
		 * @var boolean / string
		 */
		public $notValid = false;


		private function init(){

			$this->setSettings();
			$this->setValidity();
			$this->setFields();
			
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

			ob_start();

				//if the form can be filled in:
				if( $this->notValid === false ){

					echo '<form class="form form-'.$this->getSetting( 'slug' ).'" id="form_'.$this->id.'"';
	
					if( $this->getSetting( 'maintain_msg' ) === 'true' )
						echo ' data-maintain-msg="true" ';
	
					echo '>';
	
						echo '<div class="form-fields">';
						
							foreach( $this->fields as $field ){
	
								$field->render();
	
							}
	
						echo '</div>';
	
						echo '<div class="form-footer">';
	
							echo '<button class="submit-form">';
	
								echo $this->getSetting( 'btn-text', 'Verstuur' );
	
							echo '</button>';
		
						echo '</div>';
						
						$default = Url::path( 'plugin', 'chef-forms/Templates/Loader' );
						Template::element( 'loader', $default )->display();
	
					echo '</form>';

				}else{

					$default = Url::path( 'plugin', 'chef-forms/Templates/Confirmations/'.$this->notValid.'-error' );
					Template::element( 'forms/'.$this->notValid.'-error', $default )->display();

				}

			$this->html = ob_get_clean();
			return $this;
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


			global $wpdb;

			$query = "SELECT ID FROM {$wpdb->posts}  WHERE post_name = %s";
			$id = $wpdb->get_var( $wpdb->prepare( $query, $name ) );

			if( $id ){

				//set the ID of this form
				$this->id = $id;

				//return the made form:
				return self::make();	
			}

			//return an empty string, basically
			return $this;

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
			$this->init();

			$entry = self::saveEntry( $id );
		
			//allow plugins to hook into this event:
			do_action( 'form_submitted', $this, $entry );
			do_action( 'before_notification', $this, $entry );


			//check if a redirect has been set
			if( !empty( $this->redirect ) ){

				//store this form-session in a php session:
				self::store();

				//return the redirect data
				return json_encode( $this->redirect );
			
			}

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
		 * Save a single entry
		 * 
		 * @param  int $id
		 * @return array $entry
		 */
		public function saveEntry( $id ){

			do_action( 'before_entry_save', $this, $_POST['entry'] );

			$title = 'Inschrijving '.\get_the_title( $id ).' - '.date( 'd-m-Y' );

			$args = array(
				'post_title'	=> 	$title,
				'post_parent' 	=>	$id,
				'post_type' 	=> 	'form-entry',
				'post_status'	=> 	'publish',
				'post_date'		=> 	date( 'Y-m-d H:i:s' ), 
				'post_date_gmt'	=>	date( 'Y-m-d H:i:s' )
			);

			$entryId = wp_insert_post( $args );

			//set entry id in the post global, for easy acces:
			$_POST['entry_id'] = $entryId;
			$entry = $_POST['entry'];

			$entry = apply_filters( 'chef_forms_entry_values', $entry );

			//save all fields
			update_post_meta( $entryId, 'entry', $entry );


			do_action( 'after_entry_save', $this, $entry );


			return $entry;

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
		private function getSetting( $name, $default = false ){

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

			//set vars if these do not exist:
			if( !isset( $settings['max_entries'] ) ){
				$settings['max_entries'] = '';
				$settings['entry_start_unix'] = '';
				$settings['entry_start'] = '';
				$settings['entry_end_unix'] = '';
				$settings['entry_end'] = '';
			}


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

			foreach( $datas as $data ){

				$notifications[] = Notification::make( $data, $this->fields );
				
			}
			
			//allow other plugins to filter this stuff:
			$notifications = apply_filters( 'chef_forms_notifications', $notifications, $this );	
			return $notifications;
		}


	
	}