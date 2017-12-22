<?php

	namespace CuisineForms\Admin;

	use \stdClass;
	use \CuisineForms\Wrappers\AjaxInstance;
	use \CuisineForms\Wrappers\FormBuilderManager;

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

				echo FormBuilderManager::createField();
				die();

			});

			//delete section:
			add_action( 'wp_ajax_deleteField', function(){

				$this->setPostGlobal();

				FormBuilderManager::deleteField();
				die();

			});

		}
	}