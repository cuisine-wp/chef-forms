<?php
namespace CuisineForms\Fields;

use Cuisine\Wrappers\Script;
use Cuisine\Wrappers\Field;
use CuisineForms\Front\Tag;

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
        $this->properties['class'] = 'datepicker';

        $type = $this->type;

        Field::text(

            $this->id,
            $this->getLabel(),
            $this->properties

        )->render();

    }

    /**
     * Set a default label:
     * 
     * @return string
     */
    public function getDefaultLabel(){

        return __( 'Please pick a date', 'cuisineforms' );

    }
}