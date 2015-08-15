<?php

	namespace ChefForms\Front;


	use Cuisine\Utilities\Session;

	class Tag {


		/**
		 * Check a tag, return a replacement value
		 * 
		 * @param  string $tag 
		 * @return string      
		 */
		public static function check( $tag ){

			if( strpos( $tag,'{{') !== false && strpos( $tag, '}}' ) ) {

				if( strpos( $tag,'{{post_') !== false || strpos( $tag, '{{ post_' ) !== false ){

					$return = self::postData( $tag );

				}else{

					$return = self::postMeta( $tag );

				}
			}


			//filter the result:
			return apply_filters( 'chef_form_tag', $return, $tag );
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
			$tag = str_replace( array( '{{ post_date }}', '{{post_date}}' ), $p->post_title, $tag );

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
