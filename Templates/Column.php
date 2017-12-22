<?php 

	use CuisineForms\Wrappers\Form;


	echo '<div class="column form-column">';

		$column->theTitle();


		$id = $column->getField( 'form' );
		Form::make( $id )->display();



	echo '</div>';


