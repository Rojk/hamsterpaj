<?php
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";
?>
<?php
	require_once(PATHS_INCLUDE . '../amusefiles/amuse-functions.php');
  $query = 'SELECT i.id, i.name, i.image, i.type, ';
  $query .= 'IF(LENGTH(i.info) > 80, CONCAT(LEFT(i.info, 77), \'...\'), i.info) AS info ';
  $query .= 'FROM amuse_items AS i, amuse_notices AS n WHERE i.id = n.item_id AND n.user = "' . $userid . '" AND i.type = 1 ';
  $query .= 'ORDER BY n.timestamp DESC LIMIT 9';
  $result = mysql_query($query) or die(report_sql_error($query));
  if(mysql_num_rows($result) > 0)
  {
    echo '<table><tr>';
    for($i = 0; $data = mysql_fetch_assoc($result); $i++)
    {
      if($i == 3 || $i == 6)
      {
        echo '</tr><tr>';
        $i = 0;
      }
      amuse_draw_small_item($data, 'table');
    }
    echo '</table>';
  }
?>
</div>