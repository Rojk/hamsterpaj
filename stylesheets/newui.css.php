<?php
	header('Content-type: text/css');

	define('BORDER_COLOR_DARK', '#565656');
	define('BORDER_COLOR_LIGHT', '#a3a7ab');
	define('LIGHT_BLUE', '#e0e7ea');
?>
body
{
	background: url('http://images.hamsterpaj.net/radio/omg.png');
	margin: 0px;
	font-family: arial, verdana, "ms trebuchet";
	font-size: 12px;
	padding: 10px;
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

#main
{
	//background: white;
	width: 986px;
	margin-top: 5px;
}

#top
{
	margin-top: 20px;
}

#top_left
{
	float: left;
	width: 284px;
	height: 75px;
	background: <?=BORDER_COLOR_LIGHT?>;
}

#top_right
{
	float: left;
	width: 702px;
	height: 75px;
	background: <?=BORDER_COLOR_LIGHT?>;
}

#menu_main
{
	z-index: -1;
	height: 23px;
	width: 992px;
}

.menu_cont {
	float: left;
	margin-right: 3px;
	line-height: 23px;
	background: url(http://www.hamsterpaj.net/heggan/images/menu_fade.png) repeat-x;
}
.menu_top { 
	float: left; 
	padding-right: 10px;
	background: url(http://www.hamsterpaj.net/heggan/images/tr.png) no-repeat top right; 
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


#menu_main .active a
{
	color: black;
}

#menu_sub
{
	margin-top: 1px;
	height: 23px;
	border-top: 1px solid #76777b;
	border-left: 1px solid #76777b;
	//border-bottom: 1px solid #76777b;
	background: url(http://www.hamsterpaj.net/heggan/images/menu.png);
	font-weight: normal;
	line-height: 23px;
	font-size: 10px;
}

#menu_sub a
{
	border-right: 2px solid #000000;
	padding-left: 8px;
	padding-right: 8px;
	text-decoration: none;

}

.submenu_top { 
	width: 978px;
	border-bottom: 1px solid #76777b;
	padding-right: 10px;
	background: url(http://www.hamsterpaj.net/heggan/images/menu_side.png) no-repeat top right; 
}



#site_frame
{
	z-index: 2;
	clear: both;
	background: green;
}



#right_now
{
	margin-top: 5px;
	width: 983px;
	height: 29px;
	line-height: 29px;
	padding-left: 3px;
	border: 1px solid <?=BORDER_COLOR_LIGHT?>;
	background: url('http://www.hamsterpaj.net/heggan/images/hotbar.png');
	color: black;
	float: left;
}

#right_now a
{
	color: black;
}

#main_left
{
	width: 165px;
	padding: 2px;
	float: left;
	clear: left;
}

.module
{
	text-align: left;
	//background: url('http://images.hamsterpaj.net/ui/left_module_bg.png') repeat-x #fdefe1;
	padding: 2px;
	margin: 3px;
	margin-bottom: 5px;
	border: 1px solid #b4b4b4;
}

.module_h1
{
	text-align: left;
	margin: 0px;
	padding-left: 7px;
	font-size: 15px;
	color: black;
	font-weight: bold;
	margin-bottom: 5px;
	border: 1px solid #b4b4b4;
	border-top: none;
	background: url('http://www.hamsterpaj.net/heggan/images/module_h1.png');
}

.module_content
{
	text-align: left;
	border: 2px solid #ffffff;
	padding: 7px;
	background: url('http://www.hamsterpaj.net/heggan/images/module_bg.png');
}

#content
{
	background: white;
	padding: 3px;
	width: 635px;

	float: left;
}

#main_right
{
	width: 165px;
	float: right;
	text-align: right;
}

#footer
{
	clear: both;
	height: 32px;
	background: url('http://images.hamsterpaj.net/webcows_ad/webcows_bottom_fade.png');
	border-top: 1px solid <?=BORDER_COLOR_LIGHT?>;
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
		top: 75px;
		left: 210px;
}	

