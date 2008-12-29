<?php
    require('../include/core/common.php');
    require(PATHS_INCLUDE . 'libraries/photoblog.lib.php');
    
    if ( ! isset($_GET['id']) || ! is_numeric($_GET['id']) )
    {
        echo 'Faulty ID.';
        die;
    }
    
    if ( ! isset($_GET['month']) )
    {
        $options = array(
            'id' => $_GET['id']
        );
    }
    else
    {
        if ( ! is_numeric($_GET['month']) )
        {
            die('Faulty month');
        }
        
        $options = array(
            'user' => $_GET['id'],
            'month' => $_GET['month']
        );
    }
    
    $photo = photoblog_photos_fetch($options);
    echo json_encode($photo);