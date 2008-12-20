<?php
require('../include/core/common.php');
$ui_options['menu_path'] = array('mattan', 'ditt_namn');
ui_top($ui_options);

	function makeBinary($inputString, $byteLength=8)
	{
    	$binaryOutput = '';
    	$strSize = strlen($inputString);

    	for($x=0; $x<$strSize; $x++)
    	{
        	$charBin = decbin(ord($inputString{$x}));
    	    $charBin = str_pad($charBin, $byteLength, '0', STR_PAD_LEFT);
    		$binaryOutput .= $charBin;
	    }
    	return chunk_split($binaryOutput, 8, ' ');
	}

	function makeHEX($name){
		return strtoupper(chunk_split(bin2hex($name), 2, " "));
	}
	function makeFjortis($name){
		for($i = 0; $i < strlen($name); $i = $i + 2){
			$name[$i] = strtoupper($name[$i]);
		}
		return $name;		
	}
	function makeLeetSpeak($name){
		$name = str_replace('a','4', $name);
		$name = str_replace('A','4', $name);
		$name = str_replace('e','3', $name);
		$name = str_replace('E','3', $name);
		$name = str_replace('i','1', $name);
		$name = str_replace('I','1', $name);
		$name = str_replace('o','0', $name);
		$name = str_replace('O','0', $name);
		$name = str_replace('ks','xx', $name);
		$name = str_replace('KS','xx', $name);
		$name = str_replace('s','5', $name);
		$name = str_replace('S','5', $name);
		$name = str_replace('t','7', $name);
		$name = str_replace('T','7', $name);
		$name = str_replace('ö','oe', $name);
		$name = str_replace('Ö','OE', $name);
		$name = str_replace('ä','ae', $name);
		$name = str_replace('Ä','AE', $name);
		return $name;
	}
	function makeRovarSprak($name){
		$name = strtolower($name);
		$result = NULL;
		$vokaler = array('a','e','i','o','u','y','å','ä','ö', '.', '!', '?', ',', ' ');
		for($i = 0; $i < strlen($name); $i++){
			if(in_array($name[$i],$vokaler)){
				$result .= $name[$i];
			}
			else{
				$result .= $name[$i] . 'o' . $name[$i];
			}
		}
		return ucfirst($result);
	}
	function makeBetydelse($name){
		$result = mysql_query('SELECT betydelse FROM names WHERE namn LIKE "' . $name . '" LIMIT 1');
		if(mysql_num_rows($result) == 0){
			return 'Tyvär, ditt namn fanns inte i databasen.';
		}
		else{
			$data = mysql_fetch_assoc($result);
			return $data['betydelse'];
		}
	}
function makeMorse($name){
	$name = strtolower($name);
    $name = str_replace('a', '.- ', $name);
    $name = str_replace('b', '-... ', $name);
    $name = str_replace('c', '-.-. ', $name);
    $name = str_replace('d', '-.. ', $name);
    $name = str_replace('e', '. ', $name);
    $name = str_replace('f', '..-. ', $name);
    $name = str_replace('g', '--. ', $name);
    $name = str_replace('h', '.... ', $name);
    $name = str_replace('i', '.. ', $name);
    $name = str_replace('j', '.--- ', $name);
    $name = str_replace('k', '.-. ', $name);
    $name = str_replace('l', '.-.. ', $name);
    $name = str_replace('m', '-- ', $name);
    $name = str_replace('n', '-. ', $name);
    $name = str_replace('o', '--- ', $name);
    $name = str_replace('p', '.--. ', $name);
    $name = str_replace('q', '--.- ', $name);
    $name = str_replace('r', '.-. ', $name);
    $name = str_replace('s', '... ', $name);
    $name = str_replace('t', '- ', $name);
    $name = str_replace('u', '..- ', $name);
    $name = str_replace('v', '...- ', $name);
    $name = str_replace('w', '.-- ', $name);
    $name = str_replace('x', '-..- ', $name);
    $name = str_replace('y', '-.-- ', $name);
    $name = str_replace('z', '--.. ', $name);
    $name = str_replace('1', '.---- ', $name);
    $name = str_replace('2', '..--- ', $name);
    $name = str_replace('3', '...-- ', $name);
    $name = str_replace('4', '....- ', $name);
    $name = str_replace('5', '..... ', $name);
    $name = str_replace('6', '-.... ', $name);
    $name = str_replace('7', '--... ', $name);
    $name = str_replace('8', '---.. ', $name);
    $name = str_replace('9', '----. ', $name);
    $name = str_replace('0', '----- ', $name);
    $name = str_replace(' ', '    ', $name);
		return $name;
}

function makePhoneticConvert($name){    
	switch ($name) {
		case 'a' :
			$name = 'Alpha ';
	    	break;
		case 'b' :
  			$name = 'Bravo ';
   		 	break;
		case 'c' :
        	$name = 'Charlie ';
        	break;
    	case 'd' :
    	    $name = 'Delta ';
    	    break;
    	case 'e' :
        	$name = 'Echo ';
        	break;
		case 'f' :
        	$name = 'Foxtrot ';
       		break;
    	case 'g' :
        	$name = 'Golf ';
        	break;
   		case 'h' :
        	$name = 'Hotel ';
        	break;
  	 	case 'i' :
     	    $name = 'India ';
        	break;
   		case 'j' :
        	$name = 'Juliet ';
        	break;
    	case 'k' :
        	$name = 'Kilo ';
        	break;
    	case 'l' :
        	$name = 'Lima ';
        	break;
    	case 'm' :
        	$name = 'Mike ';
        	break;
    	case 'n' :
        	$name = 'November ';
        	break;
    	case 'o' :
        	$name = 'Oscar ';
        	break;
    	case 'p' :
        	$name = 'Papa ';
        	break;
    	case 'q' :
        	$name = 'Quebec ';
        	break;
    	case 'r' :
        	$name = 'Romeo ';
        	break;
    	case 's' :
        	$name = 'Sierra ';
        	break;
    	case 't' :
        	$name = 'Tango ';
        	break;
    	case 'u' :
        	$name = 'Uniform ';
        	break;
    	case 'v' :
        	$name = 'Victor ';
        	break;
    	case 'w' :
        	$name = 'Whiskey ';
        	break;
    	case 'x' :
        	$name = 'X-ray ';
        	break;
    	case 'y' :
        	$name = 'Yankee ';
        	break;
    	case 'z' :
        	$name = 'Zulu ';
        	break;
}
	return $name;
}

function makePhonetic($name){
	$stringarray = str_split(strtolower($name));

	for ($i = 0; $i < count($stringarray); $i++) {
		$temp .= makePhoneticConvert($stringarray[$i]);
	}
	
	return $temp;
}

function getAllaBarnen($name){
	$result = mysql_query('SELECT joke FROM humorarkiv WHERE joke LIKE "%förutom ' . $name . '%" LIMIT 1');
	if(mysql_num_rows($result) == 0){ return 'Ingen historia med ditt namn hittades :('; }
	else{
		$data = mysql_fetch_assoc($result);
		return $data['joke'];
	}
}

echo rounded_corners_top();
?>
<h1>Ditt namn</h1>
<p class="intro">
Folk tycker det är jätteroligt att skriva sina namn på olika sätt och att hitta på betydelser för bokstäverna, så i brist på annat att göra har vi kodat
ett litet script som gör att detta åt dig, håll till godo!
</p>
<h2>Vad heter du?</h2>
<div>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="text" name="name" style="width: 250px;" class="textbox" />&nbsp;<input type="submit" value="Konvertera" class="button_80" />
</form>
<br /><br />
Fyll bara i ditt namn och tryck på "Konvertera" så får du ditt namn skrivet på flera olika sätt, just nu finns:<br />
<b>Binärt, HEX, Fjortis-text, LeetSpeak, Rövarspråket, Betydelsen, MD5, Fonetiskt</b>
</p>
</div>

<?php
	if(isset($_POST['name'])){
		echo '<p class="subtitle">Resultat</p>';
		echo '<div>';

		echo '<i>Ditt namn skrivet med <b>ettor och nollor:</b></i><BR>' . makeBinary($_POST['name']) . '<BR><BR>';
		echo '<i>Ditt namn skrivet i <b>HEX:</b></i><BR>' . makeHEX($_POST['name']) . '<BR><BR>';
		echo '<i>Ditt namn skrivet med <b>fjortis-text:</b></i><BR>' . makeFjortis($_POST['name']) . '<BR><BR>';
		echo '<i>Ditt namn skrivet med <b>leetspeak:</b></i><BR>' . makeLeetSpeak($_POST['name']) . '<BR><BR>';
		echo '<i>Ditt namn skrivet på <b>rövarspråket:</b></i><BR>' . makeRovarSprak($_POST['name']) . '<BR><BR>';	
		echo '<i>Ditt namns <b>betydelse:</b></i><BR>' . makeBetydelse($_POST['name']) . '<BR><BR>';
		echo '<i>Ditt namn krypterat med <b>MD5:</b></i><BR>' . md5($_POST['name']) . '<BR><BR>';
		echo '<i>Ditt namn skrivet med <b>morse-kod:</b></i><BR>' . makeMorse($_POST['name']) . '<BR><BR>';
		echo '<i>Ditt namn skrivet med det <b>fonetiska alfabetet:</b></i><BR>' . makePhonetic($_POST['name']) . '<BR><BR>';
		echo '<i><b>Alla Barnen-historia</b> med ditt namn:</i><BR>' . getAllaBarnen($_POST['name']) . '<BR><BR>';
		echo '<i>Ditt namn skrivet <b>baklänges</b>:</i><br />' . strrev($_POST['name']) . '<br /><br />';
		echo '</div>';
	}
echo rounded_corners_bottom();

	ui_bottom();
?>
