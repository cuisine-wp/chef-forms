<?php

namespace ChefForms\Wrappers;

class FormCreator extends Wrapper {

    /**
     * Return the igniter service key responsible for the FormCreator class.
     * The key must be the same as the one used in the assigned
     * igniter service.
     *
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'form-creator';
    }

}
