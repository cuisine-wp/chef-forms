<?php
namespace CuisineForms\Admin;

use Cuisine\Utilities\Sort;

class FormBuilder {

	/**
	 * Get the post id for this form:
	 *
	 * @var integer
	 */
	var $id = 0;

	/**
	 * Title of this new form
	 *
	 * @var string
	 */
	var $title;


	/**
	 * Boolean to see if this form already exists
	 *
	 * @var boolean
	 */
	var $exists = false;


	/**
	 * An array of fields this form can have
	 *
	 * @var array
	 */
	var $fields = array();



	/**
	 * Init a form builder and return this object
	 *
	 * @param  string $title
	 * @param  array  $options
	 * @return \CuisineForms\Admin\FormBuilder
	 */
	public function make( $title, $options = array() ){

		$this->title = $title;
		$this->options = $this->sanitizeOptions( $options );

		//check if this form already exists:
		$this->exists = $this->checkExistence();


		return $this;

	}


	/**
	 * Trigger the save functions
	 *
	 * @param array $fields array of field objects
	 */
	public function set( $fields ){


		if( !$this->exists || $this->options['force-overwrite'] ){

			$this->fields = $fields;

			//create the post:
			$args = array(
				'post_title'	=> $this->title,
				'post_name'		=> sanitize_title( $this->title ),
				'post_type'		=> 'form',
				'post_status'	=> 'publish'
			);

			$this->id = wp_insert_post( $args, true );

			//errors dont get any further:
			if( is_wp_error( $this->id ) )
				return false;


			//update the gatekeeper:
			if( !$this->exists ){

				$forms = get_option( 'existingForms', array() );
				$forms[ $this->id ] = $this->title;
				update_option( 'existingForms', $forms );

			}else{
				//overwrite a certain form:
				$this->updateFormID( $this->title, $this->id );
			}

			//save the meta-data:
			$this->saveFields();
			$this->saveSettings();

		}

		return $this;
	}


	/**
	 * Get the form based on the first given title
	 *
	 * @param  String $title
	 * @return Int
	 */
	public function get( $title ){

		$allForms = get_option( 'existingForms', array() );

		foreach( $allForms as $id => $formTitle ){

			if( $formTitle == $title )
				return $id;

		}

		return false;

	}


	/**
	 * Save all fields
	 *
	 * @return void
	 */
	private function saveFields(){

		$fields = array();

		$i = 0;

		foreach( $this->fields as $field ){

            if( method_exists( $field, 'getProperty' ) ){
                    
                $row = $i + 1;
                $position = $i + 1;
                $classes = $this->getClasses( $field );
                $validation = $this->getValidation( $field );

                $fields[ $i ] = array(
                    'label'			=> ( $field->label != '' ? $field->label : $field->name ),
                    'placeholder'	=> $field->getProperty( 'placeholder' ),
                    'deletable'		=> $field->getProperty( 'deletable' ),
                    'defaultValue'	=> $field->getDefault(),
                    'required'		=> ( $field->getProperty( 'required' ) ? 'true' : 'false' ),
                    'row'			=> ( $field->getProperty( 'row' ) ? $field->getProperty( 'row' ) : $row ),
                    'type'			=> ( $field->getProperty( 'field_type' ) ? $field->getProperty( 'field_type' ) : $field->type ),
                    'position'		=> ( ( $field->getProperty( 'position', 0 ) > 0 ) ? $field->getProperty( 'position' ) : $position ),
                    'classes'		=> $classes,
                    'validation'	=> $validation
                );
  
                if( $field->getProperty('options') ){
                    $fields[$i]['options'] = $field->getProperty('options');
                    $fields[$i]['choices'] = $field->getProperty('options');
                }

                $i++;
            }
		}

		//save it:
		update_post_meta( $this->id, 'fields', $fields );

	}

	/**
	 * Returns the field's classes
	 * 
	 * @param  Cuisine\Wrappers\Field $field
	 * 
	 * @return String
	 */
	public function getClasses( $field )
	{
		$classes = [];
		if( $field->getProperty( 'classes', false ) ){
			if( is_array( $field->getProperty( 'classes' ) ) ){
				$classes = $field->getProperty( 'classes' );
			}else{
				$classes = explode( ' ', $field->getProperty( 'classes' ) );
			}
		}

		if( $field->getProperty( 'class', false ) )
			$classes[] = $field->getProperty( 'class' );
		
		if( is_array( $classes ) )
			return implode( ' ', $classes );
	
		return '';
	}

	/**
	 * Return validation strings
	 * 
	 * @param  Cuisine\Wrappers\Field $field
	 * 
	 * @return String
	 */
	public function getValidation( $field )
	{
		$validation = [];
		if( $field->getProperty( 'validation', false ) ){
			if( is_array( $field->getProperty( 'validation' ) ) ){
				$validation = $field->getProperty( 'validation' );
			}else{
				$validation = explode( ' ', $field->getProperty( 'validation' ) );
			}
		}

		if( $field->getProperty( 'validate', false ) )
			$validation[] = $field->getProperty( 'validate' );
		
		return implode( ' ', $validation );	
	}


	/**
	 * Save all settings
	 *
	 * @return void
	 */
	private function saveSettings(){

		$settings = $this->options;
		unset( $settings['force-overwrite'] );

		update_post_meta( $this->id, 'settings', $settings );

	}



	/**
	 * Check to see if this form is already build:
	 *
	 * @return bool
	 */
	private function checkExistence(){

		$createdForms = get_option( 'existingForms', array() );

		if( in_array( $this->title, $createdForms ) )
			return true;

		return false;

	}


	/**
	 * Sanitize the form options and set the defaults:
	 *
	 * @param  array $options
	 * @return array
	 */
	private function sanitizeOptions( $options ){


		if( !isset( $options['btn-text'] ) )
			$options['btn-text'] = 'Verstuur';

		if( !isset( $options['labels'] ) )
			$options['labels'] = 'top';

		if( !isset( $options['confirm'] ) )
			$options['confirm'] = '{{ alle_velden }}';

		if( !isset( $options['force-overwrite'] ) )
			$options['force-overwrite'] = false;


		return $options;
	}


	/**
	 * Update the ID of a certain form in Existing Forms:
	 *
	 * @param  string $title
	 * @param  int $newId new Form ID
	 * @return void
	 */
	public function updateFormID( $title, $newId ){

		$allForms = get_option( 'existingForms', array() );

		//get from the options table:
		foreach( $allForms as $id => $formTitle ){

			if( strtolower( $formTitle ) == strtolower( $this->title ) ){
				unset( $allForms[ $id ] );
				$allForms[ $newId ] = $formTitle;
			}
		}

		update_option( 'existingForms', $allForms );

	}


}

