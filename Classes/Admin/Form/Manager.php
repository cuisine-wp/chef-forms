<?php
namespace CuisineForms\Admin\Form;

use Cuisine\Utilities\Session;
use Cuisine\Wrappers\Field;
use CuisineForms\Wrappers\FormBuilderManager;
use CuisineForms\Wrappers\NotificationManager;
use CuisineForms\Wrappers\SettingsManager;
use CuisineForms\Wrappers\Toolbar;


class Manager{

	/**
	 * Post ID
	 *
	 * @var int
	 */
	private $postId = null;


	/**
	 * Call the methods on construct
	 *
	 * @return \ChefFields\Admin\Form\Manager
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

		if( get_post_type() !== 'form' )
			return false;

		wp_nonce_field( Session::nonceAction, Session::nonceName );

		echo '<div class="form-manager" data-form_id="'.esc_attr( $this->postId ).'">';

			Toolbar::build();

			$class = 'field-container form-view';
			if( !isset( $_GET['entry_page'] ) )
				$class .= ' active';

			echo '<div class="'.esc_attr( $class ).'" id="field-container">';

				echo '<h2><span class="dashicons dashicons-hammer"></span>';
				echo __( 'Form builder', 'cuisineforms' ).'</h2>';

				FormBuilderManager::build();
				
			echo '</div>';


			echo '<div class="notifications-container form-view" id="notifications-container">';
				
				echo '<h2><span class="dashicons dashicons-megaphone"></span>';
				echo __( 'Notifications', 'cuisineforms' ).'</h2>';
				
				NotificationManager::build();
			
			echo '</div>';


			echo '<div class="settings-container form-view" id="settings-container">';
			
				echo '<h2><span class="dashicons dashicons-admin-generic"></span>';
				echo __( 'Settings', 'cuisineforms' ).'</h2>';
			
				SettingsManager::build();
			
			echo '</div>';

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

		//check the nonce:
	    $nonceName = (isset($_POST[Session::nonceName])) ? $_POST[Session::nonceName] : Session::nonceName;
	    if (!wp_verify_nonce($nonceName, Session::nonceAction)) return;

	    //check the post-type
	    if( get_post_type( $post_id ) !== 'form' )
	    	return;


			FormBuilderManager::save( $post_id );
			NotificationManager::save( $post_id );
			SettingsManager::save( $post_id );


		return true;
	}



}?>