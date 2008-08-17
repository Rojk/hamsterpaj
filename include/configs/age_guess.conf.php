<?php
	/* Comments as keys, difference from answer in array */

	/* Correct */
	$AGE_GUESS_COMMENTS['Rätt!'] = array(0);
	$AGE_GUESS_COMMENTS['Duktigt!'] = array(0);
	$AGE_GUESS_COMMENTS['Du kan ju det här!'] = array(0);
	$AGE_GUESS_COMMENTS['Bra jobbat!'] = array(0);

	/* Almost */
	$AGE_GUESS_COMMENTS['Nära skjuter ingen hare'] = array(-1, 1);
	$AGE_GUESS_COMMENTS['Nästan i alla fall'] = array(-1, 1);

	/* Too young */
	$AGE_GUESS_COMMENTS['Så ung?'] = array(-4, -3, -2);

	/* Default wrong message */
	$AGE_GUESS_COMMENTS['Fel!'] = array();
	for($i = -100; $i < 100; $i++)
	{
		if($i != 0)
		{
			$AGE_GUESS_COMMENTS['Fel!'][] = $i;		
		}
	}
?>