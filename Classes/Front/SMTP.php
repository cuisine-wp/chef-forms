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
					'port'		=> 	'465',
					'user'		=>	$this->settings['user'],
					'password'	=>	$this->settings['password']
				);


				$settings = apply_filters( 'chef_forms_smtp_settings', $settings );

				// Set the SMTPSecure value, if set to none, leave this blank
				$phpmailer->SMTPSecure = $settings['ssl'];
			
				// Set the other options
				$phpmailer->Host = $settings['host'];
				$phpmailer->Port = $settings['port'];
					
				// If we're using smtp auth, set the username & password
				if ( $settings['auth'] ) {

					$phpmailer->SMTPAuth = TRUE;
					$phpmailer->Username = get_option('smtp_user');
					$phpmailer->Password = get_option('smtp_pass');
				
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

	\ChefForms\Front\SMTP::getInstance();
