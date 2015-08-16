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

			$settings = $_POST['settings'];
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
	 * Output the settings page
	 *
	 * @return void (echoes html)
	 */
	public function build(){

		echo '<div class="confirmation-settings">';

			$field = Field::text( 
				'settings[btn-text]',
				'Knop Text',
				array(
					'defaultValue'	=> $this->getSetting( 'btn-text', 'Verstuur' )
				)
			);
			$field->render();


			$field = Field::select( 
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
			);
			$field->render();


			$field = Field::editor( 
				'settings[confirm]',
				'Bevestigings-bericht',
				array(
					'defaultValue'	=> $this->getSetting( 'confirm', '{{ alle_velden }}' )
				)
			);

			$field->render();

			echo '<div class="form-panels">';
				do_action( 'chef_forms_panels' );
			echo '</div>';

		echo '</div>';


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