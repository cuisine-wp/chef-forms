<?php

	namespace ChefForms\Admin;

	use \stdClass;
	use \ChefForms\Wrappers\AjaxInstance;

	class Ajax extends AjaxInstance{


		/**
		 * Init admin events & vars
		 */
		function __construct(){

			$this->ajaxListeners();

		}

		

		/**
		 * All ajax events for sections on the backend
		 * 
		 * @return string, echoed
		 */
		private function ajaxListeners(){


			//creating a section:
			add_action( 'wp_ajax_createField', function(){

				$this->setPostGlobal();

				die();

			});

			//delete section:
			add_action( 'wp_ajax_deleteField', function(){

				$this->setPostGlobal();

				FormBuilder::deleteField();
				die();

			});

			//sorting sections:
			add_action( 'wp_ajax_sortFields', function(){

				$this->setPostGlobal();

				echo FormBuilder::sortSections();
				die();

			});

		}

	}


	if( is_admin() )
		\ChefForms\Admin\Ajax::getInstance();
