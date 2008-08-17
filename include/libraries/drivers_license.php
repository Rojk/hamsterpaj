<?php

function dl_question_echo($question, $alternatives)
{
	$action = $_SERVER['PHP_SELF'] . '?action=' . $_GET['action'];
	$action .= (isset($_GET['category'])) ? '&category=' . $_GET['category'] : '';
	echo '<form action="' . $action . '" method="post" class="dl_answer_form" />'. "\n";

	if($question['image'] == 1)
	{
		echo '<img src="http://images.hamsterpaj.net/drivers-license/question_illustrations/' . $question['id'] . '.jpg" class="image" />' . "\n";
	}

	echo '<p class="question">' . "\n";
	echo $question['question'] . "\n";
	echo '</p>' . "\n\n";
		
	echo '<ul class="alternatives">' . "\n";
	echo '<input type="hidden" name="question" value="' . $question['id'] . '" />' . "\n";
	shuffle($alternatives);
	foreach($alternatives AS $alternative)
	{
		echo '<li>' . "\n";
		echo '<input type="radio" name="answer" value="' . $alternative['id'] . '" id="dl_alt_' . $alternative['id'] . '" />' . "\n";
		echo '<label for="dl_alt_' . $alternative['id'] . '">' . $alternative['text'] . '</label>' . "\n";
		echo '</li>' . "\n\n";
	}
	echo '</ul>' . "\n\n";
	
	echo '<input type="submit" value="Skicka svar" />' . "\n";
	echo '</form>'. "\n";
	echo '<br style="clear: both;" />' . "\n";
}

function dl_question_fetch($options)
{
	/* 
		mode = 'test', 'practice'
		category = null, category handle
	*/
	
	$query = 'SELECT q.id, q.category, q.question, q.image FROM dl_questions AS q';
	if($options['mode'] == 'test')
	{
		$query .= ' WHERE q.id NOT IN("' . implode('", "', $_SESSION['drivers-license']['answered_questions']) . '")';
	}
	else
	{
		$query .= ' LEFT OUTER JOIN dl_answers AS a ON q.id = a.question AND a.user = "' . $_SESSION['login']['id'] . '"';
		$query .= ' WHERE 1';
		$query .= isset($options['category']) ? ' AND q.category = "' . $options['category'] . '"' : '';	
		$query .= ' AND ( a.score < 2 OR score IS NULL) ';	
	}

	$query .= 'ORDER BY RAND() LIMIT 1';
	
	//echo $query;
	//exit;
		
	
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	if(mysql_num_rows($result) == 0)
	{
		return false;
	}
	$question = mysql_fetch_assoc($result);
	
	/* Fetch the answer alternatives to the question */
	$query = 'SELECT id, text FROM dl_alternatives WHERE question = "' . $question['id'] . '" ORDER BY id ASC';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		$alternatives[] = $data;
	}
	
	/* Over and out */
	return array('question' => $question, 'alternatives' => $alternatives);
}

function dl_question_answer($question, $answer)
{
	$query = 'SELECT q.correct_answer, q.category, a.text FROM dl_questions AS q, dl_alternatives AS a WHERE q.id = "' . $question . '" AND a.id = q.correct_answer LIMIT 1';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$data = mysql_fetch_assoc($result);
	$category = $data['category'];
	
	$correct_answer_text = $data['text'];
	
	if($answer == $data['correct_answer'])
	{
		$message = 'correct';

		/* Create a row in the user answers table */
		$insertquery = 'INSERT INTO dl_answers (user, question, score) VALUES("' . $_SESSION['login']['id'] . '", "' . $question . '", 1)';
		if(!mysql_query($insertquery))
		{
			/* A row already existed, increase the score instead of creating a new row */
			$updatequery = 'UPDATE dl_answers SET score = score + 1 WHERE user = "' . $_SESSION['login']['id'] . '" AND question = "' . $question . '" LIMIT 1';
			mysql_query($updatequery);
	
			/* If the user has two points, create/update the row holding the users score for this category */
			$query = 'SELECT score FROM dl_answers WHERE user = "' . $_SESSION['login']['id'] . '" AND question = "' . $question . '"';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			$data = mysql_fetch_assoc($result);
			if($data['score'] == 2 && strlen($category) > 0)
			{
				$updatequery = 'UPDATE dl_scores SET ' . $category . ' = ' . $category . ' + 1 WHERE user = "' . $_SESSION['login']['id'] . '" LIMIT 1';
				$insertquery = 'INSERT INTO dl_scores (user, ' . $category . ') VALUES("' . $_SESSION['login']['id'] . '", 1)';
				mysql_query($insertquery) or mysql_query($updatequery);
			}
		}
	}
	else
	{
		$message = 'incorrect';
		$query = 'UPDATE dl_answers SET score = 0 WHERE user = "' . $_SESSION['login']['id'] . '" AND question = "' . $question . '" LIMIT 1';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));		
	}
	
	$messages['correct'] = '<div class="correct"><h2>Rätt svar</h2></div>';
	$messages['incorrect'] = '<div class="incorrect"><h2>Fel svar</h2><h4>Rätt svar var</h4><p class="correction">' . $correct_answer_text . '<br />Frågans ID-nummer: ' . $question . '</p></div>';
	
	echo $messages[$message];
	return $message;
}

function dl_clear_category()
{
	$query = 'UPDATE dl_scores SET ' . $_GET['category'] . ' = 0 WHERE user = "' . $_SESSION['login']['id'] . '"';
	mysql_query($query);
	
	$query = 'DELETE FROM dl_answers WHERE user = "' . $_SESSION['login']['id'] . '" AND question IN (SELECT id FROM dl_questions WHERE category = "' . $_GET['category'] . '")';
	mysql_query($query);
}

function dl_clear_all()
{
	$query = 'DELETE FROM dl_scores WHERE user = "' . $_SESSION['login']['id'] . '"';
	mysql_query($query);
	$query = 'DELETE FROM dl_answers WHERE user = "' . $_SESSION['login']['id'] . '"';
	mysql_query($query);
}


function dl_index()
{
	global $DL_CATEGORIES;

	foreach($DL_CATEGORIES AS $category)
	{
		$sum_count += $category['count'];
		$sum_completed += $category['completed'];
	}
?>
	<h1>Gratis teoriprogram på nätet</h1>
	
	<p>
		Här hittar du <strong><?php echo $sum_count; ?> frågor</strong> att träna på inför teoriprovet för vanligt B-körkort. Systemet håller koll
		på vilka frågor du kan och vilka du behöver öva mer på - även om du loggar ut och tar en paus eller byter dator.<br />
		Det kostar ingenting att använda teoriprogrammet men du måste vara inloggad för att det skall fungera!
	</p>
<?php
	
	echo '<h2><a href="?action=practice">Plugga på allt</a> ';
	echo login_checklogin() ? '(' . round(($sum_completed / $sum_count) * 100 ) . '% klart)' : '';
	echo '</h2>' . "\n";
	
	echo '<img src="http://images.hamsterpaj.net/drivers-license/dl_teaser.png" style="float: right; margin-left: 10px;" />' . "\n";

/*
	
	echo'<ul class="dl_category_list">' . "\n";
	foreach($DL_CATEGORIES AS $handle => $category)
	{
		echo '<li><a href="?action=practice&category=' . $handle . '">' . $category['label'] . '</a> ';
		if($category['completed'] == 0)
		{
			$status = 'inte påbörjad';
		}
		elseif($category['completed'] < $category['count'])
		{
			$status = round(($category['completed'] / $category['count']) * 100 ) . '% klar';
		}
		else
		{
			$status = 'klar';
		}
		echo '(' . $category['count'] . ' frågor, ' . $status . ')</li>' . "\n";
	}	
	echo '</ul>' . "\n";
	
	echo '<h2>Rensa ämnen du har klarat av</h2>' . "\n";
	echo '<p>Om du vill kan du nollställa dina poäng, antingen för alla frågor eller efter ämne. Klicka bara på en länk här nedanför så nollställer vi dina poäng.</p>' . "\n";
	echo '<h3><a href="?action=clear_all">Nollställ poängen i alla ämnen</a></h3>' . "\n";
		
	foreach($DL_CATEGORIES AS $handle => $category)
	{
		echo '<a href="?action=clear_category&category=' . $handle . '">' . $category['label'] . '</a>, ';
	*/
	
	echo '<table class="dl_category_index">' . "\n";
	echo '<tr><th>&nbsp;</th><th class="label">&nbsp;</th><th class="question_count">Frågor</th><th class="percentage_done">Avklarat</th></tr>' . "\n";
	foreach($DL_CATEGORIES AS $handle => $category)
	{
		echo '<tr>' . "\n";
		echo '<td>' . ( ($category['completed'] == $category['count']) ? '<img src="http://images.hamsterpaj.net/drivers-license/complete.png" alt="Alla frågor avklarade" />' : '&nbsp;' ) . '</td>';
		echo '<td><a href="?action=practice&category=' . $handle . '">' . $category['label'] . '</a></td>' . "\n";
		echo '<td>' . $category['count'] . '</td>' . "\n";
		echo '<td>' . round(($category['completed'] / $category['count']) * 100 ) . '%</td>' . "\n";
		echo '<td><a href="?action=clear_category&category=' . $handle . '" class="category_clear" title="Rensa kategorin ' . strtolower($category['label']) . '"><img src="http://images.hamsterpaj.net/drivers-license/clear.png" alt="Rensa '. $category['label'] . '" /></a></td>' . "\n";
		echo '</tr>' . "\n\n";
	}	
	echo '</table>';
	
}

?>

