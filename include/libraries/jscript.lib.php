<?php
	function jscript_alert($message)
	{
		echo '<script>alert("' . str_replace(array('<br />', '"'), array('\n', '\\"'), $message) . '");</script>';
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