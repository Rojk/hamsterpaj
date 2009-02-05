<?php
    if ( ! isset($_GET['user_id'], $_GET['month'], $_GET['year']) )
    {
        die('Erronous input.');
    }
    
    if ( ! is_numeric($_GET['user_id']) || ! is_numeric($_GET['month']) || ! is_numeric($_GET['year']) )
    {
        die('Erronous input. #1');
    }
    
    require('../include/core/common.php');
    require(PATHS_INCLUDE . 'libraries/photoblog.lib.php');
    
    echo photoblog_calendar($_GET['user_id'], $_GET['month'], $_GET['year']);