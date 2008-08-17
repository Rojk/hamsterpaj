<?php
session_start();

header ("Content-type: image/png");
$im = @imagecreatetruecolor(300, 50) or die("Cannot Initialize new GD image stream");
$text_color = imagecolorallocate($im, rand(50,255), rand(50,255), rand(50,255));
$line1_color = imagecolorallocate($im, rand(50,255), rand(50,255), rand(50,255));
$line2_color = imagecolorallocate($im, rand(50,255), rand(50,255), rand(50,255));
$line3_color = imagecolorallocate($im, rand(50,255), rand(50,255), rand(50,255));
$line4_color = imagecolorallocate($im, rand(50,255), rand(50,255), rand(50,255));

imagestring($im, 5, rand(10, 200), rand(5, 30), $_SESSION['authcode'], $text_color);
imageline($im, rand(250, 300), rand(20, 40), rand(200, 290), rand(20, 43), $line1_color);
imageline($im, rand(30, 100), rand(2, 18), rand(1, 50), rand(10, 40), $line2_color);
imageline($im, rand(100, 200), rand(30, 50), rand(10, 150), rand(5, 20), $line3_color);
imageline($im, rand(150, 250), rand(15, 30), rand(1, 150), rand(1, 11), $line4_color);
imageline($im, rand(250, 300), rand(20, 40), rand(200, 290), rand(20, 43), $line1_color);
imageline($im, rand(30, 100), rand(2, 18), rand(1, 50), rand(10, 40), $line2_color);
imageline($im, rand(100, 200), rand(30, 50), rand(10, 150), rand(5, 20), $line3_color);
imageline($im, rand(150, 250), rand(15, 30), rand(1, 150), rand(1, 11), $line4_color);
imagepng($im);
imagedestroy($im);
?> 
