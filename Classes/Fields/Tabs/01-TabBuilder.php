<?php

namespace ChefForms\Fields\Tabs;

use Cuisine\Utilities\Session;
use Cuisine\Wrappers\Field;

class TabBuilder{

	/**
	 * String with the title of this tab
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
	 * @var arraym
	 */
	private $options;


	/**
	 * All fields part of this tab
	 * 
	 * @var array
	 */
	public $fields;



	function __construct(){

		add_filter( 'chef_forms_field_tabs', array( &$this, 'button' ) );

	}



	/**
	 * Make a field tab
	 * 
	 * @param  int $post_id
	 * @return ChefForms\Fields\Tabs\TabBuilder
	 */
	public function make( $title, $name, $options = array() ){
		
		$this->postId = Session::postId();

		$this->slug = $name;
		$this->title = $title;
		$this->options = $this->sanitizeOptions( $options );
		

		return $this;
	}


	/**
	 * Set a field-tab
	 * 
	 * @param [type] $fields [description]
	 */
	public function set( $fields ){

		$this->fields = $fields;
		add_action( 
			'chef_forms_field_tab_content', 
			array( &$this, 'build' ),
			10, 1
		);

	}


	/**
	 * Build this field tab
	 * 
	 * @return String (html)
	 */
	public function build( $fieldBlock ){
			

		echo '<div class="field-settings-'.esc_attr( $this->slug ).' field-setting-tab-content" id="tab-'.esc_attr( $this->slug ).'">';
			echo '<h2>'.esc_html( $this->title ).'</h2>';

			foreach( $this->fields as $field ){

				//set values
				$_name = $field->name;
				$value = $fieldBlock->getProperty( $_name );

				if( $value ){
					$field->properties['defaultValue'] = $value;
				}else{
					$field->properties['defaultValue'] = '';
				}

				$field->setName( 'fields['.$fieldBlock->id.']['. $_name .']' );
				$field->render();
				$field->setName( $_name );	

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
	 * Build the button
	 * 
	 * @return array
	 */
	public function button( $buttons ){

		$buttons[ $this->slug ] = array(
			'label' => $this->title,
			'icon' => $this->options['icon'],
			'position' => $this->options['position']
		);

		return $buttons;
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
			'position'	=> 10
		);


		return wp_parse_args( $options, $defaults );

	}




}
