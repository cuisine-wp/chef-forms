<?php

	//again, change this namespace:
	namespace ChefForms\Hooks;
	
	use Cuisine\Fields\ChoiceField;
	
	class MultiField extends ChoiceField{
	
		/**
		 * The type of this field
		 * 
		 * @var String
		 */
		public $type = 'multifield';
	

		/**
		 * Method to override to define the input type
		 * that handles the value.
		 *
		 * @return void
		 */
		protected function fieldType(){
		    $this->type = 'multifield';
		}



		/**
		 * Build the html
		 *
		 * @return String;
		 */
		public function build(){
	
		    $html = '';
		    $choices = $this->getChoices();
		    $choices = $this->parseChoices( $choices );
			

			$class = ( $this->hasDifferentKeys() ? 'key active' : 'key' );

		    
		    $html .= '<div class="multifield-builder" data-prefix="'.$this->name.'[%id]">';

		    $html .= $this->topControls();

		    $html .= '<div class="multifield-field-wrapper">';

		    
		    $html .= '<table>';

		    	$html .= '<thead><tr>';

	    			$html .= '<th class="'.$class.'">Waarde</th>';
	    			$html .= '<th>Label</th>';
	    			$html .= '<th></th>';

		    	$html .= '</tr></thead>';
		    	$html .= '<tbody>';

		    		foreach( $choices as $choice ){

		    			$html .= $this->makeItem( $choice );

		    		}

		    	$html .= '</tbody>';
		    $html .= '</table></div></div>';

		    return $html;
		}


		/**
		 * Create a single option-row
		 * 
		 * @param  array $choice
		 * @return void
		 */
		private function makeItem( $choice ){

			$html = '';
			$class = ( $this->hasDifferentKeys() ? 'key active' : 'key' );
			$prefix = $this->name.'['.$choice['id'].']';
			
			$html .= '<tr class="multi-row"><td class="'.$class.'">';

				$html .= '<input type="text" data-name="key" name="'.$prefix.'[key]" value="'.$choice['key'].'"/>';

			$html .= '</td><td class="value">';

			$html .= '<input type="text" data-name="label" name="'.$prefix.'[label]" value="'.$choice['label'].'"/>';


			$html .= '</td><td class="actions">';

				//add row
				$html .= '<span class="add-row">';
					$html .= '<span class="dashicons dashicons-plus"></span>';
					$html .= '<span class="tooltip">'.__('Nieuwe rij', 'chefforms' ).'</span>';
				$html .= '</span>';

				//remove row
				$html .= '<span class="remove-row">';
					$html .= '<span class="dashicons dashicons-minus"></span>';
					$html .= '<span class="tooltip">'.__( 'Rij weghalen', 'chefforms' ).'</span>';
				$html .= '</span>';

				//displace row
				$html .= '<span class="drag-row">';
					$html .= '<span class="dashicons dashicons-sort"></span>';
					$html .= '<span class="tooltip">'.__( 'Sorteren', 'chefforms' ).'</span>';
				$html .= '</span>';


			$html .= '</td></tr>';

			return $html;
		}



		/**
		 * Return the template, for Javascript
		 * 
		 * @return String
		 */
		public function renderTemplate(){

		    //make a clonable item, for javascript:
		    $html = '<script type="text/template" id="multifield_row">';
		        $html .= $this->makeItem( array( 
		            'id' => '0',
		            'key' => 'waarde', 
		            'label' => 'Label'
		        ) );
		    $html .= '</script>';

		    return $html;
		}


		/**
		 * Handles the top controls of this field
		 * 
		 * @return string (html)
		 */
		private function topControls(){

			$btnText = __( 'Waardes verschillen van label', 'chefforms' );
			$class = 'toggle-keys';

			if( $this->hasDifferentKeys() ){
				$btnText = __( 'Waardes verschillen niet van label', 'chefforms' );
				$class .= ' showKeys';
			}

			$html = '<div class="multifield-top-controls">';
				$html .= '<a class="'.$class.'">'.$btnText.'</a>';
			$html .= '</div>';

			return $html;
		}



		/**
		 * Checks if this fields keys are different from it's values
		 * 
		 * @return string
		 */
		private function hasDifferentKeys(){

			$choices = $this->getChoices();
			$choices = $this->parseChoices( $choices );
			$diff = false;
			
			foreach( $choices as $key => $value ){
				
				if( $key !== $value ){
					$diff = true;
					break;
				}
			}

			return $diff;
		}



		/**
		 * Makes the choices array complete
		 * 
		 * @param  Array $inputs  all default choices
		 * @return Array
		 */
		public function parseChoices( $inputs ){

		    $i = 0;
		    $choices = array();

		    foreach( $inputs as $key => $input ){
		    	
		    	$label = $input;
		    	$key = $key;

		    	if( is_array( $input ) ){

		    		if( isset( $input['label'] ) )
		    			$label = $input['label'];
		    	
		    		if( isset( $input['key'] ) )
		    			$key = $input['key'];
		    	}

		        $choice = array();
		        $choice['id'] = $i;
		        $choice['key'] = $key;
		        $choice['label'] = $label;
		      
		        $choices[] = $choice;

		        $i++;
		    }

		    return $choices;

		}


	}
