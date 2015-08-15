<?php
namespace ChefForms\Builders\Fields;

class RadioField extends ChoiceField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'radio';
    }
}