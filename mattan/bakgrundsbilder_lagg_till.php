<?php

	require('../include/core/common.php');
	require(PATHS_INCLUDE.'libraries/wallpaper.lib.php');
	
	$ui_options['title'] = 'Lägg till dina bakgrundsbilder på Hamsterpaj';
	$ui_options['menu_path'] = array('mattan', 'bakgrundsbilder');
	$ui_options['stylesheets'][] = 'wallpapers.css';
	$ui_options['stylesheets'][] = 'wallpapers_admin.css';
	
	$ui_options['javascripts'][] = 'jquery_ajaxqueue.js';
	$ui_options['javascripts'][] = 'ajaxfileupload.js';
	$ui_options['javascripts'][] = 'upload_background_images.js'; //this is the shit!


	ui_top($ui_options);

	if(!isset($_SESSION['userinfo']['wallpapers_ban']))
	{
		$query = 'SELECT wallpapers_ban FROM userinfo WHERE userid = '.$_SESSION['login']['id'].' LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$data = mysql_fetch_assoc($result);
		$_SESSION['userinfo']['wallpapers_ban'] = $data['wallpapers_ban'];
	}
	if(login_checklogin() && $_SESSION['userinfo']['wallpapers_ban'] > time())
	{
		echo '<h1>Fel</h1>'."\n";
		echo '<h2>Du får inte ladda upp fler bilder på ett tag! Läs i din gästbok för mer info.</h2>';
		ui_bottom();
		exit;
	}
	
?>
<h2>Lägg till ny bakgrundsbild</h2>
<p style="color:red;">Alla fält är obligatoriska</p>
<form action="#" id="wallpapers_form" enctype="multipart/form-data" method="post">
<h5>Namn</h5>
<input type="text" class="textbox" id="form_title" name="title" />
<h5>Kategori</h5>
<select name="cat">
<?php
	echo get_cats(0);
?>
</select>
<h5>Licens</h5>
<select name="license">
<?php
	$query = 'SELECT id, title FROM '.WALLPAPERS_LICENSE.' WHERE is_removed = 0 ORDER BY id ASC';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	if(mysql_num_rows($result) > 0)
	{
		while($data = mysql_fetch_assoc($result))
		{
			echo '<option value="'.$data['id'].'">'.$data['title'].'</option>'."\n";
		}
	}
?>
</select>
<h5>Upphovsrättsinnehavare</h5>
<select name="author">
<?php
	$query = 'SELECT id, title FROM '.WALLPAPERS_AUTHORS.' WHERE is_removed = 0 ORDER BY id ASC';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	if(mysql_num_rows($result) > 0)
	{
		while($data = mysql_fetch_assoc($result))
		{
			echo '<option value="'.$data['id'].'">'.$data['title'].'</option>'."\n";
		}
	}
?>
</select>
<h5>Tags (kommaseparerade)</h5>
<input type="text" name="tags" id="form_tags" class="textbox" />

<h5>Ladda upp bild (jpg, gif, png, bmp)</h5>
<fieldset>
	<legend>Instruktioner</legend>
	<ol>
		<li>Fyll i formuläret</li>
		<li>Välj bild att ladda upp, se till så att den inte redan finns med i arkivet</li>
		<li>Klicka på "Ladda upp bild" bredvid</li>
		<li>Välj vilka andra upplösningar du vill ska skapas</li>
		<li>Klicka på "Lägg till"</li>
		<li>Klart!</li>
	</ol>
	Efter du har laddat upp bilden kommer <strong>någon att validera din bild</strong>. Det är därför den inte syns direkt i arkivet.
</fieldset>
<br />
	<!-- MAX_FILE_SIZE must precede the file input field. Here 41943040 = 40MB -->
    <input type="hidden" name="MAX_FILE_SIZE" value="41943040" />
    <input name="uploaded_image" id="uploaded_image" type="file" />
    <input type="button" id="upload_image_button" class="button" value="Ladda upp bild" />
    <br />
    <div id="sizes">
    	<span>Storlekarna visas efter du har laddat upp bilden.</span>
		<ul>
		</ul>
	</div>
    <br />
	<br />
	<input type="submit" value="Lägg till" class="button" />
	<div id="result">
		<ul>
		</ul>
		<a id="images_link" href="/mattan/bakgrundsbilder.php" title="Gå till bakgrundsbilderna">Gå till bakgrundsbilderna</a>
	</div>
</form>

<?php
	ui_bottom();
?>