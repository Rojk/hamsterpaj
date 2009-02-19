<?php
    $ui_options['javascripts'][] = 'sorter.js';

    $out .= '<div id="photoblog_sort">';
    $out .= '<h1>Sortera dina bilder genom att dra och släppa bilderna dit du vill ha dem</h1>';
    
    /*$out .= '<h2>Skapa kategori</h2>';
    $out .= '<p>~Skapa~</p>';*/
    
    $options = array('user' => $_SESSION['login']['id']);
    $photos = photoblog_photos_fetch($options);
    
    $options['create_if_not_found'] = false;
    $options['id_index'] = true;
    $categories = photoblog_categories_fetch($options);
    
    $albums = array();
    
    foreach ( $photos as $photo )
    {
        $albums[$photo['category']][] = '<li id="photo_' . $photo['id'] . '"><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" title="' . $photo['username'] . '" /><br /><input type="checkbox" name="foo" value="' . $photo['id'] . '" /></li>';
    }
    
    foreach ( $albums as $id => $album )
    {
        $out .= '<h2>' . (! strlen($categories[$id]['name']) ? 'Inget namn' : $categories[$id]['name']) . ' <!--<input type="text" value="Ändra namn eller nåt" />--></h2>';
        $out .= '<ul id="album_' . $id . '">';
        $out .= implode('', $album);
        $out .= '</ul>';
    }
    $out .= '<p><a class="photoblog_sort_save" href="#">Spara ändringar</a> | <a class="photoblog_sort_remove" href="#">Ta bort markerade</a></p>';
    $out .= '</div>';
?>