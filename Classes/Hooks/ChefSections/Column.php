<?php

	namespace ChefForms\Hooks;
	
	use ChefSections\Columns\DefaultColumn;
	use Cuisine\Wrappers\Field;
	use Cuisine\Utilities\Url;
	
	
	class Column extends DefaultColumn{
	
		/**
		 * The type of column
		 * 
		 * @var String
		 */
		public $type = 'form';
	
	
	
		/*=============================================================*/
		/**             Backend                                        */
		/*=============================================================*/
	
		
	
		/**
		 * Create the preview for this column
		 * 
		 * @return string (html,echoed)
		 */
		public function buildPreview(){
	
			echo '<strong>'.esc_html( $this->getField( 'title' ) ).'</strong>';
	
		}
	
	
		/**
		 * Build the contents of the lightbox for this column
		 * 
		 * @return string ( html, echoed )
		 */
		public function buildLightbox(){
	
			//get all fields for this column
			$fields = $this->getFields();
	
			echo '<div class="main-content">';
			
				foreach( $fields as $field ){
	
					$field->render();
	
					//if a field has a JS-template, we need to render it:
					if( method_exists( $field, 'renderTemplate' ) ){
						echo $field->renderTemplate();
					}
	
				}
	
			echo '</div>';
			echo '<div class="side-content">';
				
				//optional: side fields
	
				$this->saveButton();
	
			echo '</div>';
		}
	
	
		/**
		 * Get the fields for this column
		 * 
		 * @return [type] [description]
		 */
		private function getFields(){
	
			$fields = array(

				Field::text( 
					'title', 				//id
					__('Title Label','chefforms'),			//label
					
					array(
						'label' 		=> false,	// Show Label false - top - left
						'placeholder' 	=> 'Titel',
						'defaultValue'	=> $this->getField( 'title' ),
					)
				),

				Field::select(
					'form',
					__( 'Form', 'chefforms' ),
					$this->getForms(),
					array(
						'defaultValue' => $this->getField( 'form' )
					)
				)
				
			);
			
			$fields = apply_filters( 'chef_form_column_fields', $fields, $this );
			
			return $fields;
	
		}

		
		/**
		 * Returns available forms in key / value pairs:
		 * 
		 * @return void
		 */
		private function getForms(){

			$query = get_posts( array( 'post_type' => 'form', 'posts_per_page' => -1 ) );
			$forms = array();
			foreach( $query as $item ){

				$forms[ $item->ID ] = $item->post_title;

			}

			return $forms;
		}
	
	}

	