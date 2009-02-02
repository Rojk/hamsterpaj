<?php
    require('../include/core/common.php');
    require(PATHS_INCLUDE . 'libraries/photoblog.lib.php');
    
    if ( ! isset($_GET['id']) || ! is_numeric($_GET['id']) )
    {
        die('Faulty ID.');
    }
    
    // fetch a single image
    $options = array(
        'photo_id' => $_GET['id']
    );
    
    $photo = photoblog_comments_fetch($options, array('user_container' => false));
    echo photoblog_comments_list(array($photo));