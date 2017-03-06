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
	public $panels = array();


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
		
		return $this;
	}



	/**
	 * Build the main tabs
	 * 
	 * @return string (html, echoed)
	 */
	public function build()
	{

		echo '<div class="settings-panel-wrapper">';

			do_action( 'chef_forms_render_settings_panels', $this );

		echo '</div>';
	}


	/**
	 * Build out the navigation
	 * 
	 * @return string (html, echoed)
	 */
	public function buildNavigation()
	{
		ob_start();
		echo '<ul id="nav-bar-settings" class="main-form-nav settings-nav">';
			do_action( 'chef_forms_form_settings_nav', $this );
		echo '</ul>';
		return ob_get_clean();
	}

	/**
	 * Save all panels
	 * 
	 * @return string
	 */
	public function save( $post_id )
	{
		do_action( 'chef_forms_form_settings_save', $post_id, $this );	
	}


}?>