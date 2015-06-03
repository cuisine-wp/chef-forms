<?php

namespace ChefForms\Builder;


use Cuisine\Utilities\Session;
use Cuisine\Utilities\Sort;
use ChefForms\Wrappers\FieldBlock as Field;

class FormBuilder{

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


		wp_nonce_field( Session::nonceAction, Session::nonceName );

		echo '<div class="field-container" id="field-container">';


		if( !empty( $this->fields ) ){

			
			foreach( $this->fields as $field ){

				$field->build();
			}


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
	 * Loop through each section and save 'em
	 * 
	 * @return bool
	 */
	public function save( $post_id ){

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

	    $nonceName = (isset($_POST[Session::nonceName])) ? $_POST[Session::nonceName] : Session::nonceName;
	    if (!wp_verify_nonce($nonceName, Session::nonceAction)) return;



		if( isset( $_POST['fields'] ) ){

			$fields = $_POST['fields'];

			//save fields
			foreach( $fields as $field ){

			}



			//save the main field meta:
			update_post_meta( $post_id, 'fields', $fields );

			//save other meta-data	
		}
			

		return true;
	}


	/**
	 * Delete section
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


	/**
	 * Save the order of fields
	 * 
	 * @return bool (success / no success)
	 */
	public function sortFields(){

		$ids = $_POST['field_ids'];

		//save this section:
		$_fields = get_post_meta( $this->postId, 'fields', true );
		
		$i = 1;
		foreach( $ids as $field_id ){
			$_fields[ $field_id ]['position'] = $i;
			$i++;
		}

		update_post_meta( $this->postId, 'sections', $_fields );
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
		$fields = get_post_meta( $this->postId, 'fields', true );
		$array = array();
		
		$fields = array(

				array(
						'type'		=> 'text',
						'id'		=> '1',
						'formId'	=> $post->ID,
						'position'	=> 0
				),

				array(
						'type'		=> 'textarea',
						'id'		=> '1',
						'formId'	=> $post->ID,
						'position'	=> 1
				),

				array(
						'type'		=> 'radio',
						'id'		=> '1',
						'formId'	=> $post->ID,
						'position'	=> 2
				),

		);


		if( is_array( $fields ) ){
		
			$fields = Sort::byField( $fields, 'position', 'ASC' );
		
			if( $fields ){

	
				foreach( $fields as $field ){

					$type = $field['type'];
					$id = $field['id'];
					$form_id = $field['formId'];
					$position = $field['position'];
	
					$array[] = Field::$type( $id, $form_id, $position );
			
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

				'id'			=> $this->highestId,
				'post_id'		=> $post_id,
				'type'			=> 'text'
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