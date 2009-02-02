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
            if ( ! login_checklogin() )
            {
                die('Only users can post comments.');
            }
            
            $options = array(
                'photo_id' => $_GET['id'],
                'comment' => $_POST['comment'],
                'author' => $_SESSION['login']['id']
            );
            
            photoblog_comments_add($options);
            
            echo 'Skickat! Häftigt! Nästan som BDB ju.';
        break;
        
        case 'fetch':    
            // fetch a single image
            $options = array(
                'photo_id' => $_GET['id']
            );
            
            $photo = photoblog_comments_fetch($options, array('use_container' => false));
            echo photoblog_comments_list($photo);
        break;
    }
    