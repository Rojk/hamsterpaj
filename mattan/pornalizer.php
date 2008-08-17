<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('mattan', 'pornalizer');
	$ui_options['title'] = 'Pornalizer';
	ui_top($ui_options);

	$male = array(
		"Hoss Hardcore", 
		"Alan Asslap", 
		"Pete Pleasure", 
		"Tommy Tosser", 
		"Slappy Sexbomb", 
		"Woody Longbone", 
		"Steve Stiffbone", 
		"Woody Wanker",
		"Jamie Jerkoff", 
		"Billy Boner", 
		"Harry Hardon", 
		"Larry Lovebone", 
		"Doggie Daddy", 
		"Nick Naughty", 
		"Luke Longbone",
		"Micky Meat",
		"Kevin Kinky",
		"Willy Vitale",
		"Jack Jizzpump",
		"Randy Rockstiff",
		"Lance Lovepump",
		"Jeremy Jizz",
		"Tony Tounge",
		"Ben Dover",
		"Lorenzo Lovegun",
		"Steve Stiffler",
		"Scott Sexpump",
		"Randy Rockhard",
		"Casey Cummings",
		"Benny Balls",
		"William Wood",
		"Paulie Pecker",
		"Carl Cumsalot",
		"Jerry Jerksalot",
		"Hank Horny",
		"Jimmy Jammer",
		"Nolan Nastee",
		"Nikki Nuttz",
		"Will Wankalot",
		"Danny Doggy",
		"Harry Hotbone",
		"Henri Hornee",
		"Sly Steeldong",
		"Barry Blowme",
		"Nicky Lovecolt",
		"Randy Rockhard",
		"Joey Rambone",
		"Ricky Lovenuts",
		"Terry Titfuck",
		"Mike Meatmaster",
		"Daddy Lovebone",
		"Jimmy Jizzball",
		"Seargent Sexy",
		"Major Meatbone",
		"Private Pecker",
		"Buck Naked",
		"Jake Jizzpump",
		"Ramone Lovegod",
		"Antonio Assman",
		"Harry Humper",
		"Clint Meatwood",
		"Tom Spanks",
		"Master Bater",
		"Bruce Ballslap",
		"David Ducockny",
		"Denzel Wankington",
		"Nikki Lovegod",
		"Seargent Love",
		"Kurt Kinky",
		"Tony Longbone",
		"Henry Hardcore",
		"Stevie Stiff",
		"Franky Fuckbone",
		"Billy Steeldong",
		"Doctor Hardlove",
		"Ronnie Rammer",
		"Lee Lovetounge",
		"Clive Cockster",
		"Sly Slapalot",
		"Woody Humpalot",
		"Benny Backdoor",
		"Willy Jizznuts",
		"Major Steelbone",
		"Homer Hardon",
		"Billy Buttlove",
		"Lee Longstroke",
		"Micky Hotlove",
		"Jake Jammer",
		"Scott Loveshot",
		"Kelly Kumshot",
		"Antonio Assram"	
	);
	
	$female = array(
		"Lucy Lovelips", 
		"Linda Luscious", 
		"Tracy Tounge", 
		"Susy Sexdoll", 
		"Barbara Bush", 
		"Fanny Fantasy", 
		"Candy Cumms", 
		"Debbie Desire", 
		"Vendy Venus", 
		"Stacy Sexlips", 
		"Barbie Bottom", 
		"Baby Lovedoll", 
		"Sandra Silkpuss", 
		"Linda Lovefoxx", 
		"Lusty Lovedoll", 
		"Sandy Sextoy", 
		"Anna Anales", 
		"Bonny Blew", 
		"Kandy Kupps", 
		"Daniela Deep", 
		"Chesty Cumms", 
		"Raven Roxx", 
		"Penny Passion", 
		"Lolita Lovehole", 
		"Foxy Fuckdoll", 
		"Gina G-String", 
		"Dee Dee Desire", 
		"Nikkie Nipple", 
		"Pammy Petlove", 
		"Daphne Doggy", 
		"Laura Lovebite", 
		"Clitty Cummings", 
		"Brandy Buttplug", 
		"Debbie Dildo", 
		"Fiona Fineass", 
		"Tanya Thong", 
		"Deborah D-cup", 
		"Natasha Naughty", 
		"Lorissa Lick", 
		"Wendy Wild", 
		"Clarissa Crotch", 
		"Sindee Sugarhole", 
		"Daizy Delight", 
		"Vivian Vamp", 
		"Lolita Lovedoll", 
		"Lilly Lovedoll", 
		"Nina Nutz",
		"Fanny Fellatio", 
		"Cunnie Lingus", 
		"Brandy Foxhole", 
		"Linda Hotlick", 
		"Penny Silkhole", 
		"Baby Lovedoll",
		"Tonya Toydoll", 
		"Sandy Suckbone", 
		"Crystal Cummings",
		"Honey Horny", 
		"Nikky Foxhole", 
		"Daizy Hotbite", 
		"Bambi Buttplug", 
		"Lucy Lovehole", 
		"Sandy Sugarlips", 
		"Debby Silkhole", 
		"Candie Toydoll", 
		"Tasty Tereza", 
		"Lorissa Lovebomb", 
		"Fiona Fucktoy", 
		"Alotta Fagina", 
		"Priscilla Pleasure", 
		"Ginger G-point", 
		"Tanita Tounge", 
		"Lula Lovelips", 
		"Sindy Sinn", 
		"Sandra Sinfox", 
		"Kinky Kitten", 
		"Sexy Salina",
		"Debbie Doggie", 
		"Bambi Nipple", 
		"Nikkie Pleasure", 
		"Chesty Cheeks", 
		"Cunnie Bottom", 
		"Titty Toungelick", 
		"Ivana Humpalot", 
		"Stacy Hotlick", 
		"Dina Dongride", 
		"Wendy Wantone", 
		"Daniella Vanella", 
		"Dani Deepthroat", 
		"Baby Lovepet", 
		"Venus Willing", 
		"Sandra Sexlips"
	);

	if(isset($_GET['name']))
	{
		$str = strtolower($_GET['name']);	
		for($i = 0; $i < strlen($str); $i++)
		{
			$sum += ord($str{$i});	
		}
	
		$name_id = $sum%90;
	
		echo '<div style="text-align: center; margin-bottom: 50px;">' . "\n";
		if($_GET['gender'] == 'f')
		{
			echo 'Om<h1>' . ucwords($str) . '</h1>hade gjort porr hade hon hetat<h1>' . $female[$name_id] . '</h1>';	
		}
		else
		{
			echo 'Om<h1>' . ucwords($str) . '</h1>hade gjort porr hade han hetat<h1>' . $male[$name_id] . '</h1>';
		}
		echo '</div>' . "\n";
	}
	
	echo '<h2 style="margin-bottom: 10px;">Vad hade du hetat som porrstjärna?</h2>' . "\n";
	echo '<form>' . "\n";
	echo '<h4>Namn:</h4>' . "\n";
	echo '<input type="text" name="name" class="textbox" /><br />' . "\n";
	echo '<input type="radio" name="gender" value="m" id="pornalizer_m" checked="true" />' . "\n";
	echo '<label for="pornalizer_m">Kille</label><br />' . "\n";
	echo '<input type="radio" name="gender" value="f" id="pornalizer_f" />' . "\n";
	echo '<label for="pornalizer_f">Tjej</label><br />' . "\n";
	echo '<input type="submit" value="Pornalize" />' . "\n";
	echo '</div>' . "\n";
?>
<h2>Shit, moralpanik!</h2>
<p style="font-style: italic;">
	- Hur kan en sida som riktar sig till <strong>barn</strong> hålla på med <strong>porr</strong>? Det måste ju betyda <strong>pedofiler</strong> och att
	de stackars kidsen får <strong>skev uppfattning om sex</strong>!
</p>
<p>
	Lugna ner sig ett par hekto nu. Hamsterpaj riktar sig till <em>ungdomar</em> som är minst tretton år. Att det dyker upp "Bruce Ballslap" när man fyller i "Emil" är ungefär
	lika allvarligt som att rektorn i Simpsons heter <em>Seymore Butts</em>.
</p>

<?php
	ui_bottom();
?>


