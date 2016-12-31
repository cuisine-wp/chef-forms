<?php

	namespace ChefForms\Front\Form;


	use Cuisine\Utilities\Session;
	use Cuisine\Wrappers\User;

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
		public static function notification( $tag, $fields = array(), $entry = array() ){

			if( self::check( $tag ) ){

				//replace field entry values:
				foreach( $fields as $field ){

					$fieldValue = $field->getValueFromEntry( $entry );

					$tag = str_replace(

						array(
							'{{'.$field->name.'}}',
							'{{ '.$field->name.' }}'
						),
						$fieldValue['value'],
						$tag

					);
				}

				//replace admin_email:
				$admin_email = get_option( 'admin_email' );
				$tag = str_replace( array( '{{ admin_email }}', '{{admin_email}}' ), $admin_email, $tag );

				//replace entry id's:
				if( isset( $_POST['entry_id'] ) )
					$tag = str_replace( array( '{{ entry_id }}', '{{entry_id}}'), $_POST['entry_id'], $tag );


				//replace post, meta and other values:
				$tag = self::field( $tag );
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

			$return = $tag;

			if( self::check( $tag ) ){

				if( strpos( $tag,'{{post_') !== false || strpos( $tag, '{{ post_' ) !== false ){

					$return = self::postData( $tag );

				}

				if( strpos( $tag,'{{postmeta_') !== false || strpos( $tag, '{{ postmeta_' ) !== false ){

					$return = self::postMeta( $tag );

				}

				if( strpos( $tag, '{{user_' ) !== false || strpos( $tag, '{{ user_' ) !== false ){

					$return = self::userData( $tag );
				}

				if( strpos( $tag, '{{usermeta_' ) !== false || strpos( $tag, '{{ usermeta_' ) !== false ){

					$return = self::userMeta( $tag );

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

				//ensure we always take the root post id:
				$p = get_post( Session::rootPostId() );

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

			//get the root post-id if given:
			if( isset( $_POST['_rootPid'] ) ){
				$post_id = $_POST['_rootPid'];
			}else{
				$post_id = Session::postId();
			}

			$metas = get_post_meta( $post_id );

			if( !empty( $metas ) ){
				foreach( $metas as $key => $val ){

					$value = $val[0];
					$key = 'post_meta_'.$key;

					$tag = str_replace( array( '{{ '. $key .' }}', '{{'.$key.'}}' ), $value, $tag );

				}
			}

			return $tag;
		}


		/**
		 * Check for available user-data like names and emails
		 *
		 * @param  string $tag
		 * @return string
		 */
		public static function userData( $tag ){

			if( is_user_logged_in() ){

				$tag = str_replace( array( '{{ user_name }}', '{{user_name}}' ), User::get( 'display-name' ), $tag );
				$tag = str_replace( array( '{{ user_email }}', '{{user_email}}' ), User::get( 'email' ), $tag );
				$tag = str_replace( array( '{{ user_id }}', '{{user_id}}' ), User::get( 'ID' ), $tag );

			}

			return $tag;

		}


		/**
		 * Check for available user-meta
		 *
		 * @param  string $tag
		 * @return string
		 */
		public static function userMeta( $tag ){

			$originalTag = $tag;

			if( User::loggedIn() ){

				$metas = get_user_meta( User::getId() );
				if( !empty( $metas ) ){
					foreach( $metas as $key => $val ){

						$value = ( isset( $val[0] ) ? $val[0] : false );
						$value = maybe_unserialize( $value );
						$key = 'usermeta_'.$key;

						if( $value && !is_array( $value ) )
							$tag = str_replace( array( '{{ '. $key .' }}', '{{'.$key.'}}' ), $value, $tag );

					}
				}


				//meta didn't exist; return an empty string:
				if( $tag === $originalTag )
					$tag = '';

			}else{

				//user isn't logged in, so return an empty string:
				$tag = '';

			}

			return $tag;

		}


	}
