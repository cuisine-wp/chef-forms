<?php
namespace ChefForms\Fields;

class PasswordField extends DefaultField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'password';
    }

	/**
     * Get the value from this field, including the label for the notifications
     * 
     * @param  array $entry The entry being saved.
     * @return string (html)
     */
    public function getNotificationPart( $entryItems ){

        return '';
    }
}