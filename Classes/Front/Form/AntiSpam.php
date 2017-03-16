<?php
    
	namespace ChefForms\Front\Form;


	class AntiSpam{

		/**
		 * Render a honeypot on the client-side to prevent spam
		 * 
		 * @return string (html, echoed)
		 */
		public static function honeypot(){

			$html = '<span style="display:none">';
				$html .= '<input type="text" name="url"/>';
			$html .= '</span>';

			echo $html;
		}

		/**
		 * Check if the honeypot was used
		 * 
		 * @return bool
		 */
		public static function isClean( $form ){

			//this is spam
			if( isset( $_POST['url'] ) && $_POST['url'] !== '' )
				return false;

			return true;

		}


		/**
		 * Sanitize the eventual entry
		 * 
		 * @param  array $_entry
		 * @return array
		 */
		public static function sanitizeEntry( $_entry ){

			unset( $_entry['url'] );
			return $_entry;
		}


	}