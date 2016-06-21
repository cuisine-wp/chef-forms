<?php

namespace ChefForms\Admin\Form\Settings;

use Cuisine\Wrappers\Field;
use Cuisine\Utilities\Session;

class Manager{

	/**
	 * Settings array 
	 * 
	 * @var array
	 */
	public $settings = array();


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
		
		$this->settings = $this->getSettings();

		return $this;
	}


	/*=============================================================*/
	/**             Metabox functions                              */
	/*=============================================================*/

	/**
	 * Save the settings:
	 * 
	 * @return bool $success
	 */
	public function save( $post_id ){

		//save all settings:
		if( !empty( $_POST['settings'] ) ){

			$settings = $this->sanitizeSettings( $_POST['settings'] );

			$_settings = array();
			foreach( $settings as $id => $setting ){

				$_settings[ $id ] = $setting;
			
			}
			
			update_post_meta( $post_id, 'settings', $_settings );

			do_action( 'panel_save', $post_id );

			return true;
		}

		return false;
	}


	/**
	 * Clean up certain setting aspects, if needed:
	 * 
	 * @param  array $setting
	 * @return array $setting
	 */
	private function sanitizeSettings( $settings ){

		if( $settings['entry_start'] != '' ){
			$settings['entry_start_unix'] = strtotime( $settings['entry_start'] );
		}else{
			$settings['entry_start_unix'] = '';
		}

		if( $settings['entry_end'] != '' ){
			$settings['entry_end_unix'] = strtotime( $settings['entry_end'] );
		}else{
			$settings['entry_end_unix'] = '';
		}

		//save the editor field:
		$fields = $this->getFields();
		foreach( $fields as $field ){

			if( $field->type == 'editor' ){
			
				$name = str_replace( array( 'settings[', ']' ), '', $field->name );
				$settings[ $name ] = $_POST[ $field->id ];

			}
		}

		return $settings;
	}


	/**
	 * Output the settings page
	 *
	 * @return void (echoes html)
	 */
	public function build(){

		echo '<div class="confirmation-settings">';

			global $post;
			$fields = $this->getFields();

			echo '<span class="shortcode">Shortcode: <ins>[cuisine_form id="'.Session::postId().'"]</ins></span>';

			foreach( $fields as $field ){

				$field->render();

			}



			echo '<div class="form-panels">';
				do_action( 'chef_forms_panels' );
			echo '</div>';

		echo '</div>';


	}


	private function getFields(){

		$fields = array(


			Field::text( 
				'settings[btn-text]',
				__('Button text','chefforms'),
				array(
					'defaultValue'	=> $this->getSetting( 'btn-text', __('Send','chefforms') )
				)
			),
			Field::select( 
				'settings[labels]',
				'Labels',
				array(
					false 	=> __('No labels','chefforms'),
					'top'	=> __('Labels above','chefforms'),
					'left'	=> __('Labels left','chefforms')
				),
				array(
					'defaultValue'	=> $this->getSetting( 'labels', 'top' )
				)
			),
			Field::editor( 
				'settings[confirm]',
				__('Confirmation-message','chefforms'),
				array(
					'defaultValue'	=> $this->getSetting( 'confirm', __('Your message has been successfully sent. We will contact you very soon!', 'chefforms' ) )
				)
			),

			Field::text(
				'settings[max_entries]',
				__('Max number of submissions','chefforms'),
				array(
					'defaultValue' => $this->getSetting( 'max_entries', '' )
				)
			),

			Field::date(
				'settings[entry_start]',
				__('Valid from','chefforms'),
				array(
					'defaultValue' => $this->getSetting( 'entry_start', '' )
				)
			),

			Field::date(
				'settings[entry_end]',
				__('Valid to','chefforms'),
				array(
					'defaultValue' => $this->getSetting( 'entry_end', '' )
				)
			),

			Field::checkbox(
				'settings[maintain_msg]',
				__('Leave confirmation message on the page after form submission','chefforms'),
				array(
					'defaultValue' => $this->getSetting( 'maintain_msg', 'false' )
				)
			),

			Field::checkbox(
				'settings[no_ajax]',
				__('Never use ajax for submitting this form','chefforms'),
				array(
					'defaultValue' => $this->getSetting( 'no_ajax', 'false' )
				)
			)
		);

		$fields = apply_filters( 'chef_forms_setting_fields', $fields );
		return $fields;

	}


	/*=============================================================*/
	/**             Getters & Setters                              */
	/*=============================================================*/



	/**
	 * Get all settings
	 * 
	 * @return array
	 */
	private function getSettings(){

		$settings = get_post_meta( $this->postId, 'settings', true );
		return $settings;
	}



	/**
	 * Return a setting
	 * 
	 * @param  string  $name
	 * @param  boolean $default
	 * @return string
	 */
	private function getSetting( $name, $default = false ){

		if( isset( $this->settings[ $name ] ) )
			return $this->settings[ $name ];

		return $default;


	}




}?>