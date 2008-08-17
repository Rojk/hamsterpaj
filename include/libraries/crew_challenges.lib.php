<?php

	crew_challenges_fetch()
	{
		$crew_challenges[0]['label'] = '';
		$crew_challenges[0]['text'] = '';
		$crew_challenges[0]['handle'] = '';
		
		$crew_challenges[0]['label'] = '';
		$crew_challenges[0]['text'] = '';
		$crew_challenges[0]['handle'] = '';
		
		$crew_challenges[0]['label'] = '';
		$crew_challenges[0]['text'] = '';
		$crew_challenges[0]['handle'] = '';
		
		$crew_challenges[0]['label'] = '';
		$crew_challenges[0]['text'] = '';
		$crew_challenges[0]['handle'] = '';
		
		$crew_challenges[0]['label'] = '';
		$crew_challenges[0]['text'] = '';
		$crew_challenges[0]['handle'] = '';
		

	
		$query = 'SELECT challenge FROM crew_challenges WHERE user = "' . $_SESSION['login']['id'] . '"';
		$result = mysql_query($query);
		while($data = mysql_fetch_assoc($result))
		{
			$accepted_challenges[] = $data;
		}
	}

?>