<?php
    require('../include/core/common.php');
    require(PATHS_INCLUDE . 'libraries/photoblog.lib.php');
    
    if ( ! isset($_GET['id']) || ! is_numeric($_GET['id']) )
    {
        echo 'Faulty ID.';
        die;
    }
    
    $options = array(
        'id' => $_GET['id']
    );
    
    $photo = photoblog_photos_fetch($options);
    echo json_encode($photo);