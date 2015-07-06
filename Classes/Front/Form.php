<?php

	namespace ChefForms\Front;

	use Cuisine\Wrappers\Field;
	use Cuisine\Utilities\Sort;
	use Cuisine\View\Template;
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
		private $fields = array();


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
		private $message = array();


		/**
		 * All of this forms settings
		 * 
		 * @var array
		 */
		private $settings = array();





		private function init(){

			$this->setSettings();
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
		public function make( $id ){

			$this->id = $id;
			$this->init();

			ob_start();

				echo '<form class="form form-'.$this->getSetting( 'slug' ).'" id="form_'.$this->id.'">';

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

					Template::loader();

				echo '</form>';

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
			$entry = $_POST['entry'];

			update_post_meta( $entryId, 'entry', $entry );

			//notify everybody
			$this->notify();


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

			$this->setNotifications();

			if( !empty( $this->notifications ) ){

				foreach( $this->notifications as $notification ){

					$this->notification->send();
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
		private function getFieldArgs( $field ){

			$arr = array();

			if( isset( $field['placeholder' ] ) )
				$arr['placeholder'] = $field['placeholder'];

			if( isset( $field['defaultValue'] ) )
				$arr['defaultValue'] = $field['defaultValue'];

			if( isset( $field['required'] ) )
				$arr['required'] = $field['required'];


			if( isset( $field['choices'] ) ){

				$choices = array();
				foreach( $field['choices'] as $val ){

					$choices[ $val['key'] ] = $val['label'];

				}

				$arr['choices'] = $choices;
			}


			//get the label value from the settings:
			$arr['label'] = $this->getSetting( 'labels', 'top' );

			return $arr;
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


			//combined with the post:
			$formPost = get_post( $this->id );
			$post_values = array(

				'title'	=> $formPost->post_title,
				'date'	=> $formPost->post_date,
				'slug'	=> $formPost->post_name

			);


			$this->settings = array_merge( $settings, $post_values );
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

						$field_id = 'field_'.$this->id.'_'.$id;
						$type = $field['type'];
						$args = $this->getFieldArgs( $field );
						

						if( !isset( $field['choices'] ) ){

							$array[] = Field::$type(

								$field_id,
								$field['label'],
								$args
							);

						}else{
							
							$array[] = Field::$type(

								$field_id,
								$field['label'],
								$args['choices'],
								$args
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
			
			return $notifications;
		}


	
	}