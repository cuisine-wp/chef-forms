<?php
namespace ChefForms\Builders\Fields;

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

       	echo '<div class="'.$class.'">';

       		if( $this->label !== '' && $this->properties['label'] )
       		    echo '<label for="'.$this->id.'">'.$this->label.'</label>';

       		echo '<input type="file" ';

       		    echo 'id="'.$this->id.'" ';

       		    echo 'class="" ';

       		    echo 'name="'.$this->name.'" ';

       		    if( $this->properties['required'] )
       		    	echo 'data-validate="required" ';

       		echo '/>';

       	echo '</div>';

    }

}