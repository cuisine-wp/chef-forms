<?php
    
namespace ChefForms\Front;

use Cuisine\Wrappers\Field;

class FieldBuilder{



    /**
     * Call the appropriate field class.
     *
     * @param string $class The custom field class name.
     * @param array $colProperties The defined field properties. Muse be an associative array.
     * @throws Exception
     * @return object ChefForms\Builder\FieldBuilder
     */
    public function make( $class, $name, $label, array $colProperties ){

        try {
            // Return the called class.
            $class =  new $class( $name, $label, $colProperties );

        } catch(\Exception $e){

            //@TODO Implement log if class is not found

        }

        return $class;

    }


    /**
     * Route through Cuisine's native Field classes
     *
     * @param string $name Name of the method
     * @param  array $attr
     * @return self::$name(), if it exists.
     */
    public function __call( $name, $attr ){

        $types = Field::getAvailableTypes();
        $names = array_keys( $types );

        //if method can be found:
        if( in_array( $name, $names ) ){

            $method = $types[ $name ];
            $props = ( isset( $attr[2] ) ? $attr[2] : array() );
            return $this->make( $method['class'], $attr[0], $attr[1], $props );
        }

        return false;
    }

}


   
