<?php
	// 2008-09-09: Cosmos survey...
	require 'include/core/common.php';
	
	if($_GET['school'] == 'g' && cache_load('confirmit_survey_p711303687_g') < 800)
	{
		$clicks = cache_load('confirmit_survey_p711303687_g');
		$clicks = ($clicks === false) ? 0 : $clicks;
		$clicks++;
		cache_save('confirmit_survey_p711303687_g', $clicks);
		
		header('Location: http://survey.confirmit.com/wix3/p711303687.aspx?hgid=24765');
	}
	else if($_GET['school'] == 'h' && cache_load('confirmit_survey_p711303687_h') < 400)
	{
		$clicks = cache_load('confirmit_survey_p711303687_h');
		$clicks = ($clicks === false) ? 0 : $clicks;
		$clicks++;
		cache_save('confirmit_survey_p711303687_h', $clicks);
		
		header('Location: http://survey.confirmit.com/wix3/p711303687.aspx?hhid=24765');
	}
?>