<?php
	require('include/core/common.php');
?><script>
<?php
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		echo 'opener.window.location = "/traffa/profile.php?id=' . $_GET['id'] . '";';
	}
	else
	{
		echo 'alert("Nu var du allt haxx?");';
	}
?>
window.close();
</script>