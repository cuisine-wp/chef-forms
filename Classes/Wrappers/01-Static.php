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

        if ( is_null( static::$instance ) ){
            static::$instance = new static();
        }
        return static::$instance;
    }


} 