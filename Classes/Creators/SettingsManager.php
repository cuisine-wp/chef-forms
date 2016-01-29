<?php

namespace ChefForms\Creators;

use Cuisine\Wrappers\Field;

class SettingsManager{

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
				'Knop Text',
				array(
					'defaultValue'	=> $this->getSetting( 'btn-text', 'Verstuur' )
				)
			),
			Field::select( 
				'settings[labels]',
				'Labels',
				array(
					false 	=> 'Geen labels',
					'top'	=> 'Labels boven',
					'left'	=> 'Labels links'
				),
				array(
					'defaultValue'	=> $this->getSetting( 'labels', 'top' )
				)
			),
			Field::editor( 
				'settings[confirm]',
				'Bevestigings-bericht',
				array(
					'defaultValue'	=> $this->getSetting( 'confirm', __('Hartelijk dank voor uw bericht, we nemen zo spoedig mogelijk contact met u op', 'chef-forms' ) )
				)
			),

			Field::text(
				'settings[max_entries]',
				'Maximaal aantal berichten',
				array(
					'defaultValue' => $this->getSetting( 'max_entries', '' )
				)
			),

			Field::date(
				'settings[entry_start]',
				'Geldig vanaf',
				array(
					'defaultValue' => $this->getSetting( 'entry_start', '' )
				)
			),

			Field::date(
				'settings[entry_end]',
				'Geldig tot',
				array(
					'defaultValue' => $this->getSetting( 'entry_end', '' )
				)
			),

			Field::checkbox(
				'settings[maintain_msg]',
				'Laat bevestigings-bericht staan',
				array(
					'defaultValue' => $this->getSetting( 'maintain_msg', 'false' )
				)
			),

			Field::checkbox(
				'settings[no_ajax]',
				'Gebruik nooit ajax voor dit formulier',
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