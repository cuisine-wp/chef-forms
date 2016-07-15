<?php
    
namespace ChefForms\Fields;

use Cuisine\Wrappers\Field;
use Cuisine\Utilities\Sort;
use ChefForms\Front\Form\Tag;

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
     * Row ID for this field in the form:
     * 
     * @var Int
     */
    var $row;

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
     * Extra validation settings for this field
     * 
     * @var string
     */
    var $validation = '';



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
        $this->row = $this->properties['row'];
        $this->name = $this->properties['name'];
        $this->fieldType();

        if( isset( $this->properties['deletable'] ) ){

            if( $this->properties['deletable'] == 'false' || $this->properties['deletable'] == false )
                $this->deletable = false;

        }


        if( isset( $this->properties['validation'] ) ){

            //set validation as an array:
            if( !is_array( $this->properties['validation'] ) )
                $this->properties['validation'] = explode( ',', $this->properties['validation'] );

                
            //validation property as a string
            $this->validation = implode( ',', $this->properties['validation'] );

        }

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

        $this->properties['defaultValue'] = apply_filters( 'chef_forms_field_default_value', $this->properties['defaultValue'], $this );

        if( isset( $this->properties['defaultValue'] ) )
            $this->properties['defaultValue'] = Tag::field( $this->properties['defaultValue'] );

        if( empty( $this->properties['validation'][0] ) )
            unset( $this->properties['validation'] );

        if( empty( $this->properties['classes'] ) )
            unset( $this->properties['classes'] );


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


                $html .= '<tr><td style="text-align:left;width:200px" width="200px"><strong>'.$label.'</strong></td>';
                $html .= '<td style="text-align:right">';

                    $html .= esc_html( $entry['value'] );

                $html .= '</td></tr>';

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

        echo '<div class="field-block '.esc_attr( $this->type ).'" data-form_id="'.esc_attr( $this->formId ).'" data-field_id="'.esc_attr( $this->id ).'">';

            echo '<div class="field-preview">';
                $this->buildPreview( true );
            echo '</div>';

            $this->buildLightbox();

        echo '</div>';

    }


    /**
     * Build the field's lightbox:
     *
     * @return string (html)
     */
    public function buildLightbox(){
        echo '<div class="field-options">';

            echo '<div class="field-live-preview">';

                echo $this->buildPreview();

                echo '<span class="close">&times;</span>';
            
                echo $this->buildTabs();

            echo '</div>';

            echo '<div class="field-setting-tabs">';

                echo '<div class="field-settings-basics field-setting-tab-content active" id="tab-basics">';

                    $this->buildDefaultSettingsTab();

                echo '</div>';

                do_action( 'chef_forms_field_tab_content', $this );

            echo '</div>';
            $this->bottomControls();

        echo '</div>'; 
    }


    /**
     * The first tab in the lightbox
     * 
     * @return string ( html, echoed )
     */
    public function buildDefaultSettingsTab(){

        echo '<h2>'.__( 'Default Options', 'chefforms' ).'</h2>';

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
    }


    /**
     * Get all tabs, and ouput html:
     *
     * @return string (html)
     */
    public function buildTabs(){

        $tabs = $this->getTabs();

        //if( sizeof( $tabs ) > 1 ){
            $html = '<ul class="tab-container">';

                $i = 0;
                foreach( $tabs as $key => $tab ){

                    $html .= '<li class="tab '.( $i == 0 ? 'active' : '' ).'"';
                    $html .= ' data-tab="'.esc_attr( $key ).'">';

                        if( isset( $tab['icon'] ) && $tab['icon'] != '' )
                            $html .= '<i class="dashicons '.esc_attr( $tab['icon'] ).'"></i>';

                        $html .= esc_html( $tab['label'] );

                    $html .= '</li>';

                    $i++;
                }

            $html .= '</ul>';
        //}

        return $html;
    }


    /**
     * Generate the preview for this field:
     * 
     * @return string (html)
     */
    public function buildPreview( $mainOverview = false ){

        $html = '';

        $html .= '<label class="preview-label">'.$this->getLabel().'</label>';
        $html .= '<input class="preview-input preview-'.esc_attr( $this->type ).'" disabled type="'.esc_attr( $this->type ).'"';

        if( $this->getProperty( 'placeholder', false ) )
            $html .= ' placeholder="'.esc_attr( $this->getProperty( 'placeholder' ) ).'"';

        $html .= '>';
    
        //do not display these in the lightbox:
        if( $mainOverview ){

            $html .= $this->getFieldIcon();
            $html .= $this->previewControls();

        }

        echo $html;

    }


    /**
     * Returns the preview button and delete text
     * 
     * @return string
     */
    public function previewControls(){

        $html = '<div class="field-controls">';

            if( $this->deletable ){
                $html .= '<span class="delete-field">';
                    $html .= '<i class="dashicons dashicons-trash"></i>';
                    $html .= __( 'Delete field', 'chefforms' );
                $html .= '</span>';
            }

            $html .= '<span class="open-lightbox button button-primary">';
                $html .= __( 'Edit', 'chefforms' );
            $html .= '</span>';

        $html .= '</div>';

        return $html;
    }


    /*=============================================================*/
    /**             Lightbox                                       */
    /*=============================================================*/



    /**
     * Create the bottom controls:
     * 
     * @return string ( html, echoed )
     */
    public function bottomControls(){

        echo '<div class="bottom-controls">';
            if( $this->deletable ){
                echo '<p class="delete-field">';
                    echo '<span class="dashicons dashicons-trash"></span>';
                    echo __( 'Delete', 'chefforms' ).'</p>';
                echo '</p>';
            }
    
            echo '<span class="save-field button">';
                _e( 'Save field', 'chefforms' );
            echo '</span>';
        echo '</div>';
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
                    'class'         => array( 'update', 'update-label', 'label-field' ),
                    'defaultValue'  => $this->getProperty( 'label', 'Label' )
                )
            ),

            Field::text(
                $prefix.'[placeholder]',
                'Placeholder',
                array(
                    'class'         => array( 'update', 'update-placeholder' ),
                    'defaultValue'  => $this->getProperty( 'placeholder' )
                )
            ),

             Field::text(
                $prefix.'[defaultValue]',
                __( 'Default value', 'chefforms' ),
                array(
                    'defaultValue'  => $this->getProperty( 'defaultValue' )
                )
            ),


            Field::checkbox(
                $prefix.'[required]',
                __( 'Required', 'chefforms' ),
                array(
                    'class'         => array( 'update', 'update-label', 'req-field' ),
                    'defaultValue'  => $this->getProperty( 'required' )
                )
            ),

            Field::hidden(
                $prefix.'[validation]',
                array(
                    'defaultValue'  => $this->validation
                )
            ),

            Field::hidden(
                $prefix.'[type]',
                array(
                    'defaultValue'  => $this->type
                )    
            ),

            Field::hidden(
                $prefix.'[deletable]',
                array(
                    'defaultValue' => ( $this->deletable ? 'true' : 'false' )
                )
            ),

            Field::hidden(
                $prefix.'[position]',
                array(
                    'class'         => array( 'field-input', 'position-input' ),
                    'defaultValue'  => $this->position
                )    
            ),
            
            Field::hidden(
                $prefix.'[row]',
                array(
                    'class' => array( 'field-input', 'row-input' ),
                    'defaultValue' => $this->row
                )
            )



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
     * Returns the field icon as registered with Chef Forms
     * 
     * @return string
     */
    public function getFieldIcon(){

        $html = '';

        $types = FieldFactory::getAvailableTypes();
        $icon = $types[ $this->type ]['icon' ];
        if( isset( $icon ) && $icon !== '' )
            $html = '<span class="dashicons '.esc_attr( $icon ).'"></span>';

        return $html;
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

        if( !isset( $this->properties['label'] ) )
            $this->properties['label'] = $this->getDefaultLabel();

        if( !isset( $this->properties['row'] ) )
            $this->properties['row'] = false;

    }

    /**
     * Set a default label:
     * 
     * @return string
     */
    public function getDefaultLabel(){

        return __( 'Some text', 'chefforms' );

    }

    /**
     * Return tabs
     * 
     * @return array
     */
    public function getTabs(){

        $default = array(
            'basics' => array(
                'label'     => __( 'Basic options', 'chefforms' ),
                'icon'      => 'dashicons-admin-generic',
                'position'  => -1
            )
        );

        $tabs = apply_filters( 'chef_forms_field_tabs', $default );

        $tabs = Sort::byField( $tabs, 'position', 'ASC' );

        return $tabs;
    }

}


   
