<?php
	function jscript_alert($message, $return = false)
	{
		$out = '<script>alert("' . str_replace(array('<br />', '"'), array('\n', '\\"'), $message) . '");</script>';
		if($return === true)
		{
			return $out;
		}
		else
		{
			echo $out;
		}
	}
	
	function jscript_location($target)
	{
		echo '<script language="javascript" type="text/JavaScript">window.location="' . $target . '";</script>';
	}
	
	function jscript_go_back($pages = 1)
	{
		echo '<script language="javascript" type="text/JavaScript">history.go(-' . $pages . ');</script>';
	}
	
	function jscript_selfclose()
	{
		echo '<script language="javascript" type="text/javascript">self.close();</script>';
	}
?>