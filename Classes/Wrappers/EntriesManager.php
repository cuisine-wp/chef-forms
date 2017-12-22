<?php

namespace CuisineForms\Wrappers;

class EntriesManager extends Wrapper {

    /**
     * Return the igniter service key responsible for the EntryManager class.
     * The key must be the same as the one used in the assigned
     * igniter service.
     *
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'entries-manager';
    }

}
