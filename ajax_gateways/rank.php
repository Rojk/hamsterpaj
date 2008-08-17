<?php

require('../include/core/common.php');

log_to_file('rank', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'rank');

if(login_checklogin() && isset($_GET['rank']) && isset($_GET['item_id']) && isset($_GET['item_type']))
{
	if(in_array($_GET['rank'], array(0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5)))
	{
		// Fetch old vote if there is one
		$query = 'SELECT * FROM user_ranks' . 
					' WHERE user_id="' . $_SESSION['login']['id'] . '"' .
						' AND item_id="' . $_GET['item_id'] . '"' .
						' AND item_type="' .$_GET['item_type'] . '"';
		log_to_file('rank', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'fetch_old_vote', $query);
		$result = mysql_query($query);
		unset($old_rank);
		if($data = mysql_fetch_assoc($result))
		{
			$old_rank = $data['rank'];
		}
		// If there are no previous votes on this item
		$queryinsert = 'INSERT INTO item_ranks (item_id, item_type, count, average) VALUES ("' .
						$_GET['item_id'] . '", "' .
						$_GET['item_type'] . '", "' .
						'1' . '", "' .
						$_GET['rank'] . '")';

		//Eftersom medelvärdena envisas med att bli fel så räknar vi om dom helt vid varje röstning ett tag
		$query = 'SELECT SUM(rank) as sum, COUNT(rank) as count FROM user_ranks WHERE item_id="' . $_GET['item_id'] . '"' .
						' AND item_type="' . $_GET['item_type'] . '" GROUP BY item_id';
		$result = mysql_query($query) or die(reqort_sql_error);
		if($data = mysql_fetch_assoc($result))
		{
			$average = $data['sum'] / $data['count'];
			$count = $data['count'];
		}
		//todo! ta bort denna tillfälliga och tunga sql-sats och ersätt med utkommenterade satser (när felen i dess hittats och rättats)
//		if(isset($old_rank))
//		{
			// If there are previous votes including one from current user
			$queryupdate = 'UPDATE item_ranks SET' .
//							' average = average + (("' . $_GET['rank'] . '" - "' . $old_rank . '") / count)' .
							' average = "' . $average . '", ' .
							' count = "' . $count . '"' . 
							' WHERE item_id="' . $_GET['item_id'] . '"' .
							' AND item_type="' .$_GET['item_type'] . '"';
/*
		}
		else
		{
			// If there are previous votes but none from this user
			$queryupdate = 'UPDATE item_ranks SET' .
							' count = count + 1,' .
							' average = average + ("' . $_GET['rank'] . '" / (count + 1))' .
							' WHERE item_id="' . $_GET['item_id'] . '"' .
							' AND item_type="' . $_GET['item_type'] . '"';
		}
*/
		log_to_file('rank', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'rank new item', $queryinsert);
		log_to_file('rank', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'update item rank', $queryupdate);
		mysql_query($queryinsert) or mysql_query($queryupdate) or die(report_sql_error($queryupdate, __FILE__, __LINE__));					

		if(!isset($old_rank))
		{
			$query = 'INSERT INTO user_ranks (item_id, item_type, rank, user_id) VALUES ("' . 
							$_GET['item_id'] . '", "' .
							$_GET['item_type'] . '", "' . 
							$_GET['rank'] . '", "' . 
							$_SESSION['login']['id'] . '")';
		}
		else
		{
			$query = 'UPDATE user_ranks SET' .
							' rank="' . $_GET['rank'] . '"' . 
							' WHERE user_id="' . $_SESSION['login']['id'] . '"' .
							' AND item_id="' . $_GET['item_id'] . '"' .
							' AND item_type="' . $_GET['item_type'] . '"';
		}
		log_to_file('rank', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'set user rank', $query);
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));					
		log_to_file('rank', LOGLEVEL_DEBUG, __FILE__, __LINE__, 
					'rank_set, ' . $_GET['rank'] . ' stars for ' . $_GET['item_type'] . ' ' . $_GET['item_id'] . ' from user ' . $_SESSION['login']['id']);
	}
	else
	{
		/* Varning, haxors försöker skicka icke godkända poängsummor, aktivera laserskölden! */
		die('Oh no, somebody set up us the bomb! Men med dina leeta mirkk-haxx0r-elite-skillz så sätter du väl bara upp en cURL som floodar kontodatabasen och fläskar in röster?');
	}
	
}					
					


?>