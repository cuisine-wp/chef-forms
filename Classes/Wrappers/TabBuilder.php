<?php

namespace ChefForms\Wrappers;

class TabBuilder extends Wrapper {

    /**
     * Return the igniter service key responsible for the TabBuilder class.
     * The key must be the same as the one used in the assigned
     * igniter service.
     *
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'tab-builder';
    }

}
