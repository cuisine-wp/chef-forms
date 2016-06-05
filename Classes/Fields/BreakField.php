<?php
namespace ChefForms\Fields;

use Cuisine\Wrappers\Field;

class BreakField extends DefaultField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'break';
    }


    /*=============================================================*/
    /**             FRONTEND                                       */
    /*=============================================================*/


    /**
     * Render this field on the front-end
     * @return [type] [description]
     */
    public function render(){

        echo '<hr/>';

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
     * Generate the preview for this field:
     * 
     * @return void
     */
    public function buildPreview( $mainOverview = false ){

        $html = '';

        $html .= '<label>'.esc_html( $this->getLabel() ).'</label>';
        $html .= '<span class="field-type"><hr/></span>';

        //do not display these in the lightbox:
        if( $mainOverview ){

            $html .= $this->getFieldIcon();
            $html .= $this->previewControls();

        }

        echo $html;

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
     * Creator fields
     * 
     * @return array
     */
    private function getFields(){

        $prefix = 'fields['.$this->id.']';

        return array(

            Field::hidden(
                $prefix.'[label]',
                array(
                    'classes'       => array( 'update', 'update-label', 'label-field' ),
                    'defaultValue'  => $this->getProperty( 'label', 'Titel' )
                )
            ),

            Field::hidden(
                $prefix.'[placeholder]',
                array(
                    'defaultValue'  => $this->getProperty( 'placeholder' )
                )
            ),

             Field::hidden(
                $prefix.'[defaultValue]',
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