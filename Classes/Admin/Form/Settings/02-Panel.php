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
	public $title;

	/**
	 * String with this slug
	 * 
	 * @var string
	 */
	public $slug;

	/**
	 * Array containing all options
	 * 
	 * @var array
	 */
	public $options;


	/**
	 * Values of all settings
	 * 
	 * @var array
	 */
	public $values;


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
		$values = get_post_meta( $this->postId, $this->slug, true );
		if( !$values ) $values = [];

		$this->values = $values;


		return $this;
	}


	/**
	 * Set a form-panel
	 * 
	 * @param [type] $fields [description]
	 */
	public function set( $fields ){

		$this->fields = $fields;

		add_action( 'chef_forms_render_settings_panels', array( &$this, 'build' ) );
		add_action( 'chef_forms_form_settings_nav', array( &$this, 'navigation' ) );
		add_action( 'chef_forms_form_settings_save', array( &$this, 'save' ) );

	}


	/**
	 * Returns the navigational button
	 * 
	 * @return string
	 */
	public function navigation()
	{

		$active = '';
		$classes = ( is_array( $this->get( 'classes' ) ) ? $this->get( 'classes' ) : array() );
		if( in_array( 'active', $classes ) )
			$active = ' active';

		echo '<li class="form-nav-item '.$active.'" data-slug="'.$this->slug.'">';
			echo $this->title;
		echo '</li>';
	}


	/**
	 * Build this section
	 * 
	 * @return String (html)
	 */
	public function build(){
		

		$class = $this->getClass();
		echo '<div class="settings-panel '.$class.'" id="panel-'.$this->slug.'">';

		

		echo '<h2>';

			echo $this->getIcon();
			echo $this->title;

		echo'</h2>';

		if( $this->get( 'content' ) )
			echo wpautop( $this->get( 'content' ) );


		foreach( $this->fields as $field ){

			if( isset( $this->values[ $field->name ] ) )
				$field->properties['defaultValue'] = $this->values[ $field->name ];

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

	/**
	 * Returns the class of this panel
	 * 
	 * @return string
	 */
	public function getClass()
	{
		$class = sanitize_title( $this->title );

		if( $this->get( 'classes' ) && is_array( $this->get( 'classes' ) ) )
			$class .= ' '.implode( ' ', $this->get( 'classes' ) );

		return $class;

	}

	/**
	 * Returns the icon of this panel
	 * 
	 * @return string
	 */
	public function getIcon()
	{
		$html = '';
		if( $this->get('icon' ) ){

			if( strpos( $this->get( 'icon' ), 'dashicon' ) !== false ){
				$html = '<span class="'.$this->get( 'icon' ).'"></span>';
			}else{
				$html = '<img src="'.esc_url( $this->get('icon') ).'" class="panel-icon">';
			}
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

		$save = [];

		foreach( $this->fields as $field ){

			$name = $field->name;
			if( isset( $_POST[ $name ] ) )
				$save[ $name ] = $_POST[ $name ];

		}	

		if( !empty( $save ) )
			update_post_meta( $post_id, $this->slug, $save );

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

		if( 
			isset( $this->options[ $name ] ) && 
			!empty( $this->options[ $name ]	) &&
			$this->options[ $name ] != null &&
			$this->options[ $name ] != '' 
		)
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
