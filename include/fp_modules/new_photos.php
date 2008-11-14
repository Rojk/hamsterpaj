<?php
if(login_checklogin())
{
$photos = photos_fetch(array('limit' => 4, 'order-direction' => 'DESC'));
$output .= photos_list($photos);
}
?>