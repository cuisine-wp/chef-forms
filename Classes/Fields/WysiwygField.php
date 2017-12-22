<?php
namespace CuisineForms\Fields;

use Cuisine\Wrappers\Script;
use Cuisine\Wrappers\Field;
use Cuisine\Utilities\Url;

class WysiwygField extends DefaultField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'wysiwyg';
    }




    /**
     * Render this field on the front-end
     * @return [type] [description]
     */
    public function render(){

        $this->sanitizeProperties();
        $this->setClasses();
        $this->setScript();

        $type = $this->type;

        Field::textarea(

            $this->id,
            $this->getLabel(),
            $this->properties

        )->render();

    }


    /**
     * Setup the script used for this field
     */
    private function setScript(){

    	$url = Url::plugin( 'chef-forms', true ).'Assets/js/front/';
    	Script::register( 'wysiwyg-trigger', $url.'Editor', true );

    }



    /**
     * Set the classes for this field
     * @return void
     */
    private function setClasses(){

    	$this->properties['classes'] = array( 'editor' );

    }


    /**
     * Generate the preview for this field:
     * 
     * @return void
     */
    public function buildPreview( $mainOverview = false ){

        $html = '';

        $html .= '<label class="preview-label">'.$this->getLabel().'</label>';
        $html .= '<textarea disabled style="min-height:150px;" class="preview-input preview-wysiwyg">';

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