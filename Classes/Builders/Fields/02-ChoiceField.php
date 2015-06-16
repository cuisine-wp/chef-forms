<?php
namespace ChefForms\Builders\Fields;

use Cuisine\Wrappers\Field;

class ChoiceField extends DefaultField{

	
	/**
	 * Build up the field block
	 * 
	 * @return string ( html, echoed )
	 */
	public function build(){

	    echo '<div class="field-block '.$this->type.'" data-form_id="'.$this->formId.'" data-field_id="'.$this->id.'">';

	        echo '<div class="field-preview">';
	            echo $this->buildPreview();
	            echo '<span class="toggle-field"></span>';
	        echo '</div>';

	        echo '<div class="field-options">';

	            $fields = $this->getFields();

	            foreach( $fields as $field ){

	                $field->render();
	            }

	            //render the javascript-templates seperate, to prevent doubles
	            $rendered = array();
	            foreach( $fields as $field ){

	                if( method_exists( $field, 'renderTemplate' ) && !in_array( $field->name, $rendered ) ){

	                    echo $field->renderTemplate();
	                    $rendered[] = $field->name;

	                }
	            }

	            $this->bottomControls();

	        echo '</div>';          
	        echo '<div class="loader"><span class="spinner"></span></div>';

	    echo '</div>';

	}



	/**
	 * Generate the preview for this field:
	 * 
	 * @return void
	 */
	public function buildPreview(){

	    $html = '';

	    $html .= '<label>'.$this->getLabel().'</label>';
	    $html .= '<span class="field-type">'.$this->type.'</span>';

	    return $html;

	}


	/**
	 * Get the fields for this class
	 * 
	 * @return array
	 */
	private function getFields(){

	    $prefix = 'fields['.$this->id.']';

	    $defaultChoices = array(
	    		'optie-1' => 'Optie 1',
	    		'optie-2' => 'Optie 2',
	    		'optie-3' => 'Optie 3'
	    );

	    return array(

	    	Field::multifield(
	    		$prefix.'[choices]',
	    		'Keuzes',
	    		array(
	    			'options' => $this->getProperty( 'choices', $defaultChoices )
	    		)
	    	),

	        Field::text(

	            $prefix.'[label]',
	            'Label',
	            array(
	                'defaultValue'  => $this->getProperty( 'label', 'Label' )
	            )
	        ),

	        Field::text(


	            $prefix.'[placeholder]',
	            'Placeholder',
	            array(
	                'defaultValue'  => $this->getProperty( 'placeholder' )
	            )
	        ),


	        Field::checkbox(

	            $prefix.'[required]',
	            'Verplicht?',
	            array(
	                'defaultValue'  => $this->getProperty( 'required' )
	            )
	        ),

	        Field::hidden(
	            $prefix.'[type]',
	            array(
	                'defaultValue'  => $this->type
	            )    

	        ),

	        Field::hidden(
	            $prefix.'[position]',
	            array(
	                'class'         => array( 'field-input', 'position-input' ),
	                'defaultValue'  => $this->position
	            )    

	        ),



	    );
	}



	/**
	 * Return a property
	 * 
	 * @param  string $name
	 * @param  string $default
	 * @return mixed (string/bool)
	 */
	public function getProperty( $name, $default = false ){

	    if( isset( $this->properties[ $name ] ) )
	        return $this->properties[ $name ];

	    if( isset( $this->properties[ 'options' ][ $name ] ) )
	    	return $this->properties[ 'options' ][ $name ];

	    return $default;
	}

	/**
	 * Get choices
	 *
	 * @return Array / void
	 */
	public function getChoices(){

	    if( $this->properties['options']['choices'] )
	        return $this->properties['options']['choices'];

	}


	/**
	 * Get the class of sub-inputs like radios and checkboxes
	 * 
	 * @return String;
	 */
	public function getSubClass(){

	    $classes = array(
	                        'subfield',
	                        'type-'.$this->type,
	                        $this->getValue()
	    );

	    $classes = apply_filters( 'cuisine_subfield_classes', $classes );
	    $output = implode( ' ', $classes );

	    return $output;
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

	    //check to see if it's an associative array
	    $isIndexed = ( array_values( $inputs ) === $inputs );

	    foreach( $inputs as $key => $input ){
	    	
	    	if( is_array( $input ) && isset( $input['label'] ) ){
	    		$label = $input['label'];
	    	}else{
	    		$label = $input;
	    	}


	        $choice = array();
	        $choice['id'] = $i;
	        $choice['key'] = ( $isIndexed ? $input : $key );
	        $choice['label'] = $label;
	      
	        $choices[] = $choice;

	        $i++;
	    }

	    return $choices;

	}

}