<?php
	require('../include/core/common.php');
	include(PATHS_LIBRARIES . 'poll.lib.php');
	
	if($_GET['action'] == 'vote' && is_numeric($_GET['poll_id']) && in_array($_GET['answer_id'], array(1, 2, 3, 4, 5, 6, 7)))
	{
		echo 'Ok';
		$poll = poll_fetch(array('id' => $_GET['poll_id']));
		preint_r($poll);
		if($poll[0]['can_answer'] == true)
		{
			$query = 'UPDATE poll SET alt_' . $_GET['answer_id'] . '_votes = alt_' . $_GET['answer_id'] . '_votes + 1';
			$query .= ' WHERE id = "' . $_GET['poll_id'] . '"';
			
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			
			if(login_checklogin())
			{
				$query = 'INSERT INTO poll_answers (poll_id, user_id, answer_id) VALUES("' . $_GET['poll_id'] . '", "' . $_SESSION['login']['id'] . '", "' . $_GET['answer_id']. '")';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			}
			else
			{
			
			}
		}
	}
?>