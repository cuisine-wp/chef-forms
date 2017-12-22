<?php

namespace CuisineForms\Wrappers;

class Entry extends Wrapper {

    /**
     * Return the igniter service key responsible for the Entry class.
     * The key must be the same as the one used in the assigned
     * igniter service.
     *
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'entry';
    }

}
