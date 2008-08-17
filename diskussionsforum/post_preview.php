<?php
	require('../include/core/common.php');

	echo '<div class="forum_post_container">' . "\n";
	echo '<div class="forum_post_top"></div>' . "\n";
	echo '	<div class="forum_post">' . "\n";
	echo '		<div class="author">' . "\n";
	echo '			<a class="username" href="/traffa/profile.php?id=' . $_SESSION['login']['id'] . '">' . $_SESSION['login']['username'] . '</a>' . "\n";
	echo '			<div class="passepartout">' . "\n";
	if($_SESSION['userinfo']['image'] == 1 || $_SESSION['userinfo']['image'] == 2)
	{
		echo '			<img src="http://images.hamsterpaj.net/images/users/thumb/' . $_SESSION['login']['id'] . '.jpg" class="user_avatar" />' . "\n";
	}
	else
	{
		echo '?';
	}
	echo '		</div>' . "\n";
	echo '	</div>' . "\n";
	echo '	<div class="post_info">' . "\n";
	echo '		<span class="post_timestamp">' . fix_time(time()) . '</span>' . "\n";
	echo '	</div>' . "\n";
	echo '	<div class="post_content">' . "\n";
	echo utf8_encode(discussion_forum_parse_output($_POST['content']));
	echo '	</div>' . "\n";
	echo '	<div class="controls">' . "\n";
	echo '	</div>' . "\n";
	echo '</div>' . "\n";
	echo '<div class="forum_post_bottom"> </div>' . "\n";
	echo '</div>' . "\n";
?>