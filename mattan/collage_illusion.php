<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('mattan', 'ditt_namn');
	$ui_options['stylesheets'][] = 'collage_illusion.css';
	$templates = array('wood_standing', 'wood_laying', 'brick_standing', 'brick_laying', 'cork_standing', 'cork_laying', 'whiteboard_standing', 'whiteboard_laying');

	$heading = '<h1>Kollageapparaten på Hamsterpaj</h1>' . "\n";


	$form = '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">' . "\n";
	$form .= '<label>Välj en bild att ladda upp</label><br />' . "\n";
	$form .= '<input type="file" name="photo" />' . "\n";	
	$form .= '<br /><label>Välj en stil</label><br />' . "\n";
	$form .= '<ol id="collage_illusion_thumbs">' . "\n";
	foreach($templates AS $bg)
	{
		$form .= '<li>' . "\n";
		$checked = ($bg == 'wood') ? ' checked="checked"' : '';
		$form .= '<img src="http://images.hamsterpaj.net/collage_collision/thumbs/' . $bg . '.jpg" />';
		$form .= '<input type="radio" name="template" value="' . $bg . '"' . $checked . ' />';
		$form .= '</li>' . "\n";
	}
	$form .= '</ol>' . "\n";
	$form .= '<br style="clear: both;" />' . "\n";
	$form .= '<input type="submit" value="OK" />' . "\n";
	$form .= '</form>' . "\n";	
	
	if(isset($_FILES['photo']))
	{
		$template = (in_array($_POST['template'], $templates)) ? $_POST['template'] : 'wood_standing';	
		
		$work_dir = '/mnt/images/collage_illusion/';
		$filename = time() . rand(1000, 9999) . '.jpg';
		
		$bg = '/mnt/images/collage_illusion/templates/' . $template . '.png';
		$photo = $_FILES['photo']['tmp_name'];
		$outfile = '/mnt/images/collage_illusion/outfiles/' . $filename;
		$size = '"640x640>"';
		
		$tmp_photo = $work_dir . 'tmp/' . rand(0, 99999999) . '.jpg';
		$photo_resize = 'convert -resize ' . $size . ' ' . $photo . ' ' . $tmp_photo;
		system($photo_resize);

		$size = getimagesize($tmp_photo);
		$size = $size[0] . 'x' . $size[1] . '!';
		$tmp_bg = $work_dir . 'tmp/' . rand(0, 99999999) . '.png';
		$bg_resize = 'convert -resize ' . $size . ' ' . $bg . ' ' . $tmp_bg;				
		system($bg_resize);
		
		$composite = 'composite ' . $tmp_bg . ' ' . $tmp_photo . ' ' . $outfile;
		system($composite);
		
		unlink($tmp_bg);
		unlink($tmp_photo);

		$image .= '<img src="http://images.hamsterpaj.net/collage_illusion/outfiles/' . $filename . '" />' . "\n";
		$image .= '<p>För att ladda ner bilden till din dator, högerklicka på den och välj "Spara bild som"</p>' . "\n";
		$image .= '<p>Bilden finns kvar på Hamsterpajs servrar i 24 timmar, därefter raderas den</p>' . "\n";
	}


	ui_top($ui_options);
	$out .= $heading;
	$out .= $form;
	$out .= $image;
	echo $out;
	ui_bottom();
?>
