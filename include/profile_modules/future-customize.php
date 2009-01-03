<?php
require(PATHS_INCLUDE . 'future-functions.php');

	switch($_POST['future_time'])
	{
		case 'unknown':
			$date = '0000-00-00';
			break;
		case 'fixeddate':
			if (isset($_POST['fixed_date']) && !empty($_POST['fixed_date']))
			{
				$date = $_POST['fixed_date'];
			}
			break;
		case 'between':
			if (isset($_POST['start_year']) && !empty($_POST['start_year']) && isset($_POST['stop_year']) && !empty($_POST['stop_year']))
			{
				;
				$date = $_POST['start_year'];
				$enddate = $_POST['stop_year'];		
			}
			break;
		default:
			if (is_numeric(strtotime($_POST['future_time'])))
			{
				$date = $_POST['future_time'];
			}
			break;
	}
	if (isset($_POST['future_thing']))
	{
		if (empty($_POST['future_thing']))
		{
			jscript_alert('Du måste ange vad som händer i framtiden');
		}
		else 
		{
			$_POST['future_thing'] = htmlspecialchars($_POST['future_thing']);
			$status = '&nbsp;&nbsp;';
			
			$query = 'INSERT INTO traffa_future (userid, future_name, future_date, future_enddate, status, date_text) VALUES ("' . $_SESSION['userid'] . '", "' . $_POST['future_thing'] . '", "' . $date . '", "' . $enddate . '", "' . $status . '", "' . $_POST['date_text'] . '")';
			mysql_query($query) or die(report_sql_error($query));
		}
	}

function profile_future_jschangedate()
{
	echo '<script language="javascript" type="text/javascript">';
	echo 'function go()';
	echo '{';
	echo 'box = document.future.future_time;';
	echo 'date_value = box.options[box.selectedIndex].value;';
	echo 'if (date_value == "fixeddate") {';
	echo 'document.getElementById("fixed_date").style.display="block";';
	echo 'document.getElementById("between_date").style.display="none";';
	echo '}';
	echo 'else if (date_value == "between")	{';
	echo 'document.getElementById("fixed_date").style.display="none";';
	echo 'document.getElementById("between_date").style.display="block";';
	echo '}';
	echo 'else {';
	echo 'document.getElementById("fixed_date").style.display="none";';
	echo 'document.getElementById("between_date").style.display="none";';
	echo '}';
	echo 'document.future.date_text.value = document.future.future_time.options[document.future.future_time.selectedIndex ].text;';
	echo '}';
	echo '</script>';
}

function profile_future_printdate($when)
{
	echo '<div id="between_date" style="display: none;">';
	echo '<br />';
	echo '<table>';
	echo '<tr>';
	echo '<td> Startdatum:</td>';
	echo '<td><input name="start_year" value="" size="12" onfocus="this.blur()" readonly><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fStartPop(document.future.start_year,document.future.stop_year);return false;" HIDEFOCUS> <img class="PopcalTrigger" align="absmiddle" src="http://images.hamsterpaj.net/images/illustrations/date/calbtn.gif" width="34" height="22" border="0" alt=""></a></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>Slutdatum:</td>';
	echo '<td><input name="stop_year" value="" size="12" onfocus="this.blur()" readonly><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fEndPop(document.future.start_year,document.future.stop_year);return false;" HIDEFOCUS> <img class="PopcalTrigger" align="absmiddle" src="http://images.hamsterpaj.net/images/illustrations/date/calbtn.gif" width="34" height="22" border="0" alt=""></a></td>';
	echo '</tr>';
	echo '</table>';	
	echo '</div>';
	
	echo '<div id="fixed_date" style="display: none;">';
	echo '<br />';
	echo 'Datum: <input name="fixed_date" value="" size="11"><a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fPopCalendar(document.future.fixed_date);return false;" HIDEFOCUS> <img class="PopcalTrigger" align="absmiddle" src="http://images.hamsterpaj.net/images/illustrations/date/calbtn.gif" width="34" height="22" border="0" alt=""></a>';
	echo '</div>';
	
	echo '<iframe width=132 height=142 name="gToday:contrast:agenda.js" id="gToday:contrast:agenda.js" src="/javascripts/date/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;"></iframe>';


}

function profile_future_check_date($input_date)
{
	$curr_date = date('Y-m-d');
	$year = date('Y');
	if ($curr_date > $input_date) 
	{
		$year++;
	}
	return $year;
}

function profile_future_inputform()
{
	echo '<form action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '" method="post" name="future">';
  echo '<h2>Vad händer i framtiden?</h2>';
  echo '<input type="text" name="future_thing" style="width: 300px;"/><br />';
  echo '<h2>När händer det?</h2>';
  echo '<select name="future_time" onChange="go()" style="width: 300px;"><br />';
	echo '<option value="unkown">Ej valt</option>';
	echo '<option value="' . profile_future_check_date(date('Y-04-15')) . '-04-15">Till våren</option>';
  echo '<option value="' . profile_future_check_date(date('Y-07-15')) . '-07-15">Till sommaren</option>';
  echo '<option value="' . profile_future_check_date(date('Y-10-15')) . '-10-15">Till hösten</option>';
  echo '<option value="' . profile_future_check_date(date('Y-12-15')) . '-12-15">Till vintern</option>';
  echo '<option value="fixeddate">Datum</option>';
  echo '<option value="between">Start/Slutdatum</option>';
  echo '<option value="' . date('Y') . '-00-00">Under året</option>';
	echo '</select><br />';
	profile_future_printdate('start');
	echo '<br /><input type="submit" name="submit" value="Spara!" class="button" />';
	echo '<input type="hidden" name="date_text" value="Ej valt" />';
	echo '</form>';
	echo '<br /><hr />';
	
}

function profile_future_change_form()
{

	if (isset($_GET['action']) && $_GET['action'] == 'remove')
	{
		$query = 'DELETE FROM traffa_future WHERE id = "' . $_GET['futureid'] . '"';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}

	if (isset($_POST['change_submit']))
	{
		$query = 'UPDATE traffa_future SET status = "&nbsp;&nbsp;" WHERE userid = ' . $_SESSION['userid'];
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		foreach ($_POST as $id => $status)
		{
			$id = substr($id, 2);
		  if (is_numeric($id))
      {
				if ($status = 'yes')
				{
					$status = 'X';
				}
				$query = 'UPDATE traffa_future SET status = "' . $status . '" WHERE id = ' . $id;
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			}
		}
	}

	$query = 'SELECT * FROM traffa_future WHERE userid = ' . $_SESSION['userid'] . ' ORDER BY future_date';
	$result = mysql_query($query) or die(report_sql_error($query));
	echo '<h2>Din framtid</h2>';
	echo '<form action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '" method="post">';
	while ($data = mysql_fetch_assoc($result))
	{
		$checked = '';
		if ($data['status'] == 'X') 
		{
			$checked = 'checked';
		}
		echo '<input type="checkbox" value="yes" name="id' . $data['id'] . '" ' . $checked . '/> ';
		echo $data['future_name'];
		echo profile_future_draw_text($data['date_text'], $data['future_date'], $data['future_enddate']) . ' ';
		echo ' <a href="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id']. '&action=remove&futureid=' . $data['id'] . '">[Ta bort]</a><br />';
	}
	if (mysql_num_rows($result) > 0)
	{
		echo '<br /><input type="submit" name="change_submit" value="Spara de nya valen" class="button" />';
	}
	echo '</form>';
}


profile_future_jschangedate();
profile_future_inputform();
profile_future_change_form();

?>
