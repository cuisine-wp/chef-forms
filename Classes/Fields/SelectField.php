<?php
namespace ChefForms\Fields;


class SelectField extends ChoiceField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'select';
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

    	$html .= '<select class="preview-select preview-input" disabled">';
    	foreach( $choices as $choice ){
    		$html .= '<option>'.esc_html( $choice['label'] ).'</option>';
    	}
    	$html .= '</select>';
           
    	//do not display these in the lightbox:
    	if( $mainOverview ){

    		$html .= $this->getFieldIcon();
    		$html .= $this->previewControls();

    	}

    	echo $html;

    }
}