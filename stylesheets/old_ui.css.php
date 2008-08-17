<?php
	header('Content-type: text/css');

	define('BORDER_COLOR_DARK', '#565656');
	define('BORDER_COLOR_LIGHT', '#a3a7ab');
	define('LIGHT_BLUE', '#e0e7ea');
?>
body
{
	background: #6391b3;
	margin: 0px;
	font-family: verdana, arial, "ms trebuchet", sans-serif;
	color: #454545;
	font-size: 11px;
	padding: 10px;
	padding-top: 0px;
	overflow-x: hidden;
}


a
{
	color: black;
	text-decoration: none;
	border-bottom: 1px dotted #565656;
	_border-bottom: 1px solid #ababab;
}

a p, a h1, a h2, a h3, a h4, a h5, a h6, a img, img
{
	text-decoration: none;
	border: none;
}

h1, h2, h3, h4
{
	font-weight: normal;
	margin: 3px;
	margin-left: 0px;
}

h4
{
	font-size: 11px;
	margin-bottom: 1px;
}

h3
{
	font-size: 13px;
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
	width: 815px;
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
	background: url('http://images.hamsterpaj.net/login_bar/top_background.png');
	border-top: 1px solid #565656;
	height: 65px;
}

#logo
{
	background: url('http://images.hamsterpaj.net/logo.png') no-repeat;
	margin-right: 10px;
	float: left;
}



/* Old main menu CSS

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
	color: #3e3e3e;
	border-bottom: none;
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
	margin-right: 6px;
	margin-top: 3px;
	padding-left: 3px;
	padding-right: 3px;
	background: url('http://images.hamsterpaj.net/ui/menu_fade.png') repeat-x #fdaf26;
	line-height: 17px;
	_line-height: 18px;
}

#menu_main .active
{
	height: 18px;
	margin-bottom: -1px;
	background: white url('http://images.hamsterpaj.net/ui/active_tab_bg.png');
	border-bottom: 1px solid #e4d6cb;
}

#menu_main .active a
{
	color: black;
}
*/

#menu_main
{
	height: 21px;
	width: 795px;
	font-size: 12px;
	font-family: "ms trebuchet", verdana, arial;
	margin-top: 3px;
	_margin-bottom: -1px;
}

#menu_main div
{
	float: left;
	margin-right: 2px;
	margin-top: 3px;
	padding: 0px;
	padding-left: 5px;
	background: #fcaf38 url('http://images.hamsterpaj.net/ui/main_menu_sprite.png') no-repeat scroll 0px -90px;
	padding-top: 2px;
	padding-bottom: 2px;
}

#menu_main a
{
	margin: 0px;
	margin-top: -1px;
	background: #fcaf38 url('http://images.hamsterpaj.net/ui/main_menu_sprite.png') no-repeat scroll 100% -90px;
	padding-right: 10px;
	border-bottom: none;
	padding-top: 2px;
	padding-bottom: 2px;
}

#menu_main .active
{
	background: #fcaf38 url('http://images.hamsterpaj.net/ui/main_menu_sprite.png') no-repeat scroll 0px 0px;
}

#menu_main .active a
{
	background: #fcaf38 url('http://images.hamsterpaj.net/ui/main_menu_sprite.png') no-repeat scroll 100% -0px;
}

#logo
{
	width: 320px;
	float: left;
	height: 60px;
	padding: 0px;
	margin: 0px;
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
	border-bottom: none;
	height: 18px;
	margin-top: 8px;
	width: auto;
}

#content_menu div
{
	float: left;
	background: #fcaf38 url('http://images.hamsterpaj.net/ui/sub_menu_sprite.png') no-repeat scroll 0px -90px;
	margin-right: 1px;
	padding-left: 6px;
	height: 15px;
	padding-top: 3px;
}

#content_menu a
{
	border: none;
	background: #fcaf38 url('http://images.hamsterpaj.net/ui/sub_menu_sprite.png') no-repeat scroll 100% -90px;
	padding-right: 8px;
	font-size: 12px;
	padding-top: 3px;
}

#content_menu .active
{
	background: #fcaf38 url('http://images.hamsterpaj.net/ui/sub_menu_sprite.png') no-repeat scroll 0px 0px;
	border-bottom: 1px solid white;
	margin-bottom: -1px;
}

#content_menu .active a
{
	background: #fcaf38 url('http://images.hamsterpaj.net/ui/sub_menu_sprite.png') no-repeat scroll 100% 0px;
}

/*

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
	background: url('http://images.hamsterpaj.net/ui/submenu_bg.png') repeat-x #fdaf26;
	line-height: 17px;
	color: white;
}

#content_menu a
{
	text-decoration: none;
	color: #3e3e3e;
	border-bottom: none;
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

#content_menu
{
	background: #fbce8a;
	border: 1px solid #565656;
	border-bottom: none;
	height: 25px;
	width: auto;
}

#content_menu div
{
	background: white;
	border: none;
	border-right: 1px solid #ababab;
	background: #f9cc88;
	height: 100%;
	margin: 0px;
	line-height: 25px;
	padding: 0px;
	padding-left: 8px;
	padding-right: 8px;
}

#content_menu .active
{
	background: white;
	margin-bottom: -1px;
	border-bottom: 1px solid white;
	height: 100%;
}

*/



#middle
{
	float: left;
	width: 646px;
	margin-left: 5px;
	overflow-x: hidden;
}

#content
{
	background: white;
	border: 1px solid #565656;
	padding: 3px;
	padding-top: 8px;
	margin-bottom: 150px;
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

#steve
{
	float: right;
	cursor: pointer;
}

#login_pane
{
	padding-top: 10px;
	padding-left: 32px;
	width: 445px;
	float: left;
}

#login_pane ul
{
	margin: 0px;
	padding: 0px;
}

#login_pane ul li
{
	cursor: pointer;
	font-size: 11px;
	color: #9a9a9a;
	width: 63px;
	text-align: center;
	list-style-type: none;
	margin: 2px;
	padding: 0px;
	float: left;
}

#login_pane ul .settings
{
	margin-left: 15px;
}

#login_pane ul li a
{
	text-decoration: none;
	color: #9a9a9a;
}

#login_pane ul li strong
{
	font-weight: normal;
	color: black;
}

#login_pane .logged_in
{
	margin: 0px;
	padding: 0px;
	padding-left: 2px;
	padding-bottom: 1px;
	font-size: 12px;
}

#login_pane .register_hint
{
	margin-top: 2px;
	margin-left: -2px;
	padding-left: 2px;
	padding-top: 2px;
	clear: both;
}

#login_pane form
{
	height: 38px;
}

#login_pane .button
{
	margin: 0px;
	padding: 0px;
}

#login_pane .icon
{
	cursor: pointer;
	width: 25px;
	height: 22px;
	padding: 2px;
	padding-left: 3px;
	text-align: center;
	background: url('http://images.hamsterpaj.net/login_bar/icon_bg.png');
	margin: 2px auto auto;
}

#login_pane ul li img
{
	height: 20px;
}

#login_pane h5
{
	margin: 0px;
	padding: 0px;
}

#login_pane .username
{
	width: 120px;
}

#login_pane .password
{
	width: 120px;
}

#login_pane input
{
	width: 110px;
	margin-top: 2px;
}

#login_pane .username, #login_pane .password
{
	float: left;
}

#login_pane .login_buttons
{
	float: left;
	height: 38px;
}

#login_pane .login_buttons li
{
	width: 90px;
}

.user_avatar
{
	display: block;
	cursor: pointer;
}


#ie6_warning
{
	border: 1px solid #565656;
	background: #eeead4;
	margin: 5px;
	padding: 5px;
}

#ie6_warning img
{
	float: right;
	cursor: pointer;
}

#ie6_warning p
{
	margin: 0px;
	padding: 0px;
}

#sms_tip
{
	border: 1px solid #565656;
	margin: 5px;
	background: #ffcd6e;
	padding: 10px;
}
