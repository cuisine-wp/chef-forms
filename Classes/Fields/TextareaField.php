<?php
namespace ChefForms\Fields;

class TextareaField extends DefaultField{

    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'textarea';
    }


    /**
     * Generate the preview for this field:
     * 
     * @return void
     */
    public function buildPreview( $mainOverview = false ){

        $html = '';

        $html .= '<label class="preview-label">'.$this->getLabel().'</label>';
        $html .= '<textarea class="preview-input" disabled style="min-height:50px;">';

        if( $this->getProperty( 'placeholder', false ) )
            $html .= $this->getProperty( 'placeholder' );

        $html .= '</textarea>';

        if( $mainOverview ){
      		$html .= $this->getFieldIcon();
       		$html .= $this->previewControls();
       	}
       	
        echo $html;

    }

}