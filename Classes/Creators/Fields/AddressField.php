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

        $this->setDefaultValue();

        $fields = $this->getFields();

        echo '<div class="address-wrapper">';
        foreach( $fields as $field ){

        	$field->render();

        }
        echo '</div>';

    }


    /**
     * Get The front-end fields for tha Address Field:
     * 
     * @return array
     */
    private function getFields(){

    	return array(

    		Field::text(
    			'street',
    			'Straatnaam & Huisnummer',
    			array(
    				'label'	=> false,
    				'placeholder' => 'Straatnaam & Huisnummer',
    				'validate'	=> 'address'
    			)
    		),

    		Field::text(
    			'zip',
    			'Postcode',
    			array(
    				'label'	=> false,
    				'placeholder'	=> 'Postcode',
    				'validate'	=> 'zipcode',
    				'class'	=> array( 'zip' )
    			)
    		),

    		Field::text(
    			'city',
    			'Stad',
    			array(
    				'label'	=> false,
    				'placeholder' => 'Stad',
    				'class' => array( 'city' )
    			)
    		)
    	);

    }


}