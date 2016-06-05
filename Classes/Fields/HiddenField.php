<?php
namespace ChefForms\Fields;

use Cuisine\Wrappers\Field;

class HiddenField extends DefaultField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'hidden';
    }


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
            $this->properties

        )->render();

    }


    /**
     * Generate the preview for this field:
     * 
     * @return string (html)
     */
    public function buildPreview( $mainOverview = false ){

        $html = '';

        $html .= '<input class="preview-input preview-'.esc_attr( $this->type ).'" disabled type="text"';

        if( $this->getProperty( 'placeholder', false ) )
            $html .= ' placeholder="'.esc_attr( $this->getProperty( 'placeholder' ) ).'"';

        $html .= '>';
    
        //do not display these in the lightbox:
        if( $mainOverview ){

            $html .= $this->getFieldIcon();
            $html .= $this->previewControls();

        }

        echo $html;

    }


}