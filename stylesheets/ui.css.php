<?php
	//require_once('/storage/www/www.hamsterpaj.net/data/include/constants.php');
	header('Content-type: text/css');

	define('BORDER_COLOR_DARK', '#565656');
	define('BORDER_COLOR_LIGHT', '#a3a7ab');
	define('LIGHT_BLUE', '#e0e7ea');
	
	/* NATTPAJ-konstanten flyttad till constants.php */
	include("/storage/www/www.hamsterpaj.net/data/include/constants.php");
	
	function nattpaj_css($css){ 
		echo (NATTPAJ == true) ? $css : ''; 
	}
?>
body
{
	/*background: <?php echo AFOOL08 ? '#000000' : '#6391b3'; ?>;
	<?php echo AFOOL08 ? 'opacity: .20;' : ''; ?>
	
	<?php echo AFOOL08 ? 'filter: alpha(opacity=20);' : ''; ?>*/
	background: #6391b3 <?php nattpaj_css('url(http://images.hamsterpaj.net/nattpaj/nattpaj_bg.png)'); ?>;
	margin: 0px;
	font-family: verdana, arial, "ms trebuchet", sans-serif;
	color: #454545;
	font-size: 11px;
	padding: 10px;
	padding-top: 0px;
	overflow-x: hidden;
	/*<?php echo AFOOL08 ? '1' : '0'; ?>*/

}
a
{
	color: black;
	text-decoration: none;
}

p a
{
	border-bottom: 1px dotted #565656;
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
	font-size: 25px;
	font-family: Georgia,serif;
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
	margin: 2px;
	padding-left: 3px;
	padding-right: 3px;
	text-indent: 0px;
}

.button_small
{
	margin: 1px;
	padding: 0px;
	font-size: 9px;
}

#site_top_rounded_corners
{
	display: block;
	/*margin-top: 10px;*/
}


#bigbanner
{
	height: 120px;
	_margin-bottom: -3px;
}

#site_container
{
	width: 795px;
	padding: 1px;
	float: left;
	background: white url('http://images.hamsterpaj.net/ui/content_container_bg.png');
	margin-right: 5px;
	margin-top: 0px;
}

#top
{
	height: 57px;
	<?php nattpaj_css('background: url(http://images.hamsterpaj.net/nattpaj/nattpaj_top_bg.png); color: #fff !important;'); ?>
}

#logo
{
	display: block;
	float: left;
	margin: 3px 10px 0 5px;
}

#top form
{
	margin: 0px;
	padding: 0px;
}

/* Menu starts here */

#main_left
{
	width: 150px;
	float: left;
	clear: left;
	padding-top: 5px;
	background: white;
	overflow: hidden;
}


#main_left .module_container_open, #main_left .module_container_closed 
{
	display: none;
}

#main_left .menu, #main_left .menu_active
{
	background: #fef5e2 url('http://images.hamsterpaj.net/ui/menu/menu_box_background.png');
	margin-bottom: 5px;
	margin-left: 5px;
	margin-right: 3px;
}

#main_left a, #main_left a:active
{
	outline: none;
}

#main_left div h3
{
	background: url('http://images.hamsterpaj.net/ui/menu/menu_h3_background.png');
	padding: 1px;
	padding-left: 3px;
	margin: 0px;
	display: block;
	width: auto;
}


#main_left div h3 a
{
	display: block;
	margin: 0px;
}

#main_left .menu .menu_box_open_bottom, #main_left .menu_active .menu_box_closed_bottom
{
	display: none;
}

#main_left .menu_active .menu_box_open_bottom
{
	display: block;
}

#main_left .menu_active h3
{
	border-bottom: 1px solid #c5c2bc;
}

#content_menu
{
	display: none;
}

#main_left .menu
{
	_overflow: hidden;
	padding: 0px;
}

#main_left .menu img
{
	display: block;
	margin: 0px;
	padding: 0px;
}

#main_left .menu .menu_content
{
	display: none;
	padding: 0px;
	height: 0px;
}

#main_left .menu ul, #main_left .menu_active ul
{
	padding: 0px;
	margin: 0px;
}

#main_left .menu ul ul, #main_left .menu_active ul ul
{
	font-size: 10px;
	margin-bottom: 8px;
}



#main_left .menu_active .menu_content
{
	display: block;
}

#main_left h4
{
	font-weight: bold;
	margin-left: 5px;
}

#main_left .menu ul li, #main_left .menu_active ul li
{
	list-style-type: none;
	padding: 0px;
	margin: 0px;
	padding-top: 2px;
	padding-bottom: 2px;
	padding-left: 10px;
}

#main_left .menu a, #main_left .menu_active a
{
	border-bottom: none;
}

#main_left .menu ul .active, #main_left .menu_active ul .active
{
	background: white;
	border: 1px solid #dfd9cd;
	border-right: 1px solid white;
	margin-right: -1px;
	margin-left: 6px;
	padding-left: 4px;
	margin-top: 2px;
	margin-bottom: 2px
}

#quicksearch
{
	padding-left: 5px;
	padding-right: 0px;
	margin: 0;
}

#quicksearch form
{
	margin: 0px;
	padding: 0px;
}

#quicksearch h5
{
	padding: 0px;
	margin: 0px;
	font-size: 11px;
}

#quicksearch .quicksearch_input
{
	width: 115px;
	display: block;
	float: left;
	border: 1px solid #ababab;
	margin: 0px;
	margin-top: 1px;
	margin-right: 4px;
	color: #757575;
}

#main_left .left_module
{
	margin-left: 5px;
	margin-right: 3px;
	border-right: 1px solid #dfdfdf;
	border-left: 1px solid #dfdfdf;
	padding: 2px;
}

#main_left .left_module #note 
{
	width: 132px;
	height: 160px;
}

#main_left .left_module_bottom
{
	display: block;
	margin-bottom: 3px;
	margin-left: 3px;
}
#main_left .left_module_top
{
	margin-left: 3px;
	display: block;
	margin-top: 3px;
}

/* Menu ends here */

#main_right
{
	overflow: hidden;
	width: 197px;
	float: left;
}

#skyscraper
{
	padding-left: 14px;
	_margin-top: -5px;
}


.right_module_container
{
	background: url('http://images.hamsterpaj.net/right_modules/background.png');

}

.right_module_container h3
{
	background: orange url('http://images.hamsterpaj.net/right_modules/top.png');
	padding-top: 4px;
	padding-left: 6px;
	padding-bottom: 2px;
	margin: 0px;
	cursor: pointer;
}

.right_module
{
	padding: 4px;
	width: 185px;
	background: url('http://images.hamsterpaj.net/right_modules/fade_background.png') no-repeat left bottom;
}

#right_modules
{
	padding-top: 19px;
}

#middle
{
	float: left;
	width: 644px;
	overflow-x: hidden;
	margin-bottom: 15px;
}

#content
{
	background: white;
	padding: 3px;
	padding-top: 0px;
	margin-bottom: 10px;
	overflow-x: hidden;
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
	display: block;
}

#login_pane
{
	padding-top: 5px;
	padding-left: 0px;
	width: 435px;
	float: left;
}

#login_pane ul
{
	margin: 0px;
	padding: 0px;
	float: left;
}

#login_pane ul li
{
	cursor: pointer;
	font-size: 11px;
	color: #9a9a9a;
	width: 69px;
	_width: 68px;
	text-align: center;
	list-style-type: none;
	margin: 4px 0px 0 0;
	padding: 0px;
	float: left;
}

#login_pane ul .photocomments
{
	width: 78px;
	margin-top: 0;
}

#login_pane ul li a
{
	text-decoration: none;
	border: none;
	color: #9a9a9a;
}

#login_pane ul li strong
{
	font-weight: normal;
	color: black;
}

#login_pane ul li strong a
{
	font-weight: normal;
	color: black;
}

#login_pane #user_info
{
	float: right;
	width: 145px;
	padding-left: 5px;
}

#login_pane #user_info .username
{
	font-weight: bold;
	display: block;
	border-bottom: none;
}

#login_pane #user_info .online_time
{
	line-height: 20px;
}

#login_pane #user_info .settings
{
	margin-right: 10px;
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
	padding: 2px 2px 2px 3px;
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
	font-weight: normal;
	padding: 0px;
}

#login_pane .username
{
	width: 100px;
}

#login_pane .password
{
	width: 100px;
}

#login_pane input
{
	width: 90px;
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
	width: 85px;
}

#search_and_status
{
	padding-top: 2px;
	height: 24px;
	border-top: 2px solid #e4e5e6;
	border-bottom: 2px solid #e4e5e6;
	background-color: #DCE8F4;
	background: url('http://images.hamsterpaj.net/ui/search_and_status_bg.png');
	width: 795px;
}

#search_and_status #quicksearch
{
	float: left;
	width: 145px;
	margin-right: 10px;
}

#search_and_status .status
{
	float: left;
	width: 600px;
}

#user_status_input
{
	width: 500px;
	color: #757575;
	border: none;
	cursor: pointer;
	background: none;
	padding-top: 3px;
}

#user_status_input:focus
{
	font-style: normal;
	color: black;
	padding-left: 2px;
	cursor: text;
	background: white;
	border: 1px solid #ababab;
}

#user_status_save_button
{
	display: none;
}

.user_avatar
{
	display: block;
	cursor: pointer;
	width: 75px;
	height: 100px;
}

#recent_update_notice a, #recent_update_notice span
{
	margin: 0px;
	padding: 0px;
}

/* Notices */
.notice
{
	border: 1px solid #ababab;
	border-top: none;
	border-right: none;
	padding: 3px;
	background: url('http://images.hamsterpaj.net/ui/notice_bg.png');
	margin: 0px;
}

#steve_gun
{
	float: right;
	margin-right: 5px;
}

/* Internal ads */
.fieldset_ad
{
	margin-top: 0;
	border: 1px solid #91BE8D;
}
#internal_ad
{
	margin-top: 100px;
	background: url('http://images.hamsterpaj.net/internal_ads/internal_ad_bg.png');
	width: 618px;
	height: 105px;
	padding: 10px;
}

#internal_ad a
{
	border: none;
	text-decoration: none;
}

#internal_ad img
{
	float: left;
	margin-top: 6px;
	margin-left: 6px;
	margin-right: 21px;
}

#internal_ad h2
{
	padding: 0px;
	margin: 5px;
}

#internal_ad p
{
	margin-left: 5px;
	margin-right: 5px;
}

#adsense_left
{
	margin-left: 15px;
}

#adsense_left, #adsense_right
{
	margin-top: 15px;
}

.ie_thumbnail_120_90
{
	border: 1px solid #565656;
	width: 120px;
	height: 90px;
}

#module_forum_threads a, #module_forum_posts a, #module_latest_forum_spam a
{
	border-bottom: none;
}

#module_forum_threads ul, #module_forum_posts ul, #module_latest_forum_spam ul
{
	list-style-type: none;
	margin: 0px;
	padding: 3px;
}

#module_forum_threads ul li, #module_forum_posts ul li, #module_latest_forum_spam ul li
{
	margin-top: 2px;
	padding: 2px;
}

#internalad_topmargin
{
	height: 50px;
	clear: both;
}

#eyeDiv
{
	position: relative !important;
}