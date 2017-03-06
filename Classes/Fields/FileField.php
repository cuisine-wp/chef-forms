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
     * Returns the correct value for this field out of the supplied entry items
     *
     * @param  array $entryItems
     *
     * @return array
     */
    public function getValueFromEntry( $entryItems )
    {

        $value = [ 'label' => '', 'value' => '' ];

        foreach( $entryItems as $entry ){

            if( $this->name == $entry['name'] ){

                $value['label'] = $this->label;
                if( $value['label'] == '' && $this->properties['placeholder'] != '' )
                    $value['label'] = $this->properties['placeholder'];

                if( $this->isImage( $entry['value']['mime_type'] ) ){

                    $url = $entry['value']['url'];
                    $value = '<span style="text-align:center;width:100%;display:block;">';
                    $value .= '<img src="'.esc_url( $url ).'" style="width:auto;height:150px;"><br/>';
                    $value .= '<small><a href="'.esc_url( $url ).'" target="_blank">Download</a></small></span>';

                }else{
                    $value = $entry['value']['url'];
                }

                $value['value'] = $value;

            }
        }

        return $value;
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