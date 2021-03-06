<?php
namespace ChefForms\Fields;

use Cuisine\Wrappers\Field;

class CheckboxesField extends ChoiceField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'checkboxes';
    }


    /**
     * Render this field on the front-end
     * @return [type] [description]
     */
    public function render(){

        $this->sanitizeProperties();
        $type = $this->type;
        $this->properties['wrapper-class'] = array( 'checkboxes' );

        Field::$type(

            $this->id,
            $this->getLabel(),
            $this->getChoices(),
            $this->properties

        )->render();

    }


    /**
     * Get the value from this field, including the label for the notifications
     *
     * @param  array $entry The entry being saved.
     * @return string (html)
     */
    public function getNotificationPart( $entryItems ){

        $html = '';
        $entry = $this->getValueFromEntry( $entryItems );

        $value = $entry['value'];
        if( is_array( $entry['value'] ) )
            $value = implode( ', ', $value );

        $html .= '<tr><td style="text-align:left;width:200px" width="200px">';
            $html .= '<strong>'.esc_html( $entry['label'] ).'</strong></td>';
        $html .= '<td style="text-align:right">'.esc_html( $value ).'</td></tr>';


        return $html;

    }


    /**
     * Set a default label:
     *
     * @return string
     */
    public function getDefaultLabel(){

        return __( 'Please select some', 'chefforms' );

    }

}