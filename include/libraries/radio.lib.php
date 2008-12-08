<?php
function radio_shoutcast_fetch() {
	$scs = &new ShoutcastInfo('kaizoku.se:8000');
	$scs2 = &new ShoutcastInfo('kaizoku.se:8000');
	if( !$scs->connect() )
	{
		if( !$scs2->connect() )
		{
		  //die($scs->error(TRUE));
		}
		else
		{
			$scs2->send();
			$data = $scs2->parse();
			$scs2->close();
		}
	  //die($scs->error(TRUE));
	}
	else
	{
		$scs->send();
		$data = $scs->parse();
		$scs->close();
	}
	return $data;
}
?>