<?php
namespace ChefForms\Fields;

use Cuisine\Wrappers\Field;
use Cuisine\Utilities\Session;

class AddressField extends DefaultField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'address';
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

        $fields = $this->getFrontendFields();

        $class = 'field-wrapper address-wrapper';

        $class .= ' '.$this->type;

        if( $this->properties['label'] )
            $class .= ' label-'.$this->properties['label'];

        echo '<div class="'.esc_attr( $class ).'">';

            echo '<label>'.esc_html( $this->getLabel() ).'</label>';

            echo '<div class="address-field-wrapper">';

            foreach( $fields as $field ){

                $field->render();

            }

            echo '</div>';

        echo '</div>';

    }


    /**
     * Get The front-end fields for tha Address Field:
     *
     * @return array
     */
    private function getFrontendFields(){

    	$sVal = array( 'address' );
        $zVal = array();

        if( $this->getProperty( 'countrySelect' ) && $this->getProperty( 'countrySelect' ) !== 'none' )
            $zVal = array( 'zipcode', 'zip-'.$this->getProperty( 'countrySelect' ) );

        $cVal = array();

        if( $this->getProperty( 'required' ) ){

            $sVal[] = 'required';
            $zVal[] = 'required';
            $cVal[] = 'required';

        }

        return array(

            Field::text(
                $this->id.'_street',
                __('Address','chefforms'),
                array(
                    'label'         => false,
                    'placeholder'   => __('Address','chefforms'),
                    'validation'    => $sVal
                )
            ),

            Field::text(
                $this->id.'_zip',
                __('Zipcode','chefforms'),
                array(
                    'label'         => false,
                    'placeholder'   => __('Zipcode','chefforms'),
                    'validation'    => $zVal,
                    'class'         => array( 'zip' )
                )
            ),

            Field::text(
                $this->id.'_city',
                __('City','chefforms'),
                array(
                    'label' => false,
                    'placeholder' => __('City','chefforms'),
                    'class' => array( 'city' ),
                    'validation' => $cVal
                )
            )
        );

    }


    /**
     * Get the value from this field
     *
     * @param  array $entry The entry being saved.
     * @return string (html)
     */
    public function getNotificationPart( $entryItems ){

        $html = '';
        $address = '';
        $allowed = array( $this->id.'_street', $this->id.'_zip', $this->id.'_city' );

        foreach( $entryItems as $entry ){

            if( in_array( $entry['name'], $allowed ) ){

                $address .= $entry['value'];

                if( $entry['name'] !== $this->id.'_zip' ){
                    $address .= '<br/>';

                }else{
                    $address .= ' ';

                }

            }
        }

        $label = $this->label;
        if( $label == '' && $this->properties['placeholder'] != '' )
            $label = $this->properties['placeholder'];

        $html .= '<tr><td><strong>'.esc_html( $label ).'</strong></td>';
        $html .= '<td>'.esc_html( $address ).'</td></tr>';

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
    /*public function build(){

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

    }*/


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

            echo '<div class="loader"><span class="spinner"></span></div>';

        echo '</div>';

    }

    /**
     * Generate the preview for this field:
     *
     * @return string (html)
     */
    public function buildPreview( $mainOverview = false ){

        $html = '';

        $html .= '<label class="preview-label">'.esc_html( $this->getLabel() ).'</label>';
        $html .= '<div class="preview-input-wrapper">';

            $html .= '<input type="text" class="preview-input preview-street" disabled  placeholder="'.__( 'Address', 'chefforms' ).'">';

            $html .= '<input type="text" class="preview-input preview-zip" disabled placeholder="'.__( 'Zipcode', 'chefforms' ).'">';

            $html .= '<input type="text" class="preview-input preview-city" disabled placeholder="'.__( 'City', 'chefforms' ).'">';

        $html .= '</div>';


        //do not display these in the lightbox:
        if( $mainOverview ){

            $html .= $this->getFieldIcon();
            $html .= $this->previewControls();

        }

        echo $html;

    }


    /*=============================================================*/
    /**             GETTERS & SETTERS                              */
    /*=============================================================*/


    /**
     * Creator fields
     *
     * @return array
     */
    protected function getFields(){

        $prefix = 'fields['.$this->id.']';
        $countryOptions = $this->getCountryOptions();

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
                __('Default value','chefforms'),
                array(
                    'defaultValue'  => $this->getProperty( 'defaultValue' )
                )
            ),

            Field::select(
                $prefix.'[countrySelect]',
                __( 'Country dropdown', 'chefforms' ),
                $countryOptions,
                array(
                    'defaultValue' => $this->getProperty( 'countrySelect' )
                )
            ),

            Field::checkbox(
                $prefix.'[required]',
                __('Required?','chefforms'),
                array(
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
     * Returns a key-value array of available fields
     *
     * @return array
     */
    private function getCountryOptions(){

        $_id = ( isset( $_GET['post'] ) ? $_GET['post'] : Session::postId() );

        $_dropdowns = array( 'none' => __('No field','chefforms') );
        $fields = get_post_meta( $_id, 'fields', true );

        foreach( $fields as $fid => $field ){

            if( $field['type'] == 'select' )
              $_dropdowns[ $fid ] = $field['label'];

        }

        return $_dropdowns;

    }


    /**
     * Set a default label:
     *
     * @return string
     */
    public function getDefaultLabel(){

        return __( 'Address', 'chefforms' );

    }

}