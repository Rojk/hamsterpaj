<?php
	require('../include/core/common.php');
	require_once(PATHS_INCLUDE . 'libraries/forum.php');
	require_once(PATHS_INCLUDE . 'libraries/posts.php');
	require_once(PATHS_INCLUDE . 'libraries/discussions.php');
	require_once(PATHS_INCLUDE . 'guestbook-functions.php');
	if(login_checklogin())
	{
		if(count($_GET) > 0)
		{
			switch($_GET['action'])
			{
				case 'add_favourite':
					if(isset($_GET['category']))
					{
						forum_favourite_category_add($_GET['category']);
					}
				break;
				case 'remove_favourite':
					if(isset($_GET['category']))
					{
						forum_favourite_category_remove($_GET['category']);
					}
				break;
				case 'post_edit':
					if(isset($_GET['post_id']))
					{
						$posts = posts_fetch(array('post_id' => $_GET['post_id']));
						$post = array_pop($posts);
						if( $post['author'] != $_SESSION['login']['id'])
						{
							echo '<h1>Hörru du din tjyv! Låt bli andras inlägg!</h1>' . "\n";
							exit;
						}
						echo '<style type="text/css">
							@import url(\'/stylesheets/ui.css.johan.php\');
							@import url(\'/stylesheets/shared.css\');
							@import url(\'/stylesheets/posts.css\');
							</style>' . "\n";
						echo '<form method="post" action="/ajax_gateways/forum.php">' . "\n";
						echo '<input type="hidden" name="action" value="post_edit_save" />' . "\n";
						echo '<input type="hidden" name="post_id" value="' . $_GET['post_id'] . '"/>' . "\n";
						echo '<textarea style="width: 580px; height: 280px;" name="post_form_content" id="post_form_content">' . $post['content'] . '</textarea>' . "\n";
						echo '<input type="submit" style="float: right;" class="button_50" value="Spara" />' . "\n";
						echo '<script language="javascript" type="text/javascript" src="/javascripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
									<script language="javascript" type="text/javascript">
									tinyMCE.init({
										mode : "exact",
										elements: "post_form_content",
										theme: "advanced",
										theme_advanced_buttons1 : "bold,italic,underline,separator,bullist,numlist,separator,sup,charmap",
										theme_advanced_buttons2 : "",
										theme_advanced_buttons3 : "",
										theme_advanced_resize_horizontal : false,
										theme_advanced_resizing : true,
										theme_advanced_path : false,
										theme_advanced_toolbar_location : "top",
										theme_advanced_statusbar_location : "bottom",
										theme_advanced_toolbar_align : "left",
										auto_reset_designmode : true
									});
									</script>';
					}
				break;
				case 'post_comment':
					if(isset($_GET['post_id']))
					{
						echo '<h5>Kommentaren skickas till författarens gästbok</h5>' . "\n";
						echo '<form method="post" action="/ajax_gateways/forum.php">' . "\n";
						echo '<input type="hidden" name="action" value="post_comment_save" />' . "\n";
						echo '<input type="hidden" name="post_id" value="' . $_GET['post_id'] . '"/>' . "\n";
						echo '<textarea style="width: 580px; height: 230px;" name="post_form_content" id="post_form_content"></textarea>' . "\n";
						echo '<input type="submit" style="float: right;" class="button_50" value="Skicka" />' . "\n";
					}
				break;
				case 'post_delete_comment':
					if(isset($_GET['post_id']))
					{
						echo '<h5>När du tar bort ett inlägg kan det vara bra att ge en förklaring.</h5>' . "\n";
						echo '<p>Kommentaren hamnar i författarens gästbok. Om du inte vill kommentera så stäng bara fönstret</p>' . "\n";
						echo '<form method="post" action="/ajax_gateways/forum.php">' . "\n";
						echo '<input type="hidden" name="action" value="post_delete_comment_save" />' . "\n";
						echo '<input type="hidden" name="post_id" value="' . $_GET['post_id'] . '"/>' . "\n";
						echo '<textarea style="width: 580px; height: 180px;" name="post_form_content" id="post_form_content"></textarea>' . "\n";
						echo '<input type="submit" style="float: right;" class="button_50" value="Skicka" />' . "\n";
					}
				break;
				case 'discussion_delete_comment':
					if(isset($_GET['discussion_author']))
					{
						echo '<h5>När du tar bort en diskussion kan det vara bra att ge en förklaring.</h5>' . "\n";
						echo '<p>Kommentaren hamnar i författarens gästbok. Om du inte vill kommentera så stäng bara fönstret</p>' . "\n";
						echo '<form method="post" action="/ajax_gateways/forum.php">' . "\n";
						echo '<input type="hidden" name="action" value="discussion_delete_comment_save" />' . "\n";
						echo '<input type="hidden" name="discussion_title" value="' . $_GET['discussion_title'] . '"/>' . "\n";
						echo '<input type="hidden" name="discussion_author" value="' . $_GET['discussion_author'] . '"/>' . "\n";
						echo '<textarea style="width: 580px; height: 180px;" name="post_form_content" id="post_form_content"></textarea>' . "\n";
						echo '<input type="submit" style="float: right;" class="button_50" value="Skicka" />' . "\n";
					}
				break;
			}
		}
		elseif(count($_POST) > 0)
		{
			switch ($_POST['action'])
			{
				case 'post_edit_save':
					$posts = posts_fetch(array('post_id' => $_POST['post_id']));
					$post = array_pop($posts);
					if( $post['author'] != $_SESSION['login']['id'])
					{
						echo '<h1>Hörru du din tjuv! Låt bli andras inlägg!</h1>' . "\n";
						exit;
					}
					$query = 'UPDATE posts SET content="' . mysql_real_escape_string(html_entity_decode($_POST['post_form_content'])) . '" WHERE id="' . $_POST['post_id'] . '"';
					mysql_query($query) or die(report_sql_error($result, __FILE__, __LINE__));
					echo '<h1>Inlägget sparat</h1>' . "\n";

					echo '<p>Stäng det här fönstret och ladda om forumsidan för att se ditt inlägg.</p>' . "\n";
				break;
				case 'post_comment_save':
					$posts = posts_fetch(array('post_id' => $_POST['post_id']));
					$post = array_pop($posts);
					new_entry($post['author'], $_SESSION['login']['id'], $_POST['post_form_content']);
					echo '<h5>Kommentaren är nu skickad. Du kan stänga det här fönstret.</h5>' . "\n";
				break;
				case 'post_delete_comment_save':
					$posts = posts_fetch(array('post_id' => $_POST['post_id']));
					$post = array_pop($posts);
 					$discussions = discussions_fetch(array('id' => $post['discussion_id']));
 					$discussion = array_pop($discussions);
					$message = 'Ditt inlägg i diskussionen <a href="' . posts_url_get($_POST['post_id']) . '">' . $discussion['title'] . '</a> har tagits bort.<br />' . 
								$_POST['post_form_content'];
					new_entry($post['author'], $_SESSION['login']['id'], $message);
					echo '<h5>Användaren är nu informerad. Du kan stänga det här fönstret.</h5>' . "\n";
				break;
				case 'discussion_delete_comment_save':
					$message = 'Din diskussion ' . $_POST['discussion_title'] . ' har tagits bort. Ordningsvakten hälsar: ' . $_POST['post_form_content'];
					new_entry($_POST['discussion_author'], $_SESSION['login']['id'], $message);
					echo '<h5>Användaren är nu informerad. Du kan stänga det här fönstret.</h5>' . "\n";
				break;
			}
		}
	}
?>