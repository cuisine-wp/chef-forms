<?php
namespace ChefForms\Fields;

class FileField extends DefaultField{


    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType(){
        $this->type = 'file';
    }



    /**
     * Render this field on the front-end
     * @return [type] [description]
     */
    public function render(){

        $this->sanitizeProperties();

       	$class = 'field-wrapper';
       	$class .= ' '.$this->type;

       	if( $this->properties['label'] )
       	    $class .= ' label-'.$this->properties['label'];

       	echo '<div class="'.esc_attr( $class ).'">';

       		if( $this->label !== '' && $this->properties['label'] )
                echo '<label for="'.esc_attr( $this->id ).'">'.$this->label.'</label>';

       		echo '<input type="file" ';

       		    echo 'id="'.esc_attr( $this->id ).'" ';

       		    echo 'class="" ';

       		    echo 'name="'.esc_attr( $this->name ).'" ';

       		    if( $this->properties['required'] )
       		    	echo 'data-validate="required" ';

       		echo '/>';

       	echo '</div>';

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


                if( $this->isImage( $entry['value']['mime_type'] ) ){

                    $url = $entry['value']['url'];
                    $value = '<span style="text-align:center;width:100%;display:block;">';
                    $value .= '<img src="'.esc_url( $url ).'" style="width:auto;height:150px;"><br/>';
                    $value .= '<small><a href="'.esc_url( $url ).'" target="_blank">Download</a></small></span>';

                }else{
                    $value = $entry['value']['url'];
                }

                $html .= '<tr><td style="text-align:left;width:200px" width="200px"><strong>'.$label.'</strong></td>';
                $html .= '<td style="text-align:right">'.esc_html( $value ).'</td></tr>';

            }
        }

        return $html;

    }


    /**
     * Check to see if the upload is an image
     *
     * @return boolean
     */
    public function isImage( $mime_type ){

        if( substr( $mime_type, 0, 5 ) == 'image' )
            return true;

        return false;
    }


    /**
     * Set a default label:
     *
     * @return string
     */
    public function getDefaultLabel(){

        return __( 'Upload', 'chefforms' );

    }

}