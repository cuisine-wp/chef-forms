<?php

namespace ChefForms\Creators;

use Cuisine\Utilities\Session;
use WP_Query;


class EntriesManager{

	/**
	 * Entries array 
	 * 
	 * @var array
	 */
	public $entries = array();


	/**
	 * Amount of pages available for pagination
	 * 
	 * @var integer
	 */
	public $pageCount = 1;

	/**
	 * Total number of entries, for pagination 
	 * 
	 * @var integer
	 */
	public $totalEntries = 0;


	/**
	 * Default number of entries to query
	 * 
	 * @var integer
	 */
	public $entriesPerPage = 10;


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

		if( isset( $post ) ){
			$this->postId = $post->ID;
		}else{
			$this->postId = Session::postId();
		}

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

		//show the entries list:
		do_action( 'chef_forms_before_entry_list', $this->entries, $this->postId  );


		if( $this->entries ){

			foreach( $this->entries as $entry ){

				echo '<div class="single-entry">';

					echo '<div class="entry-date">';

						echo $entry['date'];

					echo '</div>';


					do_action( 'chef_forms_before_entry_fields', $entry, $this->postId );

					echo '<div class="entry-fields">';

						$fields = apply_filters( 'chef_forms_entry_fields', $entry['fields'] );

						foreach( $fields as $field ){

							echo '<div class="field-wrapper">';

								echo '<div class="field-label">';
									echo $field['label'].': ';
								echo '</div>';
	
								echo '<div class="field-val">';
	
									if( $field['value'] )
										echo $field['value'];
								
								echo '</div>';

							echo '</div>';


						}


					echo '</div>';

					do_action( 'chef_forms_after_entry_fields', $entry, $this->postId );


				echo '</div>';
			}

			$this->buildPagination();

		}else{

			echo  '<p>'.__( 'Nog geen inzendingen', 'chefforms' ).'</p>';
		}


		do_action( 'chef_forms_after_entry_list', $this->entries, $this->postId  );
	}

	/**
	 * Build the pagination for these entries
	 * 
	 * @return string (html,echoed)
	 */
	private function buildPagination(){

		$url = admin_url( 'post.php?post='.$_GET['post'].'&action=edit&entry_page=' );
		$current = ( isset( $_GET['entry_page' ] ) ? $_GET['entry_page']  : 1 );

		if( $this->pageCount > 1 ){

			echo '<div class="entry-pagination">';

				for( $i = 0; $i < $this->pageCount; $i++ ){

					$pageNum = $i + 1;
					$class = 'entry-page-block';

					if( $pageNum == $current )
						$class .= ' current';

					echo '<a href="'.$url.$pageNum.'" class="'.$class.'">';
						echo $pageNum;
					echo '</a>';

				}

			echo '</div>';
		}

	}


	/*=============================================================*/
	/**             Getters & Setters                              */
	/*=============================================================*/


	/**
	 * Get the entries
	 * 
	 * @return array
	 */
	public function getEntries(){

		$args = array(

			'post_parent'	=> $this->postId,
			'post_type'		=> 'form-entry',
			'paged'			=> ( isset( $_GET['entry_page'] ) ? $_GET['entry_page'] : 0 ),
			'posts_per_page'=> $this->entriesPerPage

		);

		$args = apply_filters( 'chef_forms_entries_query', $args );
		$entryPosts = new WP_Query( $args );
		$this->totalEntries = $entryPosts->found_posts;
		$this->pageCount = $entryPosts->max_num_pages;

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