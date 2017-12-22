<?php

namespace CuisineForms\Wrappers;

class SettingsManager extends Wrapper {

    /**
     * Return the igniter service key responsible for the SettingsManager class.
     * The key must be the same as the one used in the assigned
     * igniter service.
     *
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'settings-manager';
    }

}
