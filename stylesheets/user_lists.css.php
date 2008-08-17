<?php
	header('Content-type: text/css');
?>

.user_lists_list
{
	margin: 0px 0px 0px <?php echo (strstr('MSIE', $_SERVER['HTTP_HOST_AGENT']) ? 15 : 15); ?>px;
}

.user_lists_list li
{
	font-size: 16px;
	font-weight: bold;
	margin: 10px 0px 0px 15px;
	padding: 0px;
	
	float: left;
	clear: left;
}

.user_lists_list li.checked
{
	list-style-type: disc;
	list-style-image: url(http://images.hamsterpaj.net/user_lists/check_checked.png);
}

.user_lists_list li.unchecked
{
	list-style-type: circle;
	list-style-image: url(http://images.hamsterpaj.net/user_lists/check_unchecked.png);
}

.user_lists_list li.options
{
	list-style-type: none;
	float: right;
	clear: right;
	font-size: 12px;
	font-weight: none;
}