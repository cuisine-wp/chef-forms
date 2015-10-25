<?php
    
namespace ChefForms\Builders\Fields;

use Cuisine\Wrappers\Field;
use ChefForms\Front\Tag;

class DefaultField{

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
     * Name of this field in the form
     * 
     * @var string
     */
    var $name;


    /**
     * Public var for the label
     * 
     * @var string
     */
    var $label;


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
     * Is this field deletable by the user?
     * 
     * @var boolean
     */
    var $deletable = true;


    /**
     * Define a core Field.
     *
     * @param array $properties The text field properties.
     */
    public function __construct( $id, $formId, $props = array() ){

        $this->id = $id;
        $this->formId = $formId;

        $this->properties = $props;
        $this->setDefaults();

        $this->position = $this->properties['position'];
        $this->name = $this->properties['name'];
        $this->fieldType();

        if( isset( $this->properties['deletable'] ) )
            $this->deletable = $this->properties['deletable'];
        

        //set the label
        $this->label = $this->getLabel();


    }


    /*=============================================================*/
    /**             FRONTEND                                       */
    /*=============================================================*/


    /**
     * Render this field on the front-end
     * @return [type] [description]
     */
    public function render(){

        $this->sanitizeProperties();
        $type = $this->type;

        Field::$type(

            $this->id,
            $this->getLabel(),
            $this->properties

        )->render();

    }


    /**
     * Check the default value, before rendering
     * 
     */
    public function sanitizeProperties(){

        if( isset( $this->properties['defaultValue'] ) )
            $this->properties['defaultValue'] = Tag::field( $this->properties['defaultValue'] );


        if( isset( $this->properties['required'] ) && $this->properties['required'] !== 'true' )
            $this->properties['required'] = false;
        
    }


    /**
     * Get the value from this field, including the label for the notifications
     * 
     * @param  array $entry The entry being saved.
     * @return string (html)
     */
    public function getNotificationPart( $entryItems ){

        $html = '';

        foreach( $entryItems as $entry ){

            if( $this->name == $entry['name'] ){

                $label = $this->label;
                if( $label == '' && $this->properties['placeholder'] != '' )
                    $label = $this->properties['placeholder'];


                $html .= '<tr><td><strong>'.$label.'</strong></td>';
                $html .= '<td>'.$entry['value'].'</td></tr>';

            } 
        }

        return $html;

    }



    /*=============================================================*/
    /**             BACKEND                                        */
    /*=============================================================*/


    /**
     * Build up the field block
     * 
     * @return string ( html, echoed )
     */
    public function build(){

        echo '<div class="field-block '.$this->type.'" data-form_id="'.$this->formId.'" data-field_id="'.$this->id.'">';

            echo '<div class="field-preview">';
                echo $this->buildPreview();
                echo '<span class="toggle-field"></span>';
            echo '</div>';

            echo '<div class="field-options">';

                $fields = $this->getFields();

                foreach( $fields as $field ){

                    $field->render();
                }


                //render the javascript-templates seperate, to prevent doubles
                $rendered = array();
                foreach( $fields as $field ){

                    if( method_exists( $field, 'renderTemplate' ) && !in_array( $field->name, $rendered ) ){

                        echo $field->renderTemplate();
                        $rendered[] = $field->name;

                    }
                }

                $this->bottomControls();

            echo '</div>';          
            echo '<div class="loader"><span class="spinner"></span></div>';

        echo '</div>';

    }


    /**
     * Generate the preview for this field:
     * 
     * @return void
     */
    public function buildPreview(){

        $html = '';

        $html .= '<label>'.$this->getLabel().'</label>';

        $html .= '<span class="field-type">'.$this->type.'</span>';

        return $html;

    }


    /**
     * Create the bottom controls:
     * 
     * @return string ( html, echoed )
     */
    public function bottomControls(){

        if( $this->deletable ){
            echo '<p class="delete-field">';
                echo '<span class="dashicons dashicons-trash"></span>';
                echo __( 'Verwijder', 'chefsections' ).'</p>';
            echo '</p>';
        }
        
    }



    /*=============================================================*/
    /**             GETTERS & SETTERS                              */
    /*=============================================================*/


    /**
     * Creator fields
     * 
     * @return array
     */
    private function getFields(){

        $prefix = 'fields['.$this->id.']';

        return array(

            Field::text(
                $prefix.'[label]',
                'Label',
                array(
                    'defaultValue'  => $this->getProperty( 'label', 'Label' )
                )
            ),

            Field::text(
                $prefix.'[placeholder]',
                'Placeholder',
                array(
                    'defaultValue'  => $this->getProperty( 'placeholder' )
                )
            ),

             Field::text(
                $prefix.'[defaultValue]',
                'Standaard Waarde',
                array(
                    'defaultValue'  => $this->getProperty( 'defaultValue' )
                )
            ),


            Field::checkbox(
                $prefix.'[required]',
                'Verplicht?',
                array(
                    'defaultValue'  => $this->getProperty( 'required' )
                )
            ),

            Field::hidden(
                $prefix.'[type]',
                array(
                    'defaultValue'  => $this->type
                )    
            ),

            Field::hidden(
                $prefix.'[position]',
                array(
                    'class'         => array( 'field-input', 'position-input' ),
                    'defaultValue'  => $this->position
                )    
            ),



        );
    }



    /**
     * Returns the label with a required astrix
     * 
     * @return string ( html )
     */
    public function getLabel(){

        $label = '';

        if( $this->getProperty( 'label' ) ){
            $label .= $this->getProperty( 'label' );

        }else if( $this->getProperty( 'placeholder' ) ){
            $label .= $this->getProperty( 'placeholder' );

        }

        if( $this->getProperty( 'required' ) == 'true' )
            $label .= '<span class="req">*</span>';

        return ( $label !== '' ? $label : false );
    }



    /**
     * Return a property
     * 
     * @param  string $name
     * @param  string $default
     * @return mixed (string/bool)
     */
    public function getProperty( $name, $default = false ){

        if( isset( $this->properties[ $name ] ) )
            return $this->properties[ $name ];

        return $default;
    }


    /**
     * Set the default properties:
     *
     * @return array (properties object)
     */
    public function setDefaults(){

        if( !isset( $this->properties['position' ] ) )
            $this->properties['position'] = 999; 

        if( !isset( $this->properties['name'] ) )
            $this->properties['name'] = 'field_'.$this->id.'_'.$this->formId;


    }

}


   
