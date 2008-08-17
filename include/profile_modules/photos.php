<?php
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px; height: 440px;">' . "\n";
?>
<a name="photoalbum"></a>
<script>
<?php
	echo 'var photoalbum_base_url = "http://www.hamsterpaj.net/traffa/profile.php?id=' . $userid . '";' . "\n";
?>
</script>

<div style="line-height: 20px;">
<?php
	$query = 'SELECT title, photos FROM photo_albums WHERE owner = "' . $userid . '" ORDER BY position LIMIT 8';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	for($row = 0; $data = mysql_fetch_assoc($result); $row++)
	{
		if($row == 0)
		{
			$display_album = $data['photos'];
			$display_image = substr($data['photos'], 0, strpos($data['photos'], ','));
		}
		$jscript_array = (substr_count($data['photos'], ',') == 0 && strlen($data['photos']) > 0) ? $data['photos'] . ', \'false\'' : $data['photos'];
		$data['title'] = htmlspecialchars($data['title']);
		echo '<a href="javascript: void(0);" onclick="display_album(Array(' . $jscript_array . '));" style="margin-left: 10px;">' . $data['title'] . '</a>' . "\n";
		if($row != 7)
		{
			echo '<span style="margin-left: 10px;">|</span>' . "\n";
		}
	}

	echo '</div>' . "\n";

	echo '<div style="width: 100%; overflow: auto; height: 59px; background: ' . $profile_colors['dark'] . '; border-top: 1px solid ' . $profile_colors['border'] . '; text-align: center;">' . "\n";
	echo '<div style="width: auto;" id="photoalbum_thumb_scroll">' . "\n";

	echo '</div>' . "\n";
	echo '</div>' . "\n";

	echo '<div style="margin-top: 5px; padding: 3px;">' . "\n";
	echo '<script>display_album(Array(' . $display_album . ', \'false\'));</script>' . "\n";

	echo '<div style="text-align: center; vertical-align: middle; float: left; width: 400px; height: 300px;">' . "\n";
	echo '<img src="" id="photo_big" style="border: 1px solid ' . $profile_colors['border'] . '; cursor: pointer; z-index: 100;" onclick="photoalbum_resize_full();" />' . "\n";
	if($_SESSION['login']['userlevel'] >= USERLEVELS_DELETE_PHOTO)
	{
		echo '<a id="delete_photo_link" href="#12">Ta bort fotot (Jihad!)</a>';
	}
?>
<br />
<div style="border: 1px solid #ababab; background: white; font-size: 9px; clear: both;" id="photoalbum_direct_link">Datorpappa</div>

</div>

<div style="height: 290px; width: 205px; float: right; padding: 5px;">

<div id="photo_description" style="width: 195px; height: 66px; overflow: auto;">
</div>

<div id="photo_comments" style="width: 195px; height: 168px; overflow: auto; background: white; border: 1px solid <?php echo $profile_colors['border']; ?>">
</div>

<div id="photo_comment_form" style="width: 195px; height: 50px; margin-top: 3px;">
<form action="/photoalbum/iframe.php?action=comment" method="post" target="photoalbum_iframe">
<textarea name="text" style="float: left; height: 45px; width: 123px; border: 1px solid black; margin-right: 3px;" id="photo_comment_textarea"<?php if($_SESSION['login']['id'] < 1){ echo ' disabled="true"'; } ?>></textarea>
<input type="hidden" name="photo_id" id="photo_comment_id" />
<input type="submit" value="Skicka!" id="photo_comment_submit" style="line-height: 45px; height: 47px; margin-top: 1px; border: 1px solid black; background: url('http://images.hamsterpaj.net/images/profiles/photoalbum_submit_bg.png');" />
</form><br />
<span style="font-size: 10px;">Detta är ingen gästbok, klicka <a href="/traffa/guestbook.php?view=<?= $userid ?>">här</a> för att gå till <?= $userinfo['login']['username'] ?>s gästbok.</span>
</div>

<iframe src="/photo_comments.php" id="photoalbum_iframe" name="photoalbum_iframe" style="display: none;" frameborder="0"/></iframe>
<?php

	if(isset($_GET['photo_id']) && is_numeric($_GET['photo_id']))
	{
		$display_image = $_GET['photo_id'];
	}

 	if($display_image > 0)
	{
		echo '<script>photoalbum_display_image(' . $display_image . ');</script>';
	}
	else
	{
		echo '<script>document.getElementById("photo_big").src = "/images/photoalbum_no_image_full.png";</script>';
		echo '<script>document.getElementById("photo_comment_submit").disabled = true;</script>';
	}
?>
</div>
<br /><br/>
</div>
</div>
