<?php
namespace ChefForms\Builders\Fields;

use Cuisine\Wrappers\Script;
use Cuisine\Wrappers\Field;
use ChefForms\Front\Tag;

class DateField extends DefaultField{

    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'date';
    }



     /**
     * Render this field on the front-end
     * @return [type] [description]
     */
    public function render(){

        $this->sanitizeProperties();
        $type = $this->type;

        Field::text(

            $this->id,
            $this->getLabel(),
            $this->properties

        )->render();

    }



    /**
     * Check the default value, before rendering
     * 
     */
    public function sanitizeProperties(){


    	$this->properties['class'] = array( 'datepicker' );

        if( isset( $this->properties['defaultValue'] ) )
            $this->properties['defaultValue'] = Tag::field( $this->properties['defaultValue'] );


        if( isset( $this->properties['required'] ) && $this->properties['required'] !== 'true' )
            $this->properties['required'] = false;
        
    }

}