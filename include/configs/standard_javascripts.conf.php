<?php
//JavaScript merger:
$js_compress_important_files = array(
	'ui_server_message.js',
	'scripts.js',
	'steve.js',
	'new_guestbook.js',
	'forum.js',
	'posts.js',
	'abuse_report.js',
	'poll.js',
	'swfobject.js',
	'md5.js',
	'xmlhttp_login.js',
	'xmlhttp.js',
	'fult_dhml-skit_som_faar_bilder_att_flyga.js',
	'wave_effect.js',
	'joels_hackerkod.js',
	'ui.js',
	'ui_modules.js',
	'ui_business_card.js',
	'ui_multisearch.js',
	'tiny_reg_form.js',
	'stay_online.js'
);
array_unshift($js_compress_important_files, 'jquery.js', 'womlib.js', 'jquery.dimensions.js', 'jquery-ui.js', 'synchronize.js');
$js_compress_important_files = array_unique($js_compress_important_files);
?>