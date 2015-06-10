<?php
namespace ChefForms\Admin;

use Cuisine\Utilities\Session;
use Cuisine\Wrappers\Field;
use ChefForms\Wrappers\FormBuilder;
use ChefForms\Wrappers\NotificationBuilder;
use ChefForms\Wrappers\EntriesManager;


class FormManager{

	/**
	 * Post ID
	 *
	 * @var int
	 */
	private $postId = null;


	/**
	 * Call the methods on construct
	 *
	 * @return \ChefFields\Builder\FormManager
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

		echo '<div class="form-manager" data-form_id="'.$this->postId.'">';

			$this->buildMenu();

			echo '<div class="field-container form-view" id="field-container">';
				
				echo '<h2><span class="dashicons dashicons-hammer"></span>';
				echo __( 'Formulierenbouwer', 'chefforms' ).'</h2>';

				FormBuilder::build();
			echo '</div>';


			echo '<div class="notifications-container form-view current" id="notifications-container">';
				
				echo '<h2><span class="dashicons dashicons-megaphone"></span>';
				echo __( 'Notificaties', 'chefforms' ).'</h2>';
				
				NotificationBuilder::build();
			
			echo '</div>';

			echo '<div class="entries-container form-view" id="entries-container">';
				
				echo '<h2><span class="dashicons dashicons-email-alt"></span>';
				echo __( 'Inzendingen', 'chefforms' ).'</h2>';
				
				EntriesManager::build();
			
			echo '</div>';

			echo '<div class="settings-container form-view" id="settings-container">';
			
				echo '<h2><span class="dashicons dashicons-admin-generic"></span>';
				echo __( 'Instellingen', 'chefforms' ).'</h2>';
			
				$this->buildSettings();
			
			echo '</div>';

		echo '</div>';
	}


	/**
	 * Build the menu for this form
	 * 
	 * @return string ( html, echoed )
	 */
	private function buildMenu(){

		echo '<nav class="form-nav">';

			echo '<span class="nav-btn" data-type="field">';
				echo '<span class="dashicons dashicons-hammer"></span>';
				echo '<b>'.__( 'Formulierbouwer', 'chefforms' ).'</b>';
			echo '</span>';

			echo '<span class="nav-btn current" data-type="notifications">';
				echo '<span class="dashicons dashicons-megaphone"></span>';
				echo '<b>'.__( 'Notificaties', 'chefforms' ).'</b>';
			echo '</span>';

			echo '<span class="nav-btn" data-type="entries">';
				echo '<span class="dashicons dashicons-email-alt"></span>';
				echo '<b>'.__( 'Inzendingen', 'chefforms' ).'</b>';
			echo '</span>';

			echo '<span class="nav-btn nav-link settings" data-type="settings">';
				echo '<span class="dashicons dashicons-admin-generic"></span>';
				echo '<b>'.__( 'Instellingen', 'chefforms' ).'</b>';
			echo '</span>';

		echo '</nav>';
	}

	/**
	 * Create the settings-view
	 * 
	 * @return string, echoed
	 */
	private function buildSettings(){

		echo '<div class="confirmation-settings">';

			$field = Field::text( 'btn-text', 'Knop Text' );
			$field->render();

			$field = Field::editor( 'confirm', 'Bevestigings-bericht' );
			$field->render();

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


		FormBuilder::save( $post_id );
		NotificationBuilder::save( $post_id );

		return true;
	}


}?>