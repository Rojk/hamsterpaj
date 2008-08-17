<?
require('../include/core/common.php');
$ui_options['stylesheets'][] = 'traffa_index.css';
$ui_options['stylesheets'][] = '/profilteman_testmiljo/joar_lef_style.css';
$ui_options['title'] = 'Temastatistik p&aring; Hamsterpaj.net';
ui_top($ui_options);


$themes = array();

$query = "SELECT profile_theme, userid FROM userinfo";
$result = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_array($result))
{
	if (strlen($row['profile_theme']) > 0)
	{
		//echo $row['profile_theme'] . '<br />';
		$count[$row['profile_theme']][] = $row['profile_theme'];
		if (!in_array($row['profile_theme'], $themes))
		{
			$themes[] = $row['profile_theme'];
		}
	}
	//$count[$row['profile_theme']][] = $row['userid'];
}
//echo preint_r($count);
$themes[] = 'saftkillen_ft_joar_golden';
echo count($count['joar_halo3']) . '<br />';
echo preint_r($themes);
echo '<table>';
foreach ($themes as $k => $v)
{
	//$temp = $count[$v];
	echo '<tr><td>' . $v . '</td><td>' . count($count[$v]) . '</td></tr>';
}
echo '</table>';

ui_bottom();
?>