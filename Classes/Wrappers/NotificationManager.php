<?php

namespace ChefForms\Wrappers;

class NotificationManager extends Wrapper {

    /**
     * Return the igniter service key responsible for the NotificationManager class.
     * The key must be the same as the one used in the assigned
     * igniter service.
     *
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'notification-manager';
    }

}
