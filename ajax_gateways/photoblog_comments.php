<?php
    require('../include/core/common.php');
    require(PATHS_INCLUDE . 'libraries/photoblog.lib.php');
    
    if ( ! isset($_GET['id']) || ! is_numeric($_GET['id']) )
    {
        die('Faulty ID.');
    }
    
    if ( ! isset($_GET['action']) )
    {
        die('Faulty action');
    }
    
    switch ($_GET['action'])
    {
        case 'post':
            
        break;
        
        case 'fetch':    
            // fetch a single image
            $options = array(
                'photo_id' => $_GET['id']
            );
        break;
    }
    
    $photo = photoblog_comments_fetch($options, array('use_container' => false));
    echo photoblog_comments_list($photo);