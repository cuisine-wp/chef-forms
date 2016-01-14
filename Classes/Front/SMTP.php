<?php

	namespace ChefForms\Front;

	use ChefForms\Wrappers\StaticInstance;
	use Cuisine\Utilities\Url;


	class SMTP extends StaticInstance{


		var $resolution;

		var $settings;



		/**
		 * Init mail events & vars
		 */
		function __construct(){

			$this->setSettings();

			if( apply_filters( 'chef_forms_use_mandrill', $this->settings['use_mandrill'] ) == 'true' )
				$this->listen();

		}

		
		/**
		 * Listen to the required mailevents
		 * 
		 * @return void
		 */
		function listen(){


			add_action( 'phpmailer_init', function( $phpmailer ){

				//settings:
				$settings = array(
					'ssl'		=> 	true,
					'auth'		=>	true,
					'host'		=> 	$this->settings['host'],
					'port'		=> 	'587',
					'user'		=>	$this->settings['user'],
					'password'	=>	$this->settings['password']
				);


				$settings = apply_filters( 'chef_forms_smtp_settings', $settings );

				$phpmailer->IsSMTP();

				// Set the SMTPSecure value, if set to none, leave this blank
				if( $settings['ssl'] )
					$phpmailer->SMTPSecure = 'tls';
			
				// Set the other options
				$phpmailer->Host = $settings['host'];
				$phpmailer->Port = $settings['port'];
					
				// If we're using smtp auth, set the username & password
				if ( $settings['auth'] ) {

					$phpmailer->SMTPAuth = true;
					$phpmailer->Username = $settings['user'];
					$phpmailer->Password = $settings['password'];
				
				}
				
				// You can add your own options here, 
				// see the phpmailer documentation for more info:
				// http://phpmailer.sourceforge.net/docs/
				$phpmailer = apply_filters('chef_forms_smtp_custom_options', $phpmailer);
				
			});

		}


		public function setSettings(){

			$settings = get_option( 'form-settings', $this->getDefaultSettings() );
			$this->settings = $settings;

		}



		/**
		 * Get all default settings in an array
		 * 
		 * @return array
		 */
		private function getDefaultSettings(){

			return array(
						
				'use_mandrill'	=> 'true',
				'host'		=> 'smtp.mandrillapp.com',
				'user'		=> 'luc.princen@gmail.com',
				'password'	=> '_gEwO60stNDpGZFyrYaadQ'
			
			);

		}



	}
/*
	\ChefForms\Front\SMTP::getInstance();


	// To avoid any (very unlikely) clashes, check if the function alredy exists
	if ( !function_exists('phpmailer_init_smtp' ) ) {
	// This code is copied, from wp-includes/pluggable.php as at version 2.2.2
	function phpmailer_init_smtp($phpmailer) {
		
		// If constants are defined, apply those options
		if (defined('WPMS_ON') && WPMS_ON) {
			
			$phpmailer->Mailer = WPMS_MAILER;
			
			if (WPMS_SET_RETURN_PATH)
				$phpmailer->Sender = $phpmailer->From;
			
			if (WPMS_MAILER == 'smtp') {
				$phpmailer->SMTPSecure = WPMS_SSL;
				$phpmailer->Host = WPMS_SMTP_HOST;
				$phpmailer->Port = WPMS_SMTP_PORT;
				if (WPMS_SMTP_AUTH) {
					$phpmailer->SMTPAuth = true;
					$phpmailer->Username = WPMS_SMTP_USER;
					$phpmailer->Password = WPMS_SMTP_PASS;
				}
			}
			
			// If you're using contstants, set any custom options here
			$phpmailer = apply_filters('wp_mail_smtp_custom_options', $phpmailer);
			
		}
		else {
			
			// Check that mailer is not blank, and if mailer=smtp, host is not blank
			if ( ! get_option('mailer') || ( get_option('mailer') == 'smtp' && ! get_option('smtp_host') ) ) {
				return;
			}
			
			// Set the mailer type as per config above, this overrides the already called isMail method
			$phpmailer->Mailer = get_option('mailer');
			
			// Set the Sender (return-path) if required
			if (get_option('mail_set_return_path'))
				$phpmailer->Sender = $phpmailer->From;
			
			// Set the SMTPSecure value, if set to none, leave this blank
			$phpmailer->SMTPSecure = get_option('smtp_ssl') == 'none' ? '' : get_option('smtp_ssl');
			
			// If we're sending via SMTP, set the host
			if (get_option('mailer') == "smtp") {
				
				// Set the SMTPSecure value, if set to none, leave this blank
				$phpmailer->SMTPSecure = get_option('smtp_ssl') == 'none' ? '' : get_option('smtp_ssl');
				
				// Set the other options
				$phpmailer->Host = get_option('smtp_host');
				$phpmailer->Port = get_option('smtp_port');
				
				// If we're using smtp auth, set the username & password
				if (get_option('smtp_auth') == "true") {
					$phpmailer->SMTPAuth = TRUE;
					$phpmailer->Username = get_option('smtp_user');
					$phpmailer->Password = get_option('smtp_pass');
				}
			}
			
			// You can add your own options here, see the phpmailer documentation for more info:
			// http://phpmailer.sourceforge.net/docs/
			$phpmailer = apply_filters('wp_mail_smtp_custom_options', $phpmailer);
			
			
			// STOP adding options here.
			
		}
		
		} // End of phpmailer_init_smtp() function definition
	}*/
