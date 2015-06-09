<?php

namespace ChefForms\Builders;


class EntriesManager{

	/**
	 * Entries array 
	 * 
	 * @var array
	 */
	public $entries = array();


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
		
		$this->entries = $this->getEntries();

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

		echo 'Entries!';

	}



	/*=============================================================*/
	/**             Getters & Setters                              */
	/*=============================================================*/


	private function getEntries(){

		return array();

	}



}?>