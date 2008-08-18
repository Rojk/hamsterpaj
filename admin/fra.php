<?php
  require('/storage/www/standard.php');

  $ui_options['menu_path'] = array('fra');
  $ui_options['title'] = 'Adminstart';

  ui_top($ui_options);

  echo 'Du har dina val till vänster.';

  ui_bottom();
?>