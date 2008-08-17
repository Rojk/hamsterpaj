<?php

function plump_dices()
{
	$_SESSION['plump']['dices'][0] = rand(1, 6);
	$_SESSION['plump']['dices'][1] = rand(1, 6);
	$_SESSION['plump']['dices'][2] = rand(1, 6);
}

function plump_combinations_by_order($A, $B, $C)
{
	/* A + */
	$return["$A + $B + $C"] = $A + $B + $C;
	$return["$A + $B - $C"] = $A + $B - $C;
	$return["$A + ($B - $C)"] = $A + ($B - $C);
	$return["($A + $B) * $C"] = ($A + $B) * $C;
	$return["$A + ($B * $C)"] = $A + ($B * $C);
	$return["($A + $B) / $C"] = ($A + $B) / $C;
	$return["$A + ($B / $C)"] = $A + ($B / $C);	

	/* A - */
	$return["$A - $B + $C"] = $A - $B + $C;
	$return["$A - $B - $C"] = $A - $B - $C;
	$return["$A - ($B - $C)"] = $A - ($B - $C);
	$return["($A - $B) * $C"] = ($A - $B) * $C;
	$return["$A - ($B * $C)"] = $A - ($B * $C);
	$return["($A - $B) / $C"] = ($A - $B) / $C;
	$return["$A - ($B / $C)"] = $A - ($B / $C);	

	/* A * */
	$return["$A * $B + $C"] = $A * $B + $C;
	$return["$A * $B - $C"] = $A * $B - $C;
	$return["$A * ($B - $C)"] = $A * ($B - $C);
	$return["$A * ($B + $C)"] = $A * ($B + $C);
	$return["($A * $B) * $C"] = ($A * $B) * $C;
	$return["$A * ($B * $C)"] = $A * ($B * $C);
	$return["($A * $B) / $C"] = ($A * $B) / $C;
	$return["$A * ($B / $C)"] = $A * ($B / $C);	

	/* A / */
	$return["$A / $B + $C"] = $A / $B + $C;
	$return["$A / $B - $C"] = $A / $B - $C;
	$return["$A / ($B - $C)"] = $A / ($B - $C);
	$return["$A / ($B + $C)"] = $A / ($B + $C);
	$return["($A / $B) * $C"] = ($A / $B) * $C;
	$return["$A / ($B * $C)"] = $A / ($B * $C);
	$return["($A / $B) / $C"] = ($A / $B) / $C;
	$return["$A / ($B / $C)"] = $A / ($B / $C);	

	return $return;
}

function plump_combinations($dices)
{
	$combinations = array();
	
	$combinations = array_merge($combinations, plump_combinations_by_order($dices[0], $dices[1], $dices[2]));
	$combinations = array_merge($combinations, plump_combinations_by_order($dices[0], $dices[2], $dices[1]));
	$combinations = array_merge($combinations, plump_combinations_by_order($dices[1], $dices[0], $dices[2]));
	$combinations = array_merge($combinations, plump_combinations_by_order($dices[1], $dices[2], $dices[0]));
	$combinations = array_merge($combinations, plump_combinations_by_order($dices[2], $dices[0], $dices[1]));
	$combinations = array_merge($combinations, plump_combinations_by_order($dices[2], $dices[1], $dices[0]));

	foreach($combinations AS $calculation => $result)
	{
		if($result > 0 && is_int($result) && $result < 14*14)
		{
			$return[$calculation] = $result;
		}
	}
	
	$return = array_unique($return);
	asort($return);

	return $return;
}

function plump_create_game()
{
	unset($_SESSION['plump']);

	for($row = 1; $row < 14; $row++)
	{
		for($col = 1; $col <= 14; $col++)
		{
			 $_SESSION['plump']['board'][$row][$col] = 0;
		}
	}
}

function plump_rules()
{
	echo '<div id="plump_rules">' . "\n";
	echo '<h2>Så här spelar man plump</h2>' . "\n";
	echo '<p>Avänd de fyra räknesätten, plus, minus, delat med och gånger, för att räkna fram ett tal från dina tärningar. Du måste använda alla tärningar en gång ';
	echo 'men du får inte använda samma tärning flera gånger. Resultatet får inte bli ett decimaltal, <em>5/2*2=5</em> är okej, men <em>5/2+2=4.5</em> är förbjudet.</p>';
	echo '<p>När du har räknat fram ett tal klickar på du en ledig ruta (vit), då färgas rutan grå och den blir upptagen.<br />Du får ett poäng för varje ruta du färgar grå, ';
	echo 'dessutom får du ett poäng <em>för varje närliggande ruta</em> också, detta gäller även diagonalt.</p>';
	echo '<p>Om du klickar på ett tal du inte kan räkna fram eller väljer "Ta en plump" så får du en plump. Plumparna visas som små svarta cirklar där du ser din poäng, när någon ';
	echo 'har fått fyra plumpar avslutas spelet.</p>' . "\n";
	echo '</div>' . "\n";	
}

function plump_pos_by_number($number)
{
	$return['row'] = ceil($number/7);
	$return['col'] = $number - (($return['row']-1) *11);

	return $return;
}

function plump_square_free($number)
{
	$pos = plump_pos_by_number($number);
	
	if($_SESSION['plump']['board'][$pos['row']][$pos['col']] == 1)
	{
		return false;
	}
	else
	{
		return true;
	}
}

function plump_score($row, $col)
{
	$score = 1;
	
	$score += ($_SESSION['plump']['board'][$row-1][$col-1] == 1) ? 1 : 0;
	$score += ($_SESSION['plump']['board'][$row-1][$col] == 1) ? 1 : 0;
	$score += ($_SESSION['plump']['board'][$row-1][$col+1] == 1) ? 1 : 0;

	$score += ($_SESSION['plump']['board'][$row][$col-1] == 1) ? 1 : 0;
	$score += ($_SESSION['plump']['board'][$row][$col+1] == 1) ? 1 : 0;

	$score += ($_SESSION['plump']['board'][$row+1][$col-1] == 1) ? 1 : 0;
	$score += ($_SESSION['plump']['board'][$row+1][$col] == 1) ? 1 : 0;
	$score += ($_SESSION['plump']['board'][$row+1][$col+1] == 1) ? 1 : 0;

	return $score;
}

function plump_display_dices()
{
	for($i = 0; $i < 3; $i++)
	{
		$output .= '<img src="' . IMAGE_URL . 'dices/' . $_SESSION['plump']['dices'][$i] . '_' . rand(1, 4) . '.png" alt="' . $_SESSION['plump']['dices'][$i] . '" class="dice" />' . "\n";
	}
	return $output;
}

function plump_get_max_score($combinations)
{
	$max_score = array();
	foreach($combinations AS $calculation => $result)
	{
		$position = plump_pos_by_number($result);
		
		if($_SESSION['plump']['board'][$position['row']][$position['col']] != 1)
		{
			if(plump_score($position['row'], $position['col']) >= $max_score['score'])
			{
				$max_score['score'] = plump_score($position['row'], $position['col']);
				$max_score['calculation'] = $calculation;
				$max_score['result'] = $result;
				$max_score['row'] = $position['row'];
				$max_score['col'] = $position['col'];
			}
		}
	}
	
	if($max_score['score'] < 1)
	{
		return false;
	}
	return $max_score;
}

function plump_game_over()
{
	echo '<h1>Spelet är slut!</h1>' . "\n";
	if($_SESSION['plump']['computer_score'] == $_SESSION['plump']['user_score'])
	{
		echo '<h2>Det blev oavgjort, både du och datorn fick ' . $_SESSION['plump']['computer_score'] . ' poäng</h2>' . "\n";
	}
	elseif($_SESSION['plump']['computer_score'] > $_SESSION['plump']['user_score'])
	{
		echo '<h2>Nedrans, du förlorade med ' . $_SESSION['plump']['user_score'] . ' mot ' . $_SESSION['plump']['computer_score'] . ' poäng</h2>' . "\n";		
	}
	elseif($_SESSION['plump']['computer_score'] < $_SESSION['plump']['user_score'])
	{
		echo '<h2>Grattis, du slog datorn med ' . $_SESSION['plump']['user_score'] . ' mot ' . $_SESSION['plump']['computer_score'] . ' poäng</h2>' . "\n";		
	}
	echo '<a href="?action=start">Spela en till omgång</a>' . "\n";
}

function plump_score_field()
{
	$output .= "\n" . '<!-- Plump score field -->' . "\n";
	$output .= '<div class="plump_score_field">' . "\n";
	$output .= '<h1>Poängställning</h1>' . "\n";

	$output .= '<h3>Du har ' .  $_SESSION['plump']['user_score'] . ' poäng</h3>' . "\n";
	for($i = 1; $i <= 4; $i++)
	{
		$icon = ($_SESSION['plump']['user_plumps'] < $i) ? 'plump_empty.png' : 'plump_full.png';
		$output .= '<img src="' . IMAGE_URL . 'common_icons/' . $icon . '" />' . "\n";
	}

	$output .= '<h3>Datorn har ' . $_SESSION['plump']['computer_score'] . ' poäng</h3>' . "\n";
	for($i = 1; $i <= 4; $i++)
	{
		$icon = ($_SESSION['plump']['computer_plumps'] < $i) ? 'plump_empty.png' : 'plump_full.png';
		$output .= '<img src="' . IMAGE_URL . 'common_icons/' . $icon . '" />' . "\n";
	}

	$output .= '</div>' . "\n\n";
	return $output;
}

function plump_draw_board()
{
	echo '<table class="plump_board">' . "\n";
	for($row = 1; $row < 14; $row++)
	{
		echo '	<tr>' . "\n";
		for($col = 1; $col <= 14; $col++)
		{
			if($_SESSION['plump']['board'][$row][$col] != 1)
			{
				echo '		<td class="vacant"><a href="?action=mark&amp;number=' . ((($row-1)*14) + $col) . '">' . ((($row-1)*14) + $col) . '</a></td>' . "\n";
			}
			else
			{
				echo '		<td class="occupied">' . ((($row-1)*14) + $col) . '</td>' . "\n";
			}
		}
		echo '	</tr>' . "\n";
	}
	echo '</table>' . "\n";
}

?>