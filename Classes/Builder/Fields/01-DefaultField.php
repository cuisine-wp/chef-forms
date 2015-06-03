<?php
    
namespace ChefForms\Builder\Fields;

use Cuisine\Fields\DefaultField as CuisineDefaultField;

class DefaultField extends CuisineDefaultField{

    /**
     * Id of this field
     * 
     * @var String
     */
    var $id;

    /**
     * The id of this fields' form.
     * 
     * @var Integer
     */
    var $formId;

    /**
     * Type of this field
     * 
     * @var String
     */
    var $type;

    /**
     * Position of this field in the form
     * 
     * @var Int
     */
    var $position;

    /**
     * Properties of this field
     * 
     * @var array
     */
    var $properties;

    /**
     * Array of custom classes
     * 
     * @var array
     */
    var $classes = array();


    /**
     * Define a core Field.
     *
     * @param array $properties The text field properties.
     */
    public function __construct( $id, $formId, $position, $props ){

        $this->id = $id;
        $this->form_id = $formId;
        $this->position = $position;
        $this->properties = $props;

        $this->fieldType();
        //$this->setDefaults();

    }


}


   
