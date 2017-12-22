<?php
namespace CuisineForms\Fields;

use Cuisine\Wrappers\Field;
use Cuisine\Utilities\Sort;
use CuisineForms\Front\Form\Tag;

abstract class ChoiceField extends DefaultField{


    /*=============================================================*/
    /**             FRONTEND                                       */
    /*=============================================================*/


    /**
     * Render this field on the front-end
     * @return [type] [description]
     */
    public function render(){


        $this->sanitizeProperties();
        $type = $this->type;

        Field::$type(

            $this->id,
            $this->getLabel(),
            $this->getChoices(),
            $this->properties

        )->render();

    }


    /**
     * Check the default value, before rendering
     *
     */
    public function sanitizeProperties(){

    	$this->getDefaultValue();

        $this->properties['defaultValue'] = apply_filters( 'cuisine_forms_field_default_value', $this->properties['defaultValue'], $this );

        if( isset( $this->properties['defaultValue'] ) && !is_array( $this->properties['defaultValue'] ) )
            $this->properties['defaultValue'] = Tag::field( $this->properties['defaultValue'] );

        if( !empty( $default ) )
        	$this->properties['defaultValue'] = $default;

        if( empty( $this->properties['validation'][0] ) )
            unset( $this->properties['validation'] );

        if( empty( $this->properties['classes'] ) )
            unset( $this->properties['classes'] );


        if( isset( $this->properties['required'] ) && $this->properties['required'] !== 'true' )
            $this->properties['required'] = false;

    }

    /*=============================================================*/
    /**             BACKEND                                        */
    /*=============================================================*/


	/**
	 * Build up the field block
	 *
	 * @return string ( html, echoed )
	 */
	public function build(){

	    echo '<div class="field-block '.esc_attr( $this->type ).'" data-form_id="'.esc_attr( $this->formId ).'" data-field_id="'.esc_attr( $this->id ).'">';

	        echo '<div class="field-preview">';
	            echo $this->buildPreview( true );
	        echo '</div>';

	        $this->buildLightbox();

	    echo '</div>';

	}


	/**
	 * Build the field's lightbox:
	 *
	 * @return string (html)
	 */
	public function buildLightbox(){
	    echo '<div class="field-options">';

	        echo '<div class="field-live-preview">';

	            echo $this->buildPreview();

	            echo '<span class="close">&times;</span>';

	            echo $this->buildTabs();

	        echo '</div>';

	        echo '<div class="field-setting-tabs">';

	            echo '<div class="field-settings-basics field-setting-tab-content active" id="tab-basics">';

	                $this->buildDefaultSettingsTab();

	            echo '</div>';

	            do_action( 'cuisine_forms_field_tab_content', $this );

	        echo '</div>';
	        $this->bottomControls();

	    echo '</div>';
	}


	/**
	 * The first tab in the lightbox
	 *
	 * @return string ( html, echoed )
	 */
	public function buildDefaultSettingsTab(){

	    echo '<h2>'.__( 'Default Options', 'CuisineForms' ).'</h2>';


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
	}


	/**
	 * Generate the preview for this field:
	 *
	 * @return void
	 */
	public function buildPreview( $mainOverview = false ){

		$html = '';

		$html .= '<label class="preview-label">'.$this->getLabel().'</label>';

		$choices = $this->getProperty( 'choices', false );
		if( !$choices ){
			$choices = array(
				array( 'label' => 'Optie 1' ),
				array( 'label' => 'Optie 2' ),
				array( 'label' => 'Optie 3' )
	    	);
	    }

		$amountChoices = 0;

		foreach( $choices as $choice ){
			if( $amountChoices <= 2 ){

				$type = $this->type;
				if( $this->type == 'checkboxes' )
					$type = 'checkbox';

				$html .= '<span class="choice-wrapper">';
					$html .= '<input class="preview-input preview-'.esc_attr( $type ).'" disabled type="'.esc_attr( $type ).'" ';

					if( $this->isDefaultSelected( $choice ) )
						$html .= 'checked';

					$html .= '>';

					$html .= '<span class="choice-label">'.esc_html( $choice['label'] ).'</span>';
				$html .= '</span>';
			}

			$amountChoices++;
		}

		//do not display these in the lightbox:
		if( $mainOverview ){

			$html .= $this->getFieldIcon();
			$html .= $this->previewControls();

		}

		echo $html;

	}


	/**
	 * Get the fields for this class
	 *
	 * @return array
	 */
	protected function getFields(){

	    $prefix = 'fields['.$this->id.']';

	    $defaultChoices = array(
	    	'optie-1' => 'Optie 1',
	    	'optie-2' => 'Optie 2',
	    	'optie-3' => 'Optie 3'
	    );

	    //cuisine_dump( $this->getProperty( 'choices' ) );

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
	            	'class'			=> array( 'update', 'update-label', 'label-field' ),
	                'defaultValue'  => $this->getProperty( 'label', 'Label' )
	            )
	        ),

	        Field::checkbox(
	            $prefix.'[required]',
	            'Verplicht?',
	            array(
	            	'class'			=> array( 'update', 'update-label', 'req-field' ),
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
                $prefix.'[deletable]',
                array(
                    'defaultValue' => ( $this->deletable ? 'true' : 'false' )
                )
            ),



            Field::hidden(
			    $prefix.'[placeholder]',
			    array(
			        'defaultValue'  => $this->getProperty( 'placeholder' )
			    )
			),

			Field::hidden(
			    $prefix.'[defaultValue]',
			    array(
			        'defaultValue'  => $this->getProperty( 'defaultValue' )
			    )
			),


	        Field::hidden(
                $prefix.'[position]',
                array(
                    'class'         => array( 'field-input', 'position-input' ),
                    'defaultValue'  => $this->position
                )
            ),

            Field::hidden(
                $prefix.'[row]',
                array(
                    'class' => array( 'field-input', 'row-input' ),
                    'defaultValue' => $this->row
                )
            )
	    );
	}


	/**
	 * Check if this choice is selected on default
	 *
	 * @param  array  $choice
	 * @return boolean
	 */
	public function isDefaultSelected( $choice ){

		if( isset( $choice['isDefault'] ) ){

			if( $choice['isDefault'] == 'on' || $choice['isDefault'] == true )
				return true;

		}

		return false;
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
	 * Get choices from the properties and convert
	 * them into key - value pairs
	 *
	 * @return Array / void
	 */
	public function getChoices(){

	    if( $this->properties['choices'] ){

	    	$_choices = $this->properties['choices'];
	    	$choices = array_combine(
	    					Sort::pluck( $_choices, 'key' ),
	    					Sort::pluck( $_choices, 'label' )
	    	);

	    	return $choices;
	    }
	}

	/**
	 * Returns an array of default choices
	 *
	 * @return array
	 */
	public function getDefaultValue(){

		$default = array();

		$choices = $this->getProperty( 'choices');
		foreach( $choices as $choice ){
			if( $this->isDefaultSelected( $choice ) )
				$default[] = $choice['key'];


		}

		if( !empty( $default ) ){
			$this->properties['defaultValue'] = $default;
			return $default;
		}

		return false;
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

}