<?php
	header('Content-type: text/css');

	define('BORDER_COLOR_DARK', '#565656');
	define('BORDER_COLOR_LIGHT', '#a3a7ab');
	define('LIGHT_BLUE', '#e0e7ea');
?>
body
{
	background: black;
	margin: 0px;
	font-family: arial, verdana, "ms trebuchet";
	font-size: 12px;
	padding: 10px;
	padding-top: 0px;
	overflow-x: hidden;
}


a p, a h1, a h2, a h3, a h4, a h5, a h6
{
	text-decoration: none;
}

a
{
	color: black;
}

img
{
	border: none;
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
	background: #e66268;
	margin: 2px;
	padding-left: 3px;
	padding-right: 3px;
	text-indent: 0px;
}

.button_small
{
	background: url('/images/ui/button_fade.png');
	margin: 1px;
	padding: 0px;
	font-size: 9px;
}


#bigbanner
{
	height: 120px;
	_margin-bottom: -3px;
}

#site_container
{
	width: 806px;
	float: left;
}

#main
{
	background: white;
	width: 799px;
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
	height: 21px;
	width: 795px;
	font-size: 12px;
	font-weight: bold;
	font-family: "ms trebuchet", verdana, arial;
	margin-top: 3px;
}

#menu_main a
{
	text-decoration: none;
	color: white;
}

#menu_main a:hover
{
	color: white;
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
	background: #a20003;
	line-height: 17px;
	_line-height: 18px;
	color: white;
}

#menu_main .active
{
	height: 18px;
	margin-bottom: -1px;
	background: white;
	border-bottom: 1px solid white;
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
	width: 796px;
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
	width: 150px;
	float: left;
	clear: left;
}

/* Side modules */

.module_container
{
	background: #fddfb4;
	border-bottom: 1px solid #565656;
}

.module_container img
{
	display: block;
	margin-bottom: -1px;
}

.module
{
	text-align: left;
	background: url('http://images.hamsterpaj.net/emoland_module_bg.png') repeat-x #f9e1e2;
	padding: 2px;
	margin-top: 5px;
	border: 1px solid #565656;
	border-bottom: none;
	overflow: hidden;
}

.module h1
{
	margin: 0px;
	padding: 0px;
	font-size: 12px;
	text-align: center;
	color: #565656;
	font-weight: normal;
	margin-bottom: 5px;
}

.module h3
{
	cursor: pointer;
	margin: 0px;
	padding: 0px;
	text-align: center;
	font-size: 12px;
	color: #565656;
	font-weight: normal;
	margin-bottom: 5px;
}

.module h4
{
	font-weight: bold;
}

.module .old_event
{
	border-bottom: 1px solid #ababab;
	margin-left: -2px;
	font-size: 10px;
	margin-right: -2px;
	color: #565656;
	padding: 3px;
	padding-left: 5px;
}

.module .old_event a
{
	color: #565656;
	font-weight: bold;
	text-decoration: none;
}

.module .new_event
{
	border-bottom: 1px solid #ababab;
	background: white;
	margin-left: -2px;
	margin-right: -2px;
	color: #565656;
	padding: 5px;
}

.module .new_event a
{
	color: #565656;
	font-weight: bold;
	text-decoration: none;
}


#main_right
{
	overflow: hidden;
	width: 190px;
	float: left;
}

#skyscraper
{
	padding-left: 14px;
	_margin-top: -5px;
}

#right_modules
{
	padding-top: 19px;
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
	border-bottom: none;
	padding-left: 5px;
	padding-right: 5px;
	margin-right: 6px;
	background: url('http://images.hamsterpaj.net/emoland_submenu_bg.png') repeat-x #fdaf26;
	line-height: 17px;
	color: white;
}

#content_menu a
{
	text-decoration: none;
	color: black;
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
	background: url('http://images.hamsterpaj.net/emoland_hotbar_bg.png');
	clear: both;
	overflow: hidden;
	padding: 5px;
	width: 789px;
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