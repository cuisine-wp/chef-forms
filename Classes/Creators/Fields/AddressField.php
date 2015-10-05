<?php
namespace ChefForms\Builders\Fields;

use Cuisine\Wrappers\Field;

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

        $fields = $this->getFields();

        $class = 'field-wrapper address-wrapper';

        $class .= ' '.$this->type;

        if( $this->properties['label'] )
            $class .= ' label-'.$this->properties['label'];

        echo '<div class="'.$class.'">';

            echo '<label>'.$this->getLabel().'</label>';

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
    private function getFields(){

    	$sVal = array( 'address' );
        $zVal = array( 'zipcode' );
        $cVal = array();

        if( $this->getProperty( 'required' ) ){

            $sVal[] = 'required';
            $zVal[] = 'required';
            $cVal[] = 'required';

        }

        return array(

            Field::text(
                'street',
                'Straatnaam & Huisnummer',
                array(
                    'label'         => false,
                    'placeholder'   => 'Straatnaam & Huisnummer',
                    'validation'    => $sVal
                )
            ),

            Field::text(
                'zip',
                'Postcode',
                array(
                    'label'         => false,
                    'placeholder'   => 'Postcode',
                    'validation'    => $zVal,
                    'class'         => array( 'zip' )
                )
            ),

            Field::text(
                'city',
                'Stad',
                array(
                    'label' => false,
                    'placeholder' => 'Stad',
                    'class' => array( 'city' ),
                    'validation' => $cVal
                )
            )
        );

    }


}