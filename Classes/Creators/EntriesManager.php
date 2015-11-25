<?php

namespace ChefForms\Creators;

use WP_Query;


class EntriesManager{

	/**
	 * Entries array 
	 * 
	 * @var array
	 */
	public $entries = array();


	/**
	 * Post ID
	 *
	 * @var int
	 */
	private $postId = null;




	/**
	 * Call the methods on construct
	 *
	 * @return \ChefFields\Builder\FormBuilde
	 */
	function __construct(){

		$this->init();

		return $this;
	}


	/**
	 * Initiate this class
	 * 
	 * @param  int $post_id
	 * @return void
	 */
	public function init(){
		
		global $post;

		if( isset( $post ) )
			$this->postId = $post->ID;
		
		$this->entries = $this->getEntries();

		return $this;
	}


	/*=============================================================*/
	/**             Metabox functions                              */
	/*=============================================================*/

	/**
	 * Get all fields
	 *
	 * @return void (echoes html)
	 */
	public function build(){

		$html = '';
	
		if( $this->entries ){

			foreach( $this->entries as $entry ){

				$html .= '<div class="single-entry">';

					$html .= '<div class="entry-date">';

						$html .= $entry['date'];

					$html .= '</div>';


					do_action( 'chef_forms_before_entry_fields', $entry );

					$html .= '<div class="entry-fields">';

						$fields = apply_filters( 'chef_forms_entry_fields', $entry['fields'] );

						foreach( $fields as $field ){

							$html .= '<div class="field-wrapper">';

								$html .= '<div class="field-label">';
									$html .= $field['label'].': ';
								$html .= '</div>';
	
								$html .= '<div class="field-val">';
	
									if( $field['value'] )
										$html .= $field['value'];
								
								$html .= '</div>';

							$html .= '</div>';


						}


					$html .= '</div>';

					do_action( 'chef_forms_after_entry_fields', $entry );


				$html .= '</div>';
			}

		}else{

			$html = '<p>'.__( 'Nog geen inzendingen', 'chefforms' ).'</p>';
		}

		echo $html;

	}



	/*=============================================================*/
	/**             Getters & Setters                              */
	/*=============================================================*/


	/**
	 * Get the entries
	 * 
	 * @return array
	 */
	private function getEntries(){

		$args = array(

			'post_parent'	=> $this->postId,
			'post_type'		=> 'form-entry'

		);

		$entryPosts = new WP_Query( $args );
		$entries = $this->sanitizeEntries( $entryPosts );


		return $entries;

	}


	/**
	 * Mix the entries query with the field data
	 * 
	 * @param  WP_Query $query
	 * @return array
	 */
	private function sanitizeEntries( $query ){

		$fields = get_post_meta( $this->postId, 'fields', true );
		$prefix = 'field_'.$this->postId.'_';

		$entries = false;

		if( $query->have_posts() ){

			$i = 0;

			while( $query->have_posts() ){

				$query->the_post();
				$entryFields = $fields;
				$entryValues = get_post_meta( get_the_ID(), 'entry', true );

				//merge the value with the fields object:
				foreach( $entryFields as $field_id => $field ){

					foreach( $entryValues as $value ){

						$value_id = str_replace( $prefix, '', $value['name'] );
						if( $value_id == $field_id ){
							$entryFields[ $field_id ]['value'] = $value['value'];
							break;
						}

					}

					if( !isset( $entryFields[ $field_id ]['value'] ) )
						$entryFields[ $field_id ]['value'] = false;

				}

				//package the entry
				$entry = array(

					'entry_id'	=> get_the_ID(),
					'date'		=> 	get_the_date(),
					'timestamp'	=>	strtotime( get_the_date() ),
					'fields'	=>  $entryFields
				
				);

				//add it to the array
				$entries[ $i ] = $entry;
				$i++;
			}
		}

		wp_reset_query();
		wp_reset_postdata();
		
		//ugh, wordpress:
		if( isset( $_GET['post'] ) ){
			$GLOBALS['post'] = get_post( $_GET['post'] );

		}

		return $entries;
	}



}?>