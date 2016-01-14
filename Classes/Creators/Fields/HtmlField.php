<?php
namespace ChefForms\Builders\Fields;

use Cuisine\Wrappers\Field;

class HtmlField extends DefaultField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'html';
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

        echo '<div class="field-wrapper">';
            
            if( $this->getProperty( 'label' ) !== '' )
                echo '<h3>'.$this->getProperty( 'label' ).'</h3>';

            if( $this->getProperty( 'defaultValue' ) !== '' )
                echo $this->getProperty( 'defaultValue' );

        echo '</div>';

    }



    /**
     * Get the value from this field, including the label for the notifications
     * 
     * @param  array $entry The entry being saved.
     * @return string (html)
     */
    public function getNotificationPart( $entryItems ){

        return '';

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
     * Creator fields
     * 
     * @return array
     */
    private function getFields(){

        $prefix = 'fields['.$this->id.']';

        return array(

            Field::text(
                $prefix.'[label]',
                'Titel',
                array(
                    'defaultValue'  => $this->getProperty( 'label', 'Titel' )
                )
            ),

            Field::hidden(
                $prefix.'[placeholder]',
                array(
                    'defaultValue'  => $this->getProperty( 'placeholder' )
                )
            ),

             Field::textarea(
                $prefix.'[defaultValue]',
                'Text',
                array(
                    'defaultValue'  => $this->getProperty( 'defaultValue' )
                )
            ),


            Field::hidden(
                $prefix.'[required]',
                array(
                    'defaultValue'  => 'false'
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



        );
    }

}