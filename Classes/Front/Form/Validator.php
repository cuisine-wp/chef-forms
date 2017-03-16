<?php

	namespace ChefForms\Front\Form;

	use ChefForms\Wrappers\Entry;

	class Validator{

		/**
		 * The Form class
		 * 
		 * @var ChefForms\Front\Form\Form
		 */
		protected $form;

		/**
		 * The entry we're validating
		 * 
		 * @var Array
		 */
		protected $entry;


		/**
		 * Message bag with errors
		 * 
		 * @var null | Array
		 */
		protected $message = null;


		/**
		 * All validators in an array
		 * 
		 * @var array
		 */
		protected $validators = [ 'required' ];


		/**
		 * Constructor
		 *
		 * @param ChefForms\Front\Form\Form $form
		 *
		 * @return void
		 */
		public function __construct( $form )
		{
			$this->form = $form;
			$this->entry = Entry::map( $form );

			cuisine_dump( $this->entry );
			die();
		}


		/**
		 * Checks to see wether all validations are valid
		 * 
		 * @return boolean
		 */
		public function isValid()
		{
			$valid = true;

			foreach( $this->validators as $name ){

				$method = 'validate'.ucfirst( $name );
				if( !$this->{$method}() )
					$valid = false;

			}

			return $valid;
		}




		/**
		 * Validates all required fields
		 * 
		 * @return bool
		 */
		public function validateRequired()
		{
			$valid = true;

			foreach( $this->form->fields as $field ){

				if( 
					$field->getProperty( 'required' ) &&
					( 
						!isset( $_POST[ $field->name ] ) ||
						$_POST[ $field->name ] == '' ||
						is_null( $_POST[ $field->name ] )
					)
				){
					$this->setMessage( sprintf( __( '%s is a required field', 'chefforms' ), $field->name ) );
					$valid = false;
				}

			}

			return $valid;
		}


		/**
		 * Sets an error message
		 * 
		 * @param String $string
		 *
		 * @return void
		 */
		public function setMessage( $string )
		{
			$this->message = [ 'error' => true, 'msg' => $string ];
		}

	}