<?php

	namespace ChefForms\Front;

	use ChefForms\Wrappers\StaticInstance;
	use Cuisine\Utilities\Url;


	class SMTP extends StaticInstance{

		/**
		 * Init mail events & vars
		 */
		function __construct(){

			if( apply_filters( 'chef_forms_use_mandrill', true ) )
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
					'host'		=> 	'smtp.mandrillapp.com',
					'port'		=> 	'465',
					'user'		=>	'luc.princen@gmail.com',
					'password'	=>	'_gEwO60stNDpGZFyrYaadQ'
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



	}

	\ChefForms\Front\SMTP::getInstance();
