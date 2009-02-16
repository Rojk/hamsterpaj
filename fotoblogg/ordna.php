<?php
    $out .= '<h1>Sortera dina foton</h1>';
    $out .= '<p>Dra och släpp bitches!</p>';
    
    $out .= '<h2>Skapa kategori</h2>';
    $out .= '<p>~Skapa~</p>';
    
    $options = array('user' => $_SESSION['login']['id']);
    $photos = photoblog_photos_fetch($options);
    
    $albums = array();
    
    foreach ( $photos as $photo )
    {
        $albums[$photo['category']][] = '<li><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" title="' . $photo['username'] . '" /></li>';
    }
    
    $out .= '<div id="photoblog_sort">';
    foreach ( $albums as $id => $album )
    {
        $out .= '<h2>' . $id . ' <input type="text" value="Ändra namn eller nåt" /></h2>';
        $out .= '<ul>';
        $out .= implode('', $album);
        $out .= '</ul>';
    }
    $out .= '</div>';
?>