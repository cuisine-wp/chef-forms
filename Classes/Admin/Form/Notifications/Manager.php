<?php

namespace ChefForms\Admin\Form\Notifications;

use Cuisine\Wrappers\Field;

class Manager{

	/**
	 * Notifications array 
	 * 
	 * @var array
	 */
	public $notifications = array();


	/**
	 * Post ID
	 *
	 * @var int
	 */
	public $postId = null;


	/**
	 * Overwrite de default fields array
	 * 
	 * @var array
	 */
	public $fields = array();




	/**
	 * Call the methods on construct
	 *
	 * @return \ChefFields\Builder\FormBuilde
	 */
	function __construct(){

		$this->init();
		$this->fields = $this->getFields();

		return $this;
	}


	/**
	 * Initiate this class
	 * 
	 * @param  int $post_id
	 * @return void
	 */
	public function init(){
		
		global $post;

		if( isset( $post ) )
			$this->postId = $post->ID;
		
		$this->notifications = $this->getNotifications();

		return $this;
	}


	/*=============================================================*/
	/**             Metabox functions                              */
	/*=============================================================*/

	/**
	 * Get all fields
	 *
	 * @return void (echoes html)
	 */
	public function build(){

		$this->fields->render();

	}


	/*=============================================================*/
	/**             Saving                                         */
	/*=============================================================*/


	/**
	 * Loop through all fields and save 'em
	 * 
	 * @return bool
	 */
	public function save( $post_id ){

		if( isset( $_POST['notifications'] ) ){


			$_notifications = $this->fields->getFieldValues();
			update_post_meta( $post_id, 'notifications', $_notifications );
		
			return true;
		}
		
	}


	/*=============================================================*/
	/**             Getters & Setters                              */
	/*=============================================================*/

	public function getFields(){

		$subFields = array(

				Field::text(
					'to',
					__( 'E-mail to', 'chefforms' ),
					array(
						'placeholder' 	=> __( 'E-mail to', 'chefforms' ),
						'label'			=> true,
						'defaultValue'	=> '{{ admin_email }}'
					)
				),

				Field::text(
					'title',
					__( 'Subject', 'chefforms' ),
					array(
						'placeholder' 	=> 'Title',
						'label'			=> true
					)
				),

				Field::editor( 
					'content', //this needs a unique id 
					'', 
					array(
						'label'				=> false,
						'defaultValue' 		=> '{{ alle_velden }}'
					)
				)
		);

		$field = Field::repeater(
			'notifications',
			'',
			$subFields,
			array(
				'defaultValue' 	=> $this->notifications,
				'label'			=> false
			)
		);

		return $field;
	}



	private function getNotifications(){

		return array('notifications' => array(
						'title'				=> __( 'Notification', 'chefforms' ),
						'content_{uniq}'	=> '{{ alle_velden }}'
		));

	}



}?>