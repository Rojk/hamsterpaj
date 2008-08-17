<?php
require('/storage/www/standard.php');

define('WIDTH', 150);
define('HEIGHT', 35);

session_start();

header ("Content-type: image/png");
$im = @imagecreatetruecolor(WIDTH, HEIGHT) or die("Cannot Initialize new GD image stream");
$im = imagecreatefrompng(PATHS_INCLUDE . 'security_code_background.png');

$text_color = rand(50, 200);
$text_color = imagecolorallocate($im, $color, $color, $color);

imagettftext($im, 20, rand(-10, 0), rand(5, 20), 20, $text_color, PATHS_INCLUDE . 'markerman.ttf', $_SESSION['register_security_code']);

imagepng($im);
imagedestroy($im);
?> 
