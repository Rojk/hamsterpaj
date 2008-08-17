<?php

function text_quality_rank($text)
{

	$result = quality_get_array($text);

	return $result['quality_rank'];
}

/**
	returns an array of results
	$return['
	*/
function quality_get_array($text)
{
	// these are the weights for all factors affecting the final score
	$options['weight']['capital_post'] = 0.3;
	$options['weight']['capital_sentence'] = 0.8;
	$options['weight']['short_sentence'] = 1;
	$options['weight']['comma'] = 1;
	$options['weight']['spelling'] = 0.6;
	$options['weight']['rubbish'] = 1;
	$options['weight']['repetition'] = 1;
	$options['weight']['long_sentence'] = 0.5;
	$options['weight']['long_sentence_no_comma'] = 1;
	$options['weight']['length'] = 3;

	// settings
	// sentence length boundaries
	$options['words_in_short_sentence'] = 4;
	$options['words_in_long_sentence'] = 45;
		
	// post length boundaries in words
	$options['post_length']['limit_a'] = 3;
																		//from -1 to 0
	$options['post_length']['limit_b'] = 30;
																		// 0
	$options['post_length']['limit_c'] = 100;
																		//from 0 to 1
	$options['post_length']['limit_d'] = 400;
																		// 1
	$options['post_length']['limit_e'] = 1000;
																		//from 1 to -1
	$options['post_length']['limit_f'] = 1500;

	// rubbish words and expressions
	$options['rubbish'] = array('o', 'lol', 'lr', 'Elr');

	// word lengths
	$options['words']['limit_long'] = 11;

	//remove all html tags
	log_to_file('henrik', LOGLEVEL_DEBUG, __FILE__, __LINE__, $text);
	$text = strip_tags($text);
	
	// remove qouted text
	$text = preg_replace('/\[citat:[\w-åäö]+=\d+\](.*)\[\/citat\]/m', '', $text);

	// remove answer tags
	$text = preg_replace('/\[svar:[\w-åäö]+=\d+\]/', '', $text);
	$text = preg_replace('/\[\/svar\]/', '', $text);

	// replace dots in host names
	$text = preg_replace('/((\w+)\.)+(com|net|nu|se|org|eu)/', 'hostname', $text);
	
	// remove propritary tags
	$text = preg_replace('/\[.*\]/', '', $text);

	// count words and words length
	$words = preg_split('/[\s,\.\?!:;]+/', trim($text));
//	$result['words']['words'] = $words;
	$result['words']['count'] = count($words);
	$result['words']['long'] = 0;
	foreach($words as $word)
	{
		if(count($word) > $options['words']['limit_long'])
		{
			$result['words']['long']++;
		}
	}
	$result['words']['long_fraction'] = $result['words']['long'] / $result['words']['count'];

	//Hitta upprepningar av ord
	$uniq = array_unique($words);
	$result['words']['repetition'] = count($words) - count($uniq);

	// sentence length
	// the avarage length of sentences and count of short and long sentences
	$result['sentence']['count'] = 0;
	$result['sentence']['short'] = 0;
	$result['sentence']['long'] = 0;
	// split text into sentences
	$sentences = preg_split('/[\.!\?\n]+/', trim($text));
	$result['commas']['long_sentence_no_comma'] = 0;
	foreach($sentences as $sentence)
	{
		$sentence = trim($sentence);
		if(strlen($sentence) > 8)
		{
			// count the words in each sentence
			$words = preg_split('/[ ,\.!\?\t#\*_\n:;]+/', trim($sentence));
			$result['sentence']['wordcount'] += count($words);
			if(count($words) < $options['words_in_short_sentence'] && count(trim($sentence)) > 0)
			{
				$result['sentence']['short']++;
				$result['sentence']['short_sentences'][] = $sentence;
			}
			if(count($words) > $options['words_in_long_sentence'])
			{
				$result['sentence']['long']++;
				$commas = preg_match_all('/([\wåäö], [\wåäö])/', $text, $matches);
				if($commas < 1)
				{
					$result['commas']['long_sentence_no_comma']++;
				}
				$result['sentence']['long_sentences'][] = $sentence;
			}
			if(preg_match('/^\s*[A-ZÅÄÖ]/', $sentence, $matches))
			{
				$result['sentence']['starters'][] = $matches[0];
				$result['capital']['sentence']++;
			}
			$result['sentence']['sentences'][] = $sentence;
		}
	}
	$result['sentence']['count'] = count($result['sentence']['sentences']);
	$result['sentence']['avarage'] = $result['words']['count'] / $result['sentence']['count'];
	// points for short and long sentences
	$result['score']['short_sentence'] = - $options['weight']['short_sentence'] * ($result['sentence']['short'] / $result['sentence']['count']);
	$result['score']['long_sentence'] = - $options['weight']['long_sentence'] * ($result['sentence']['long'] / $result['sentence']['count']);

	// post length
	// points given for post length in words and in these intervals
	/*
		limits are named from limit_a to limit_f, x = count($words), s = score
		    x < a  =>  s = -1
		a < x < b  =>  s =  from -1 at a to 0 at b
		b < x < c  =>  s =  0
		c < x < d  =>  s = 	from 0 at c to 1 at d
		d < x < e  =>  s =  1
		e < x < f  =>  s =  from 1 at e to -1 at f
		f < x      =>  s = -1
	*/

	$result['length'] = $result['words']['count'];
	//limit   - 0
	if($result['length'] < $options['post_length']['limit_a'])
	{
		$result['score']['length'] = - 1;
	}
	//limit 0 - 1
	elseif($result['length'] < $options['post_length']['limit_b'])
	{
		$result['score']['length'] = - ($options['post_length']['limit_b'] - $result['length']) / ($options['post_length']['limit_b'] - $options['post_length']['limit_a']);
	}
	//limit 1 - 2
	else if($result['length'] < $options['post_length']['limit_c'])
	{
		$result['score']['length'] = 0;
	}
	//limit 2 - 3
	else if($result['length'] < $options['post_length']['limit_d'])
	{
		$result['score']['length'] = ($result['length'] - $options['post_length']['limit_c']) / ($options['post_length']['limit_d'] - $options['post_length']['limit_c']);
	}
	//limit 3 - 4
	else if($result['length'] < $options['post_length']['limit_e'])
	{
		$result['score']['length'] = 1;
	}
	//limit 4 - 5
	else if($result['length'] < $options['post_length']['limit_f'])
	{
		$result['score']['length'] = 1 - (($result['length'] - $options['post_length']['limit_e']) / (($options['post_length']['limit_f'] - $options['post_length']['limit_e'])/2));
	}
	else
	{
		$result['score']['length'] = -1;
	}
	
	// kommatecken
	// antalet kommatecken i texten.
	// poäng ges för antalet kommatecken i förhållande till antalet meningar
	$result['comma']['count'] = 0;
	$result['comma']['count'] = preg_match_all('/([\wåäö], [\wåäö])/', $text, $matches);
	$result['comma']['comma_per_sentence'] = $result['comma']['count'] / $result['sentence']['count'];

	// Versal i början på inlägg
	// Boolean, om inlägget börjar med versal
	$matches = array();
	$result['capital']['post'] = preg_match_all('/^[A-Z]/', trim($text), $matches);
	
	//Förekomst av uttryck och tecken som ger minuspoäng
	$intersect = array_intersect($rubbish, $words);
	$result['rubbish']['rubbish_per_sentence'] = count($intersect) / $result['sentences']['count'];
	$result['rubbish']['rubbish_per_sentence'] = $result['rubbish']['rubbish_per_sentence'] > 0 ? $result['rubbish']['rubbish_per_sentence'] : 0;
	
	//todo! Kör stavningskontroll via shell_exec. Poäng för antal felstavningar i förhållande till antalet ord.
	$words_string = preg_replace('/[^[:alpha:]åäö]/i', ' ', strip_tags(html_entity_decode($text)));

	preg_match_all('/& ([[:alpha:]åäö]+) \d+ \d+: ((([[:alpha:]åäö]+),?\s?)+)/mi',
								 shell_exec('echo "' . $words_string . '" | aspell --lang=sv -a --sug-mode=ultra'),
								 $matches);
	$result['spelling']['words_misspelled'] = count($matches[1]);

	
	// övriga poäng
	//length har redan räknats ut ovan
	$result['score']['long_sentence'] = - $result['sentence']['long'] / $result['sentence']['count'];
	$result['score']['short_sentence'] = - max($result['sentence']['short'] - $result['sentence']['count']/4, 0) / $result['sentence']['count']; 
	$result['score']['comma'] = $result['comma']['comma_per_sentence'];
	$result['score']['long_sentence_no_comma'] = $result['comma']['long_sentence_no_comma'] / ($result['sentence']['count'] * 2);
	$result['score']['spelling'] = - $result['spelling']['words_misspelled']  / $result['sentence']['count'];
	$result['score']['capital_post'] = $result['capital']['post'] ? 1 : -1;
	// A sentence without initial capital is -2, a sentence with an initial capital is +1
	// w = sentences with initial capital
	// o = sentences without initial capital
	// t = total number of sentences
	// score = w/t - 2(o/t) = (w - 2o)/t = (w - 2(t - w))/t = (w - 2t + 2w)/t = (3w - 2t)/t = 3(w/t) - 2
	// -1 is minimum score
	$result['score']['capital_sentence'] = max(3*$result['capital']['sentence']/$result['sentence']['count'] - 2, -1);
	$result['score']['repetition'] = $result['repetition'] / count($words);
	$result['score']['rubbish'] = $result['rubbish']['rubbish_per_sentence'];

	$total = 0;
	$totalweight = 0;
	foreach($result['score'] as $key => $score)
	{
		if($score != 0)
		{
			$totalweight += $options['weight'][$key];
			$total += $score * $options['weight'][$key];
		}
	}
	$q = $total / $totalweight;
	$result['quality_rank'] = $q > 0 ? sqrt($q) : - sqrt(abs($q));

	$points = $result['score'];
	$suggestions['short_sentences'] = 'undvik väldigt korta meningar';
	$suggestions['long_sentences'] = 'skriv inte för långa meningar';
	$suggestions['length'] = 'skriva ett längre inlägg';
	$suggestions['comma'] = 'använda komma för att få en mer lättläst text';
	$suggestions['long_sentence_no_comma'] = 'använd komma i långa meningar för att få texten mer lättläst';
	$suggestions['capital'] = 'börja ny mening med stor bokstav (versal)';
	$suggestions['rubbish'] = 'undvika chatt-språk och förkortningar';
	$suggestions['spelling'] = 'stava rätt';
	$suggestions['repetition'] = 'försök att variera ditt ordval';

	unset($quality_suggestions);
	if($points['long_sentence'] < 0) { $quality_suggestions[] = $suggestions['long_sentences']; }
	if($points['short_sentence'] < 0) { $quality_suggestions[] = $suggestions['short_sentences']; }
	if($points['length'] < 0) { $quality_suggestions[] = $suggestions['length']; }
	if($points['comma'] < 0) { $quality_suggestions[] = $suggestions['comma']; }
	if($points['long_sentence_no_comma'] < 0) { $quality_suggestions[] = $suggestions['long_sentence_no_comma']; }
	if($points['capital_sentence'] < 0) { $quality_suggestions[] = $suggestions['capital']; }
	if($points['rubbish'] < 0) { $quality_suggestions[] = $suggestions['rubbish']; }
	if($points['spelling'] < 0) { $quality_suggestions[] = $suggestions['spelling']; }
	if($points['capital'] < 0) { $quality_suggestions[] = $suggestions['capital']; }
	if($points['repetition'] < 0) { $quality_suggestions[] = $suggestions['repetition']; }

	$result['quality_suggestions'] = $quality_suggestions;
	return $result;
}
//	preint_r($points);
//	preint_r($return);

?>