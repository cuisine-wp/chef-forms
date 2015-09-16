<?php
namespace ChefForms\Builders\Fields;

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


}