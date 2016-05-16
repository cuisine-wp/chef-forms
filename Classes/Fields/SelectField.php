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
}