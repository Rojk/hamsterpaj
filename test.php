<?php
	include("/storage/www/standard.php");
/*
1990-10-24
*/

$birthday = $_SESSION['userinfo']['birthday'];
$birthday = "1990-10-24";
$bd_exp = explode("-", $birthday, -2);
echo $birthday;
//print_r($db_exp);
$bd_year = $bd_exp[0];
$bd_month = $bd_exp[1];
$bd_day = $bd_exp[2];
echo $bd_year.$bd_month.$db_day;


?>
