<?php
	/* Poll */
	$poll = poll_fetch(array('type' => 'daily'));	
	if($poll[0]['can_answer'] == 1 && false)
	{
		echo '<a name="poll"></a>' . poll_render($poll[0]);
	}
	else
	{
		$output .= '<br style="clear: both;" /><a name="poll"></a>' . poll_render($poll[0]);
	}

?>