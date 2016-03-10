<?php
namespace ChefForms\Builders\Fields;

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

    	$url = Url::plugin( 'chef-forms', true ).'Assets/js/';
    	Script::register( 'wysiwyg-trigger', $url.'Editor', true );

    }



    /**
     * Set the classes for this field
     * @return void
     */
    private function setClasses(){

    	$this->properties['classes'] = array( 'editor' );

    }


}