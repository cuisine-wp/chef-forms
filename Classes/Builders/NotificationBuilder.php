<?php

namespace ChefForms\Builders;

use Cuisine\Wrappers\Field;

class NotificationBuilder{

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
	private $postId = null;




	/**
	 * Call the methods on construct
	 *
	 * @return \ChefFields\Builder\FormBuilde
	 */
	function __construct(){

		$this->init();

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


		$subFields = array(

				Field::email(
					'to',
					'E-mail naar',
					array(
						'placeholder' 	=> 'E-mail naar',
						'label'			=> true,
						'defaultValue'	=> '{admin_email}'
					)
				),

				Field::text(
					'title',
					'Onderwerp',
					array(
						'placeholder' 	=> 'Title',
						'label'			=> true
					)
				),

				Field::editor( 
					'content_{uniq}', //this needs a unique id 
					'', 
					array(
						'label'				=> false,
						'defaultValue' 		=> '{alle_velden}'
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

		$field->render();

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

		
	}

	/*=============================================================*/
	/**             Getters & Setters                              */
	/*=============================================================*/


	private function getNotifications(){

		return array('notifications' => array(
						'title'				=> 'Notificatie',
						'content_{uniq}'	=> '{alle_velden}'
		));

	}



}?>