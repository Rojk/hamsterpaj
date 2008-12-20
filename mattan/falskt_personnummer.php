<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('mattan', 'personnummer');
	$ui_options['javascripts'] = array('scripts.js');
	$ui_options['admtoma_category'] = 'other.false_ssn';
	ui_top($ui_options);
	echo rounded_corners_top(array('color' => 'orange'));
?>

<script>
	function make_ssn()
	{
		var even_numbers = Array(0,2,4,6,8);
		var odd_numbers = Array(1,3,5,7,9);
		
		var ssn_string = document.getElementById('ssn_year').value.toString();
		ssn_string += document.getElementById('ssn_month').value.toString();
		ssn_string += document.getElementById('ssn_day').value.toString();
		ssn_string += (10+Math.round(90*Math.random())).toString();
		if(document.getElementById('ssn_gender').value == 'M')
		{
			ssn_string += odd_numbers[Math.round(4*Math.random())].toString();
		}
		else
		{
			ssn_string += even_numbers[Math.round(4*Math.random())].toString();
		}
		
		var multiplied = '';
		for(var i = 0; i <= 8; i++)
		{
			if(i == 0 || i == 2 || i == 4 || i == 6 || i == 8)
			{
				multiplied += (ssn_string.substr(i, 1) * 2).toString();
			}
			else
			{
				multiplied += ssn_string.substr(i, 1).toString();
			}
		}
		var multiplied_sum = 0;
		for(var i = 0; i < multiplied.length; i++)
		{
			multiplied_sum += multiplied.substr(i, 1)*1;
		}
		if(multiplied_sum.toString().substr(-1, 1) == 0)
		{
			check_digit = 0;
		}
		else
		{
			check_digit = 10 - multiplied_sum.toString().substr(-1, 1);
		}
		ssn_string = ssn_string.substr(0, 6) + '-' + ssn_string.substr(6, 3).toString() + check_digit.toString();
		document.getElementById('ssn_result').innerHTML = ssn_string;	
	}
</script>
  <h1 style="margin-top: 0;">Falskt personnummer!</h1>
		<div style="float: left; width: 80px;">
			<h3>År</h3>
			<select id="ssn_year" onchange="make_ssn();">
			<?php
				for($i = 1910; $i < 2010; $i++)
				{
					if($i == 1990)
					{
						echo '<option value="' . substr($i, -2) . '" selected="true">' . $i . '</option>' . "\n";
					}
					else
					{
						echo '<option value="' . substr($i, -2) . '">' . $i . '</option>' . "\n";
					}
				}
			?>
		</select>
		</div>
		<div style="float: left; width: 80px;">
			<h3>Månad</h3>
			<select id="ssn_month" onchange="make_ssn();">
			<?php
			for($i = 1; $i <= 12; $i++)
			{
				$i = ($i < 10) ? '0' . $i : $i;
				echo '<option value="' . $i . '">' . $i . '</option>' . "\n";
			}
			?>
		</select>
		</div>
		<div style="float: left; width: 80px;">
			<h3>Dag</h3>
			<select id="ssn_day" onchange="make_ssn();">
			<?php
			for($i = 1; $i <= 31; $i++)
			{
				$i = ($i < 10) ? '0' . $i : $i;
				echo '<option value="' . $i . '">' . $i . '</option>' . "\n";
			}
			?>
		</select>
		</div>
		<div style="float: left; width: 80px;">
			<h3>Kön</h3>
			<select id="ssn_gender" onchange="make_ssn();">
				<option value="M">Kille</option>
				<option value="F">Tjej</option>
			</select>
		</div>
		<div style="float: left;">
			<h3>Personnummer</h3>
				<div id="ssn_result" onclick="make_ssn();" style="background: #fbd291; border: 1px solid #bd9d6b; width: 80px; height: 14px; padding: 3px; float: left; margin-right: 5px;">
					
				</div>
				<input type="button" class="button_140" value="Slumpa nytt nummer &raquo;" onclick="make_ssn();" />
		</div>
		<br style="clear: both;" />
<?php
echo rounded_corners_bottom();
echo rounded_corners_top(array('color' => 'blue'));
?>

<style>
	pre
	{
		display: block;
		margin: 0px;
	}	
</style>
<h2 style="margin-top: 0;">Vill du veta hur det fungerar?</h2>
	Skattemyndigheten har givit ut en intressant <a href="http://www.skatteverket.se/download/18.b7f2d0103e5e9ecb0800074/70407.pdf" target="_blank">broschyr</a> om hur svenska personnummer är uppbyggda. Vi har skrivit ihop en kort guide för hur du själv förfalskar personnummer här nedan om du inte orkar läsa texten från skattemyndigheten.
	<br /><br />
	Börja med att välja födelseår, månad och dag:
	<pre>1987 06 14</pre>
	Plocka bort de två första siffrorna i årtalsnummret:
	<pre>8 7 0 6 1 4</pre>
	Välj två slumpade siffror:
	<pre>8 7 0 6 1 4 - 3 8</pre>
	Välj en till slumpad siffra, den ska vara <strong>jämn för tjejer</strong> och <strong>ojämn för killar</strong>:
	<pre>8 7 0 6 1 4 - 3 8 4</pre>
	Skriv 212121212 under siffrorna:
	<pre>8 7 0 6 1 4 - 3 8 4
2 1 2 1 2 1 - 2 1 2</pre>
	Multiplicera sedan siffra för siffra:
	<pre>8  7  0  6  1  4  -  3  8  4
2  1  2  1  2  1  -  2  1  2
-------------------------------------
16 7  0  6  2  4  -  6  8  8 
	</pre>
	Lägg sedan ihop summan <strong>siffra för siffra</strong>:
	<pre>1+6+7+0+6+2+4+6+8+8=48</pre>
	plocka bort tiotalssiffran, den till <strong>vänster</strong>:
	<pre>8</pre>
	Ta nu talen <strong>10</strong> och dra bort siffran du har kvar:
	<pre>10-8=2</pre>
	Om du skulle få uträkningen 10-0 så använder du 0 som svar och inte 10.. Alltså, 10-0=0<br />
	Lägg nu siffran du har kvar efter det nummret du hittade på:<br />
	<pre>87 06 14 - 38 42</pre>
	<br />
	Nu har du ett falskt personnummer som kommer fungera på diverse communities på Internet och mängder med olika ålderskontroller! Testa gärna att göra detta med ditt egna personnummer, du kan räkna ut sista siffran själv!

<?php
echo rounded_corners_bottom();
echo rounded_corners_top(array('color' => 'red'));
?>
<h2 style="margin-top: 0;">Vad säger lagen om detta?</h2>
	Det är förbjudet att använda sitt personnummer för att utge sig för att vara någon annan.<br />
	Å andra sidan känner vi inte till något fall där typ Lunarstorm har polisanmält någon för att ha försökt dölja sin ålder genom att köra ett falskt personnummer.<br />
	Hursom, risken är er och vi uppmanar er att vara snälla gossar och töser, vi tar inget ansvar för vad ni gör...<br />
	Vi gör i vilket fall inget olagligt, nummren skapas i din webbläsare och inte på vår server, vi ser aldrig personnummren du skapar.

<?php
echo rounded_corners_bottom();
	ui_bottom();
?>


