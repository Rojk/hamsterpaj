<?php
	/* ehm, tar väl bort "mina besök" eller nåt. */
	mysql_query('DELETE FROM traffa_visits WHERE tstamp < UNIX_TIMESTAMP() - 1209600');
?>