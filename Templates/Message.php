<div class="form-message <?php echo $msg['type'];?>">
	<?php 

	if( $msg['type'] !== 'error'){
		echo '<i class="fa fa-check"></i>';

	}else{
		echo '<i class="fa fa-bullhorn"></i>';

	}

	echo wpautop( $msg['text'] );

	?>
</div>