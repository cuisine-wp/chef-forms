<?php 

	use ChefForms\Wrappers\Form;


	echo '<div class="column form-column">';

	if( $column->getField( 'title' ) ){

		echo '<h2>'.$column->getField( 'title' ).'</h2>';
	
	}


	$id = $column->getField( 'form' );
	Form::make( $id )->display();



	echo '</div>';


