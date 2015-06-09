<?php

namespace ChefForms\Builders;


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

		echo 'Notifications!';

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

		return array();

	}



}?>