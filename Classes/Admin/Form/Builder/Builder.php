<?php

namespace ChefForms\Admin\Form\Builder;

use Cuisine\Utilities\Sort;
use ChefForms\Wrappers\Field;
use Cuisine\Wrappers\Field as BF;

class Builder{

	/**
	 * Fields array 
	 * 
	 * @var array
	 */
	public $fields = array();


	/**
	 * Post ID
	 *
	 * @var int
	 */
	private $postId = null;


	/**
	 * Keep the field id's unique and get the highest
	 * 
	 * @var int;
	 */
	private $highestId;



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
		
		$this->fields = $this->getFields();
		$this->highestId = $this->getHighestId();

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

		echo '<div class="form-builder-fields">';


		$currentRow = 0;
		if( !empty( $this->fields ) ){

			echo '<div class="row empty">';
			
			foreach( $this->fields as $field ){

				if( !isset( $field->row ) || $field->row !== $currentRow )
					echo '</div><div class="row">';

				$field->build();
				$currentRow = ( $field->row != '' ? $field->row : $currentRow++ );
			}

			echo '</div>';

		}else{

			echo '<div class="section-wrapper msg">';
				echo '<p>'.__('Nog geen velden aangemaakt.', 'chefforms').'</p>';
			echo '</div>';
		
		}
		echo '</div>';

	}


	/*=============================================================*/
	/**             Saving                                         */
	/*=============================================================*/


	/**
	 * Loop through the fields and save 'em
	 * 
	 * @return bool
	 */
	public function save( $post_id ){

		if( isset( $_POST['fields'] ) ){

			$fields = $_POST['fields'];
			$_fields = array();
			foreach( $fields as $id => $field ){
	
				$_fields[ $id ] = $field;
	
			}
	
			update_post_meta( $post_id, 'fields', $_fields );
	
			return true;
		}

		return false;
		
	}



	/**
	 * Create a field
	 * 
	 * @return void
	 */
	public function createField( $datas = array() ){
		
		$this->init();

		$id = $this->highestId + 1;
		$type = $_POST['type'];
		$form_id = $this->postId;

		//get the defaults:
		$args = $this->getDefaultFieldArgs();
		$args = wp_parse_args( $datas, $args );
		$args['type'] = $type;
		
		//save this field:
		$_fields = get_post_meta( $this->postId, 'fields', true );
		$_fields[ $id ] = $args;
		update_post_meta( $this->postId, 'fields', $_fields );


		$field = Field::$type( $id, $form_id, $args );
		return $field->build();

	}


	/**
	 * Delete a field
	 * 
	 * @return void
	 */
	public function deleteField(){

		$field_id = $_POST['field_id'];
		$_fields = get_post_meta( $this->postId, 'fields', true );
		unset( $_fields[ $field_id ] );
		update_post_meta( $this->postId, 'fields', $_fields );
		echo 'true';
	}




	/*=============================================================*/
	/**             Getters & Setters                              */
	/*=============================================================*/

	/**
	 * Fetches the info from the database and populates it with section-objects
	 * 
	 * @return array
	 */
	private function getFields(){

		global $post;

		//get the field post-meta:
		$fields = get_post_meta( $this->postId, 'field' );

		//fallback for older versions:
		if( !$fields )
			$fields = get_post_meta( $this->postId, 'fields', true );


		$array = array();


		if( is_array( $fields ) ){
		
			$fields = Sort::byField( $fields, 'position', 'ASC' );
		
			if( $fields ){

				foreach( $fields as $id => $field ){
					
					$type = $field['type'];
					if( !is_string( $type ) )
						continue;

					$array[] = Field::$type( $id, $this->postId, $field );
			
				}
			}
		}

		return $array;
	}


	/**
	 * Returns a filterable array of default settings
	 *
     * @filter 'chef_sections_default_section_args'
	 * @return array
	 */
	private function getDefaultFieldArgs(){

		global $post;
		if( isset( $post ) )
			$post_id = $post->ID;

		$args = array(
				'position'		=> ( count( $this->fields ) + 1 ),
				'label'			=> 'Label',
				'placeholder'	=> false,
				'required'		=> false
		);

		$args = apply_filters( 'chef_forms_default_field_args', $args );

		return $args;
	}



	/**
	 * Loops through all fields and brings back the highest ID,
	 * making sure all ID's are unique
	 * 
	 * @return Int
	 */
	private function getHighestId(){

		$highID = 0;

		if( !empty( $this->fields ) ){

			foreach( $this->fields as $field ){

				if( $field->id > $highID )
					$highID = $field->id;

			}

		}

		return $highID;

	}


}?>