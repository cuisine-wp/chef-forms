<?php

namespace CuisineForms\Wrappers;

class FormBuilderManager extends Wrapper {

    /**
     * Return the igniter service key responsible for the FormBuilderManager class.
     * The key must be the same as the one used in the assigned
     * igniter service.
     *
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'form-builder-manager';
    }

}
