<?php
	require('/storage/www/standard.php');
	$ui_options['javascripts'][] = 'user_box.js';
	$ui_options['stylesheets'][] = 'user_box.css';
	$ui_options['title'] = 'Hest';
	
	ui_top($ui_options);
?>

	<script>
	function kaka()
	{
		hp.user_box.draw({
			user_id: 87926,
			styling: 'normal_blue',
			items:
			{
				button_profile: {},
				button_guestbook: {},
				general_userinfo: { info: 'Haxx!' }
			}
		});
	}
	</script>
	
	<a href="javascript:kaka();void(0)">Go!</a> 
	
<?php
	ui_bottom();
?>