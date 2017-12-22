<?php
namespace CuisineForms\Fields;

class CheckboxField extends DefaultField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'checkbox';
    }

    /**
     * Generate the preview for this field:
     * 
     * @return string (html)
     */
    public function buildPreview( $mainOverview = false ){

        $html = '';

        $html .= '<span class="single-checkbox-wrapper">';

            $html .= '<input class="preview-input preview-'.esc_attr( $this->type ).'" disabled type="'.esc_attr( $this->type ).'">';

            $html .= '<label class="preview-label">'.esc_html( $this->getLabel() ).'</label>';

        $html .= '</span>';
    
        //do not display these in the lightbox:
        if( $mainOverview ){

            $html .= $this->getFieldIcon();
            $html .= $this->previewControls();

        }

        echo $html;

    }


    /**
     * Set a default label:
     * 
     * @return string
     */
    public function getDefaultLabel(){

        return __( 'True or false?', 'CuisineForms' );

    }


}