<?php

	namespace ChefForms\Front;


	use Cuisine\Utilities\Session;

	class Tag {



		/**
		 * Check if a tag exists
		 * 
		 * @param  string $tag
		 * @return string     
		 */
		public static function check( $tag ){

			if( strpos( $tag,'{{') >= 0 && strpos( $tag, '}}' ) > 0 ){
				return true;
			}

			return false;
		}



		/***********************************************/
		/**********  NOTIFICATION VALUES ***************/
		/***********************************************/

		/**
		 * Replace certain tags in notifications
		 * 
		 * @param  string $tag
		 * @return string     
		 */
		public static function notification( $tag, $fields = array() ){

			if( self::check( $tag ) ){

				foreach( $fields as $entry ){

					$name = $entry['name'];

					$tag = str_replace( 
			
						array( 
							'{{'.$name.'}}',
							'{{ '.$name.' }}'
						),
		
						$entry['value'],
		
					$tag );
				}


				//replace admin_email:
				$admin_email = get_option( 'admin_email' );
				$tag = str_replace( array( '{{ admin_email }}', '{{admin_email}}' ), $admin_email, $tag );



				//replace entry id's:
				if( isset( $_POST['entry_id'] ) )
					$tag = str_replace( array( '{{ entry_id }}', '{{entry_id}}'), $_POST['entry_id'], $tag );
			

				//replace post meta:
				$tag = self::postMeta( $tag );
			}

			return $tag;
		}




		/***********************************************/
		/**********  FIELD VALUES **********************/
		/***********************************************/



		/**
		 * Check a tag, return a replacement value
		 * 
		 * @param  string $tag 
		 * @return string      
		 */
		public static function field( $tag ){

			if( self::check( $tag ) ){

				if( strpos( $tag,'{{post_') !== false || strpos( $tag, '{{ post_' ) !== false ){

					$return = self::postData( $tag );

				}else{

					$return = self::postMeta( $tag );

				}

				//filter the result:
				return apply_filters( 'chef_form_tag', $return, $tag );

			}

			return $tag;
		}


		/**
		 * Check for available post-data like titles and dates
		 * 
		 * @param  string $tag
		 * @return string
		 */
		public static function postData( $tag ){

			global $post;

			if( !isset( $post ) || !isset( $post->ID ) ){

				$p = get_post( Session::postId() );

			}else{

				$p = $post;

			}


			$tag = str_replace( array( '{{ post_title }}', '{{post_title}}' ), $p->post_title, $tag );
			$tag = str_replace( array( '{{ post_date }}', '{{post_date}}' ), $p->post_date, $tag );
			$tag = str_replace( array( '{{ post_id }}', '{{post_id}}' ), $p->ID, $tag );

			return $tag;

		}


		/**
		 * Check for post meta
		 * 
		 * @param  string $tag
		 * @return string
		 */
		public static function postMeta( $tag ){

			$post_id = Session::postId();
			$metas = get_post_meta( $post_id );

			foreach( $metas as $key => $val ){

				$value = $val[0];
				$tag = str_replace( array( '{{ '. $key .' }}', '{{'.$key.'}}' ), $value, $tag );


			}

			return $tag;
		}


	}

	\ChefForms\Front\SMTP::getInstance();
