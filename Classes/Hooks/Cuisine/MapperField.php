<?php

	//again, change this namespace:
	namespace ChefForms\Hooks;
	
	use Cuisine\Fields\DefaultField;
	use Cuisine\Fields\SelectField;
	use Cuisine\Wrappers\Field;
	use Cuisine\Utilities\Sort;

	class MapperField extends DefaultField{
	
		/**
		 * The type of this field
		 * 
		 * @var String
		 */
		public $type = 'mapper';




		/**
		 * Method to override to define the input type
		 * that handles the value.
		 *
		 * @return void
		 */
		protected function fieldType(){
		    $this->type = 'mapper';
		}



		/**
		 * Build the html
		 *
		 * @return String;
		 */
		public function render(){

			$html = '<div class="map-field">';

				//$html .= $this->buildStart();
				//$html .= $this->buildOperator();

			$html .= $this->getLabel();

			$html .= $this->buildEnd();

			$html .= '</div>';
			
			echo $html;

		    return $html;
		}


		/**
		 * Build the first field (or label)
		 * 
		 * @return String ( html )
		 */
		public function buildStart(){

			$choices = $this->getChoices();
			$html = '<span class="map-start">';

			if( is_array( $choices ) ){

				$start = Field::select(

						$this->name.'[start]',
						'',
						$choices,
						array(
							'label'	=> false
						)
				);

				$html = $start->build();

			}else{

				if( $this->label )
					$html .= '<label>'.esc_html( $this->label ).'</label>';

			}

			$html .= '</span>';

			return $html;
		}




		/**
		 * Build the operator dropdown
		 * 
		 * @return String ( html )
		 */
		public function buildOperator(){

			$choices = array(
					'='		=> 		__( 'is', 'chefforms' ),
					'!='	=>		__( 'is niet', 'chefforms' ),
					'>='	=>		__( 'is groter dan', 'chefforms' ),
					'<='	=>		__( 'is kleiner dan', 'chefforms' )	
			);

			

			if( $choices ){

				$opps = Field::select(

						$this->name.'[operator]',
						'Operator',
						$choices

				);

				$html = '<span class="map-opps">';

					$html .= $opps->build();

				$html .= '</span>';
			}



			return $html;
		}



		/**
		 * Build the field dropdown
		 * 
		 * @return String ( html )
		 */
		public function buildEnd(){

			$fields = $this->getFields();
			$html = '';

			if( $fields ){
				$choices = array_combine(
					Sort::pluck( $fields, 'id' ),
					Sort::pluck( $fields, 'label' )
				);
	
				$choices = array_replace( array( 'none' => 'Geen veld' ), $choices );
	
				$types = $this->getIncluded();
	
				//filter choices
				if( is_array( $types ) ){
	
					$choices = array();
	
					foreach( $fields as $field ){
	
						if( in_array( $field['type'], $types ) ){
							$choices[ $field['id'] ] = $field['label'];
						}
					}
	
				}
	
			
	
				$html = '<span class="map-end">';
	
				if( $choices ){
	
	
					$html .= Field::select(
	
							$this->name,
							'Veld',
							$choices,
							array(
								'defaultValue'	=> $this->getDefault()
							)
	
					)->build();
	
				}
	
				$html .= '</span>';

			}

			return $html;
		}




		/**
		 * Get the choices for this field
		 * 
		 * @return mixed array/bool
		 */
		private function getChoices(){

			if( isset( $this->properties['choices'] ) )
				return $this->properties['choices'];

			return false;

		}


		/**
		 * Checks to see if we need operators
		 * 
		 * @return void
		 */
		private function useOperators(){

			if( $this->properties['operators'] )
				return $this->properties['operators'];

			return true;

		}


		/**
		 * Get the included_types for this field
		 * 
		 * @return mixed array/bool
		 */
		private function getIncluded(){

			if( isset( $this->properties['included_types'] ) )
				return $this->properties['included_types'];

			return 'all';

		}


		/**
		 * Gets the fields set for this form
		 * 
		 * @return mixed array/bool
		 */
		private function getFields(){

			$id = $this->getFormId();

			if( $id ){

				$fields = get_post_meta( $id, 'fields', true );

				if( $fields ){

					$array = array();

					foreach( $fields as $id => $field ){

						$array[] = array(
								'id'	=> $id,
								'type'	=> $field['type'],
								'label'	=> $field['label']
						);
					}

					return $array;
				}
			}

			return false;
		}


		/**
		 * Gets the form ID for this mapper
		 * 
		 * @return mixed array/bool
		 */
		private function getFormId(){

			if( $this->properties['form_id'] )
				return $this->properties['form_id'];

			return false;
		}

	}
