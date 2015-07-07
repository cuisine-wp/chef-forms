<?php

namespace ChefForms\Builders;

use Cuisine\Wrappers\Field;

class FormPanel{


	/**
	 * All fields part of this panel
	 * 
	 * @var void
	 */
	public $fields;


	/**
	 * Post ID
	 *
	 * @var int
	 */
	public $postId = null;




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
	 * @return ChefForms\Builders\FormPanel
	 */
	public function init(){
		
		global $post;

		if( isset( $post ) )
			$this->postId = $post->ID;
		

		return $this;
	}

	/**
	 * Build this section
	 * 
	 * @return String (html)
	 */
	public function build(){

		$html = '';

		foreach( $this->fields as $field ){

			$html .= $field->render();

		}

		return $html;
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

		foreach( $this->fields as $field ){

			$name = $field['name'];

			if( isset( $_POST[ $name ] ) ){
	
				$notifications = $_POST[ $name ];
			
				update_post_meta( $post_id, $name, $_notifications );
			
				return true;
			}

		}	
	}


}
