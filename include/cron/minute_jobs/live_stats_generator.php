<?php

unset($info);

$result = mysql_query('SELECT COUNT(*) AS members FROM login WHERE is_removed = 0');
$data = mysql_fetch_assoc($result);
$info['members'] = $data['members'];

$online = 0;
$logged_in = 0;
$session_dir = opendir(session_save_path());
while($file = readdir($session_dir))
{
  $online++;
}

$result = mysql_query('SELECT COUNT(*) AS logged_in FROM login WHERE lastaction > ' . (time() - 900));
$data = mysql_fetch_assoc($result);


$info['visitors'] = $online;
$info['logged_in'] = $data['logged_in'];

$output = serialize($info);

$handle = fopen(PATHS_CACHE . 'live_stats.phpserialized', 'w');

fwrite($handle, $output);
fclose($handle);

?>
