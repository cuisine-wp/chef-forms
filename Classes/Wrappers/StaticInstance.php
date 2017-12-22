<?php
namespace CuisineForms\Wrappers;

abstract class StaticInstance {


    /**
     * Static bootstrapped instance.
     *
     * @var \CuisineForms\Wrappers\StaticInstance
     */
    public static $instance = null;



    /**
     * Init the Assets Class
     *
     * @return \CuisineForms\Admin\Assets
     */
    public static function getInstance(){

        return static::$instance = new static();

    }


} 