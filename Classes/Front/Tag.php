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


			



				if( substr( $tag, 0, 5 ) == 'post_' ){

					$return = self::postData( $tag );

				}else{

					$return = self::postMeta( $tag );

				}

			//filter the result:
			return apply_filters( 'chef_form_tag', $return, $tag );

		}


		public static function postData( $type ){

			global $post;

			if( !isset( $post ) || !isset( $post->ID ) ){

				$p = get_post( Session::postId() );

			}else{

				$p = $post;

			}


			switch( $type ){

				case 'post_title':

					return $p->post_title;

				break;

				case 'post_date':

					return $p->post_date;

				break;
			}


		}


		public static function postMeta( $name ){


		}

	}

	\ChefForms\Front\SMTP::getInstance();
