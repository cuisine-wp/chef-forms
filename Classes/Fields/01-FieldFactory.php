<?php
    
namespace ChefForms\Fields;

class FieldFactory{


    /*=============================================================*/
    /**             Make functions                                 */
    /*=============================================================*/


    /**
     * Call the appropriate field class.
     *
     * @param string $class The custom field class name.
     * @param array $colProperties The defined field properties. Muse be an associative array.
     * @throws Exception
     * @return object ChefForms\Builder\FieldBuilder
     */
    public function make( $class, $id, $form_id, array $colProperties ){

        try {
            // Return the called class.
            $class =  new $class( $id, $form_id, $colProperties );

        } catch(\Exception $e){

            //@TODO Implement log if class is not found

        }

        return $class;

    }

    /**
     * Return a TextField instance.
     *
     * @param string $name The name attribute of the text input.
     * @param string $label The Labelof the text input.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\TextField
     */
    public function text( $id, $form_id, array $properties = array() ){

        return $this->make( 'ChefForms\\Fields\\TextField', $id, $form_id, $properties );

    }


    /**
     * Return a NumberField instance.
     *
     * @param string $name The name attribute of the number input.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\NumberField
     */
    public function number($id, $form_id, array $properties = array()){

        return $this->make( 'ChefForms\\Fields\\NumberField', $id, $form_id, $properties );

    }


    /**
     * Return a EmailField instance.
     *
     * @param string $name The name attribute of the number input.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\EmailField
     */
    public function email($id, $form_id, array $properties = array()){

        return $this->make( 'ChefForms\\Fields\\EmailField', $id, $form_id, $properties );

    }

    /**
     * Return a WysiwygField instance.
     *
     * @param string $name The name attribute of the number input.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\WysiwygField
     */
    public function wysiwyg( $id, $form_id, array $properties = array()){
        return $this->make( 'ChefForms\\Fields\\WysiwygField', $id, $form_id, $properties );
    }


    /**
     * Return a DateField instance.
     *
     * @param string $name The name attribute of the date input.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\DateField
     */
    public function date($id, $form_id, array $properties = array()){

        return $this->make( 'ChefForms\\Fields\\DateField', $id, $form_id, $properties );

    }

     /**
     * Return a PasswordField instance.
     *
     * @param string $name The name attribute of the date input.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\PasswordField
     */
    public function password($id, $form_id, array $properties = array()){

        return $this->make( 'ChefForms\\Fields\\PasswordField', $id, $form_id, $properties );

    }


    /**
     * Return a NumberField instance.
     *
     * @param string $name The name attribute of the number input.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\FileField
     */
    public function file($id, $form_id, array $properties = array()){

        return $this->make( 'ChefForms\\Fields\\FileField', $id, $form_id, $properties );

    }


    /**
     * Return a TextareaField instance.
     *
     * @param string $name The name attribute of the textarea.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\TextareaField
     */
    public function textarea($id, $form_id, array $properties = array()){

        return $this->make( 'ChefForms\\Fields\\TextareaField', $id, $form_id, $properties);

    }

    /**
     * Return a CheckboxField instance.
     *
     * @param string $name The name attribute of the checkbox input.
     * @param string|array $options The checkbox options.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\CheckboxField
     */
    public function checkbox($id, $form_id, $properties = array()){

        return $this->make('ChefForms\\Fields\\CheckboxField', $id, $form_id, $properties );

    }

    /**
     * Return a CheckboxesField instance.
     *
     * @deprecated
     * @param string $name The name attribute.
     * @param array $options The checkboxes options.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\CheckboxesField
     */
    public function checkboxes($id, $form_id, array $options = array(), array $properties = array()){

        $properties = array_merge( $options, $properties );

        return $this->make( 'ChefForms\\Fields\\CheckboxesField', $id, $form_id, $properties );
    }

    /**
     * Return a RadioField instance.
     *
     * @param string $name The name attribute.
     * @param array $options The radio options.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\RadioField
     */
    public function radio($id, $form_id, array $options = array(), array $properties = array()){

        $properties = array_merge( $options, $properties );

        return $this->make( 'ChefForms\\Fields\\RadioField', $id, $form_id, $properties );
    }

    /**
     * Define a SelectField instance.
     *
     * @param string $name The name attribute of the select custom field.
     * @param array $options The select options tag.
     * @param bool $multiple
     * @param array $extras
     * @return \ChefForms\Builder\Fields\SelectField
     */
    public function select( $id, $form_id, array $options = array(), array $properties = array() ){

        $properties = array_merge( $options, $properties );

        return $this->make( 'ChefForms\\Fields\\SelectField', $id, $form_id, $properties );
    }


    /**
     * Define a AddressField instance.
     *
     * @param string $name The name attribute of the select custom field.
     * @param array $extras
     * @return \ChefForms\Builder\Fields\SelectField
     */
    public function address( $id, $form_id, array $properties = array() ){
        
        return $this->make( 'ChefForms\\Fields\\AddressField', $id, $form_id, $properties );
    }


     /**
     * Return a HiddenField instance.
     *
     * @param string $name The name attribute of the text input.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\TextField
     */
    public function hidden( $name,  $form_id, array $properties = array() ){

        return $this->make( 'ChefForms\\Fields\\HiddenField', $name, $form_id, $properties );

    }


    /**
     * Return a raw HTML block instance.
     *
     * @param string $name The name attribute of the field.
     * @param array $extras Extra field properties.
     * @return \ChefForms\Builder\Fields\HtmlField
     */
    public function html( $name,  $form_id, array $properties = array() ){

        return $this->make( 'ChefForms\\Fields\\HtmlField', $name, $form_id, $properties );

    }


    /**
     * If a field doesn't exist, try to locate it.
     *
     * @param string $name Name of the method
     * @param  array $attr
     * @return self::$name(), if it exists.
     */
    public function __call( $name, $attr ){

        $types = $this->getAvailableTypes();
        $names = array_keys( $types );

        //if method can be found:
        if( in_array( $name, $names ) ){

            $method = $types[ $name ];
            $props = ( isset( $attr[2] ) ? $attr[2] : array() );
            $options = ( isset( $attr[3] ) ? $attr[3] : array() );
            $props = array_merge( $props, $options );
            return $this->make( $method['class'], $attr[0], $attr[1], $props );
        }

        return false;
    }


    /*=============================================================*/
    /**             GETTERS & SETTERS                              */
    /*=============================================================*/


    /**
     * Returns a filterable array of field types
     *
     * @filter chef_forms_field_types
     * @return array
     */
    public static function getAvailableTypes(){

        $arr = array(

            'text'       => array(

                'name'      => __( 'Tekst', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\TextField',
                'icon'      => 'dashicons-editor-textcolor'
            ),

            'number'        => array(
                'name'      => __( 'Nummer', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\NumberField',            
                'icon'      => 'dashicons-plus'
            ),

            'email'        => array(
                'name'      => __( 'E-mailadres', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\EmailField',
                'icon'      => 'dashicons-email-alt'
            ),

            'wysiwyg'      => array(
                'name'      => __( 'Editor', 'chefforms' ),
                'class'     => 'ChefForms\\Fields\\WysiwygField',
                'icon'      => 'dashicons-editor-kitchensink'
            ),

            'date'          => array(

                'name'      => __( 'Datum', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\DateField',
                'icon'      => 'dashicons-calendar-alt'
            ),

            'password'          => array(

                'name'      => __( 'Wachtwoord', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\PasswordField',
                'icon'      => 'dashicons-post-status'
            ),

            'textarea'      => array(

                'name'      => __( 'Tekstvlak', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\TextareaField',
                'icon'      => 'dashicons-editor-alignleft'
            ),

            'file'          => array(

                'name'      => __( 'File upload', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\FileField',
                'icon'      => 'dashicons-cloud'
            ),

            'checkbox'      => array( 

                'name'      => __( 'Checkbox', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\CheckboxField',
                'icon'      => 'dashicons-yes'
            ),

            'checkboxes'    => array( 

                'name'      => __( 'Checkboxes', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\CheckboxesField',
                'icon'      => 'dashicons-forms'
            ),
            'radio'         => array( 

                'name'      => __( 'Radio buttons', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\RadioField',
                'icon'      => 'dashicons-editor-ul'
            ),
            'select'        => array( 

                'name'      => __( 'Select', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\SelectField',
                'icon'      => 'dashicons-arrow-down'
            ),

            'hidden'      => array( 

                'name'      => __( 'Verborgen', 'cuisine' ),
                'class'     => 'ChefForms\\Fields\\HiddenField',
                'icon'      => 'dashicons-hidden'
            ),

            'html'          => array(

                'name'      => __( 'HTML Block', 'chefforms' ),
                'class'     => 'ChefForms\\Fields\\HtmlField',
                'icon'      => 'dashicons-editor-code'
            ),

            'break'         => array(

                'name'      => __( 'Break', 'chefforms' ),
                'class'     => 'ChefForms\\Fields\\BreakField',
                'icon'      => 'dashicons-editor-unlink'

            ),

            'address'     => array(

                'name'      => __( 'Adres', 'chefforms' ),
                'class'     => 'ChefForms\\Fields\\AddressField',
                'icon'      => 'dashicons-admin-multisite'
            )
        );


        $arr = apply_filters( 'chef_forms_field_types', $arr );
        return $arr;
    }

}


