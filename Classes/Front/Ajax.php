<?php

	namespace ChefForms\Front;

	use \stdClass;
	use \ChefForms\Wrappers\AjaxInstance;
	use \ChefForms\Wrappers\Form;

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

			add_action( 'wp_ajax_nopriv_sendForm', array( &$this, 'sendForm' ) );
			add_action( 'wp_ajax_sendForm', array( &$this, 'sendForm' ) );

		}

		/**
		 * Submit a form with ajax
		 * 
		 * @return string, echoed
		 */
		public function sendForm(){

			$this->setPostGlobal();
	
			$confirm = Form::save( $_POST['post_id'] );

			echo $confirm;
			die();
		}

	}


	\ChefForms\Front\Ajax::getInstance();
