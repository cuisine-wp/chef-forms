<?php

namespace ChefForms\Admin\Form\Entries;

use Cuisine\Utilities\Session;
use Cuisine\Utilities\Sort;
use Cuisine\Wrappers\Field;
use WP_Query;


class Manager{

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

		echo '<div class="wrap">';

			echo '<h2>'.__( 'Form Entries', 'chefforms' ).'</h2>';

			echo '<div class="entries-wrap">';

				$this->buildControls();

				if( $this->entries ){


					echo '<div class="entries-list">';
	
					foreach( $this->entries as $entry ){
	
						$entry->build();

					}
	
					$this->buildPagination();

					echo '</div>';
	
				}else{
	
					echo  '<p>'.__( 'No entries yet', 'chefforms' ).'</p>';
				}


			echo '</div>';

		echo '</div>';


		do_action( 'chef_forms_after_entry_list', $this->entries, $this->postId  );
	}

	/**
	 * Create the controls to filter entries
	 * 
	 * @return string ( html,echoed )
	 */
	private function buildControls(){

		$forms = $this->getForms();
		$parent = ( isset( $_GET['parent'] ) ? $_GET['parent'] : 'all' );
		$url = admin_url( 'edit.php' );

		echo '<form class="entry-filter" action="'.$url.'" method="get">';

			Field::select(
				'parent',
				__( 'Entries from form', 'chefforms' ),
				$forms,
				array(
					'defaultValue' => $parent
				)

			)->render();

			echo '<input type="hidden" name="post_type" value="form"/>';
			echo '<input type="hidden" name="page" value="form-entries"/>';

			echo '<button>'.__( 'Filter', 'chefforms' ).'</button>';

		echo '</form>';

	}


	/**
	 * Build the pagination for these entries
	 * 
	 * @return string (html,echoed)
	 */
	private function buildPagination(){

		//build the pagination url:
		$url = admin_url( 'edit.php?post_type=form&page=form-entries' );

		if( isset( $_GET['parent'] ) )
			$url .= '&parent='.$_GET['parent'];

		$url .= '&entry_page=';

		$current = ( isset( $_GET['entry_page' ] ) ? $_GET['entry_page']  : 1 );

		//show pagination only if the count is higher than 1
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

			'post_type'		=> 'form-entry',
			'paged'			=> ( isset( $_GET['entry_page'] ) ? $_GET['entry_page'] : 0 ),
			'posts_per_page'=> $this->entriesPerPage

		);

		if( isset( $_GET['parent'] ) )
			$args['post_parent'] = $_GET['parent'];

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

		$entries = false;

		if( $query->have_posts() ){

			$i = 0;

			while( $query->have_posts() ){

				$query->the_post();

				global $post;
				$args = array(
					'entry_id' 	=> get_The_ID(),
					'form_id'	=> $post->post_parent,
					'date'		=> strtotime( get_the_date() )
				);

				$entry = new Entry();
				$entries[ $i ] = $entry->make( $args );


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

	/**
	 * Get all forms in an ID - Title array
	 * 
	 * @return array
	 */
	private function getForms(){
		$forms = get_posts( array( 
			'post_type' => 'form', 
			'posts_per_page' => -1
		) );

		$forms = array_combine( 
			Sort::pluck( $forms, 'ID' ),
			Sort::pluck( $forms, 'post_title' )
		);

		$forms = array_replace( 
			array( 'all' => __( 'All forms', 'chefforms' ) ),
			$forms
		);

		return $forms;
	}



}?>