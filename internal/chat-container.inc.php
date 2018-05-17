<div class="container-fluid">
	<?php 
	foreach($messages as $message) {
	    echo '<div class="row">';
	    if ($message->get_sender() != $_SESSION["user"]) {
	        echo '<div class="col">';
	        echo '<div class="card m-2"><div class="card-body">';
	        echo '<p class="font-weight-light">'.$message->get_sender().' ('.$message->get_time().')</p>';
	        echo '<hr />';
	        echo '<p>'.$message->get_text().'</p>';
	        echo '</div></div>';
	        echo '</div><div class="col"></div>';
	    } else {
	        echo '<div class="col"></div><div class="col">';
	        echo '<div class="card m-2"><div class="card-body">';
	        echo '<p class="font-weight-light">'.$message->get_sender().' ('.$message->get_time().')</p>';
	        echo '<hr />';
	        echo '<p>'.$message->get_text().'</p>';
	        echo '</div></div>';
	        echo '</div>';
	    }
	    echo '</div>';
	}
	?>
</div>