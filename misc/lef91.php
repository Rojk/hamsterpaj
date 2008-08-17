<?php			
include('/storage/www/standard.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	require(PATHS_INCLUDE . 'libraries/photos.lib.php');
		$ui_options['javascripts'][] = 'lef91.js';
	
	ui_top($ui_options);
?>

<div id="photo_content" style="display: none;">
<ul id="thumbnail">
	<li><a href="http://images.hamsterpaj.net/photos/full/15/76505.jpg" id="1"><img src="http://images.hamsterpaj.net/photos/mini/15/76538.jpg" title="hej" /></a></li>
	<li><a href="http://images.hamsterpaj.net/photos/full/15/76545.jpg" id="2"><img src="http://images.hamsterpaj.net/photos/mini/15/76538.jpg" title="dummer" /></a></li>
	<li><a href="http://images.hamsterpaj.net/photos/full/15/76538.jpg" id="3"><img src="http://images.hamsterpaj.net/photos/mini/15/76538.jpg" title="kossa" /></a></li>
</ul>

<div id="large">
	<h2 innerhtml="???,??">???,??</h2>
	<img alt="image01.jpg" id="1" title="???,??" src="http://images.hamsterpaj.net/photos/full/15/76505.jpg" style="overflow: visible; display: block; opacity: 0.9999;"/>
</div>
</div>
<?php
	echo $output;
	ui_bottom();
?>