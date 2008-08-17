<?php
	header('Content-type: text/css');

	define('BORDER_COLOR_DARK', '#565656');
	define('BORDER_COLOR_LIGHT', '#a3a7ab');
	define('LIGHT_BLUE', '#e0e7ea');
?>
body
{
	//background: #a5b8c9;
	background: #3e637e url('http://images.hamsterpaj.net/ui/body_bg.jpg') repeat-x;
	margin: 0px;
	font-family: arial, verdana, "ms trebuchet";
	font-size: 12px;
	padding: 10px;
	padding-top: 0px;
}

a
{
	color: black;
}

img
{
	border: none;
}

input
{
	background: #f0f7fa;
	border: 1px solid <?=BORDER_COLOR_LIGHT?>;
}

input:focus
{
	background: white;
}

h1, h2, h3, h4
{
	font-weight: normal;
	margin: 3px;
	margin-left: 0px;
}

th
{
	text-align: left;
}

h1
{
	font-size: 20px;
}

h2
{
	font-size: 17px;
}

ul
{
	margin: 4px;
	margin-left: 15px;
}

.button
{
	height: 20px;
	background: url('/images/ui/button_fade.png');
	margin: 2px;
}

.button_small
{
	background: url('/images/ui/button_fade.png');
	margin: 1px;
	padding: 0px;
	font-size: 9px;
}

#site_container
{
	width: 821px;
	float: left;
}

#main
{
	background: white;
	width: 814px;
	border: 1px solid <?=BORDER_COLOR_DARK?>;
	border-top: none;
}

#top
{
	border-top: 1px solid #565656;
	background: white;
}

#logo
{
	margin-right: 10px;
	float: left;
}


#menu_main
{
	z-index: -1;
	height: 21px;
	width: 810px;
	font-size: 12px;
	font-weight: bold;
	font-family: "ms trebuchet", verdana, arial;
	margin-top: 3px;
}

#menu_main a
{
	text-decoration: none;
	color: #3e3e3e;
}

#menu_main a:hover
{
	color: black;
}

#menu_main div
{
	float: left;
	border: 1px solid <?=BORDER_COLOR_DARK?>;
	border-bottom: none;
	padding-left: 5px;
	padding-right: 5px;
	margin-right: 6px;
	margin-top: 3px;
	background: url('http://images.hamsterpaj.net/ui/menu_fade.png') repeat-x #fdaf26;
	line-height: 17px;
	color: white;
}

#menu_main .active
{
	height: 18px;
	margin-bottom: -1px;
	background: white;
}

#menu_main .active a
{
	color: black;
}

#logo
{
	width: 284px;
	height: 60px;
	padding: 0px;
	overflow: hidden;
}

#right_now
{
	width: 811px;
	height: 20px;
	line-height: 20px;
	padding-left: 3px;
	border-top: 1px solid <?=BORDER_COLOR_LIGHT?>;
	border-bottom: 1px solid <?=BORDER_COLOR_LIGHT?>;
	background: url('http://images.hamsterpaj.net/ui/rightnowbg.png');
	color: black;
	background: white;
	float: left;
}

#right_now a
{
	color: black;
}

#main_left
{
	width: 165px;
	float: left;
	clear: left;
}

.module
{
	text-align: left;
	background: url('http://images.hamsterpaj.net/ui/left_module_bg.png') repeat-x #fdefe1;
	padding: 2px;
	margin-top: 5px;
	border: 1px solid #565656;
}

.module h1
{
	margin: 0px;
	padding: 0px;
	font-size: 15px;
	color: white;
	font-weight: bold;
	margin-bottom: 5px;
}

#content_menu
{
	width: 638px;
	height: 18px;
	font-weight: normal;
	margin-top: 5px;
}

#content_menu div
{
	float: left;
	border: 1px solid <?=BORDER_COLOR_DARK?>;
	padding-left: 5px;
	padding-right: 5px;
	margin-right: 6px;
	background: url('http://images.hamsterpaj.net/ui/submenu_bg.png') repeat-x #fdaf26;
	line-height: 17px;
	color: white;
}

#content_menu a
{
	text-decoration: none;
	color: #3e3e3e;
}

#content_menu a:hover
{
	color: black;
}

#content_menu .active
{
	height: 18px;
	background: white;
	border-bottom: none;
	margin-bottom: -1px;
}

#middle
{
	float: left;
	width: 646px;
	margin-left: 5px;
}

#content
{
	background: white;
	border: 1px solid #565656;
	padding: 3px;
}

#login_status_bar
{
	border-top: 1px solid #ededed;
	height: 48px;
	background: url('http://images.hamsterpaj.net/login_bar/background.png');
	clear: both;
	overflow: hidden;
	padding: 5px;
	width: 804px;
}

#login_status_bar a
{
	text-decoration: none;
}

#login_status_bar a:hover
{
	text-decoration: underline;
}

#login_status_bar ul
{
	list-style-type: none;
	padding: 0px;
	margin: 0px;
}

#login_status_bar li
{
	float: left;
	margin-left: 30px;
	padding: 0px;
	text-align: center;
}

#login_status_bar .buttons
{
	float: right;
}

#important_popup
{
	position: absolute;
	width: 638px;
}

.avatar
{
	border: none;
	width: 75px;
	height: 100px;
	border: 1px solid black;
}

#bubblecontainer
{
		cursor: pointer;
		position: absolute;
		z-index: 100;
		top: 207px;
		left: 210px;
}	

.hot_messages
{
	margin-left: 15px;
	border-left: 1px solid #efefef;
	height: 60px;
}

.hot_messages div
{
	color: #8c8c8c;
	padding-left: 2px;
	height: 19px;
	line-height: 19px;
	font-size: 11px;
	border-bottom: 1px solid #efefef;
}

.hot_messages div a
{
	text-decoration: none;
}

#steve
{
	float: right;
	cursor: pointer;
}