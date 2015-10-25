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
                $this->id.'_street',
                'Straatnaam & Huisnummer',
                array(
                    'label'         => false,
                    'placeholder'   => 'Straatnaam & Huisnummer',
                    'validation'    => $sVal
                )
            ),

            Field::text(
                $this->id.'_zip',
                'Postcode',
                array(
                    'label'         => false,
                    'placeholder'   => 'Postcode',
                    'validation'    => $zVal,
                    'class'         => array( 'zip' )
                )
            ),

            Field::text(
                $this->id.'_city',
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
        
        $html .= '<tr><td><strong>'.$label.'</strong></td>';
        $html .= '<td>'.$address.'</td></tr>';

        return $html;

    }


}