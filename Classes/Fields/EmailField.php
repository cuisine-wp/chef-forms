<?php
namespace CuisineForms\Fields;

class EmailField extends DefaultField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'email';
    }


    /**
     * Set a default label:
     * 
     * @return string
     */
    public function getDefaultLabel(){

        return __( 'E-mail', 'CuisineForms' );

    }
}