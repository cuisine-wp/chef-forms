<?php
namespace ChefForms\Wrappers;

abstract class StaticInstance {


    /**
     * Static bootstrapped instance.
     *
     * @var \ChefForms\Wrappers\StaticInstance
     */
    public static $instance = null;



    /**
     * Init the Assets Class
     *
     * @return \ChefForms\Admin\Assets
     */
    public static function getInstance(){

        return static::$instance = new static();

    }


} 