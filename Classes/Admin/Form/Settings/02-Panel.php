<?php

namespace ChefForms\Admin\Form\Settings;

use Cuisine\Utilities\Session;
use Cuisine\Wrappers\Field;

class Panel{

	/**
	 * String with the title of this panel
	 * 
	 * @var string
	 */
	private $title;

	/**
	 * String with this slug
	 * 
	 * @var string
	 */
	private $slug;

	/**
	 * Array containing all options
	 * 
	 * @var array
	 */
	private $options;


	/**
	 * All fields part of this panel
	 * 
	 * @var array
	 */
	public $fields;


	/**
	 * Post ID
	 *
	 * @var int
	 */
	public $postId = null;



	function __construct(){

		add_action( 'panel_save', array( &$this, 'save' ) );

	}



	/**
	 * Make a form panel
	 * 
	 * @param  int $post_id
	 * @return ChefForms\Builders\SettingsPanel
	 */
	public function make( $name, $title, $options = array() ){
		
		$this->postId = Session::postId();

		$this->slug = $name;
		$this->title = $title;
		$this->options = $this->sanitizeOptions( $options );
		

		return $this;
	}


	/**
	 * Set a form-panel
	 * 
	 * @param [type] $fields [description]
	 */
	public function set( $fields ){

		$this->fields = $fields;

		add_action( 'chef_forms_panels', array( &$this, 'build' ) );

	}


	/**
	 * Build this section
	 * 
	 * @return String (html)
	 */
	public function build(){
		
		echo '<div class="settings-panel '.sanitize_title( $this->title ).'">';


		if( $this->get('icon' ) )
			echo '<img src="'.esc_url( $this->get('icon') ).'" class="panel-icon">';

		echo '<h2>'.$this->title.'</h2>';

		if( $this->get( 'content' ) )
			echo wpautop( $this->get( 'content' ) );


		foreach( $this->fields as $field ){

			//set values
			$value = get_post_meta( Session::postId(), $field->name, true );
			if( $value )
				$field->properties['defaultValue'] = $value;


			$field->render();

		}

		//render the javascript-templates seperate, to prevent doubles
		$rendered = array();
						
		foreach( $this->fields as $field ){
						
			if( method_exists( $field, 'renderTemplate' ) && !in_array( $field->name, $rendered ) ){
					
					echo $field->renderTemplate();
					$rendered[] = $field->name;
						
			}
		}	


		echo '</div>';

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

			$name = $field->name;

			if( isset( $_POST[ $name ] ) ){
			
				update_post_meta( $post_id, $name, $_POST[ $name ] );

			}

		}	

	}


	/*=============================================================*/
	/**             Getters & Setters                              */
	/*=============================================================*/


	/**
	 * Checks if an option is set, then returns it.
	 * 
	 * @param  string $name
	 * @return mixed
	 */
	private function get( $name ){

		if( isset( $this->options[ $name ] ) )
			return $this->options[ $name ];

		return false;

	}



	/**
	 * Set the options with defaults
	 * 
	 * @param  array $options
	 * @return array
	 */
	private function sanitizeOptions( $options ){

		$defaults = array(
						'icon'		=> false,
						'content'	=> false
		);


		return wp_parse_args( $options, $defaults );

	}




}
