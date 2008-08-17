<?php
	function treasure_item($id,$options)
	{
		if(TREASURE_ITEM==1)
		{
			$output = '<a href="/treasure.php?f='. (substr(md5('beatbOXIngmastah' . $id . $_SESSION['login']['id']), 5, -1) . "g" . $id) .'" style="border: 0px;"><img src="http://images.hamsterpaj.net/sheep.png"></a>';
			if($options['return'] == true)
			{
				return $output;
			}
			else
			{
				echo $output;
			}
		}
	}
?>