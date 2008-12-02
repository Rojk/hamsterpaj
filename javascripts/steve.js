			function steve_gun_trigger(e)
			{
				var posX = e.pageX;
				var posY = e.pageY;
				//alert('Pow! ' + posX + ' ' + posY);
				var gun_hole = document.createElement('img');
				gun_hole.src = 'http://images.hamsterpaj.net/steve/bullet_hole.png';
				document.getElementById('gun_div').appendChild(gun_hole);
				gun_hole.style.display = 'block';
				gun_hole.style.position = 'absolute';
				
				gun_hole.style.top = posY + 'px';
				gun_hole.style.left = posX + 'px';
			}
			
			function steve_shot()
			{
				alert('Aaargh!');
				window.location = '/rip_steve.php';
			}
			
			function steve_gun()
			{

alert('Snälla, låt mig leva, jag kidnappa inte hamstern!');

			/*	alert('Snälla, låt mig leva, jag har inget ont gjort!');
			*/
				document.body.style.cursor = 'url("http://images.hamsterpaj.net/steve/sniper.png"), crosshair';
				var overlay_div = document.createElement('div');
				overlay_div.id = 'gun_div';
				overlay_div.style.position = 'absolute';
				overlay_div.style.width = '100%';
				overlay_div.style.height  = '5000px';
				overlay_div.style.margin = '-10px';
				overlay_div.onclick = steve_gun_trigger;
				document.body.appendChild(overlay_div);
				document.getElementById('steve').style.display = 'none';
				document.getElementById('steve_gun').style.display = 'none';
				
				
				overlay_div.innerHTML = '<DIV ID="flying_steve" STYLE="position:absolute; left: -500px; width:47; height:68;"><IMG SRC="http://images.hamsterpaj.net/steve/steve.gif" BORDER=0 onclick="steve_shot();"></DIV>';
				
				flying_steve = new Chip("flying_steve",47,68);
				movechip("flying_steve");
			}
			
function steve_activate()
{
	if(document.getElementById('steve'))
	{
	 	document.getElementById('steve').onmousedown = steve_mousedown;
	 	document.getElementById('steve').onmouseup = steve_mouseup;
	}
	if(document.getElementById('steve_gun'))
	{
	 	document.getElementById('steve_gun').onclick = steve_gun;		
	}
}

var steve_long_click = false;
var steve_mouse = false;

function steve_mousedown()
{
	steve_long_click = false;
	steve_mouse = true;
	setTimeout('steve_click_check()', 1500);
}

function steve_click_check()
{
	if(steve_mouse == true)
	{
		alert('Oh shit, jordbävning!');
		stop_start_wave_effect();
		steve_long_click = true;
	}
}


function buzz()
{
		alert('Oh shit, jordbävning!');
		stop_start_wave_effect();
}

function steve_mouseup()
{
	steve_mouse = false;
	if(steve_long_click == false)
	{
		var steve_comments = Array();
	
	 steve_comments[0] = 'Var e min kompis?';
/*		steve_comments[0] = 'Hamsterpaj startades i Oktober 2003';
		steve_comments[1] = 'Mitt namn är Steve, och jag är importerad från den gamla webbsiten megadomain';
		steve_comments[2] = 'Hamsterpaj består av över tjugo tusen rader programkod';
		steve_comments[3] = '.... . .- ...- . -. /  .. ... /  .- /  .--. .-.. .- -.-. . /  --- -. /  . .- .-. - .... ';
		steve_comments[4] = 'Hamsterpajs första server stog på en balkong, hade en överklockad processor på 700mhz och hela 384mb i RAM.';
		steve_comments[5] = 'Vår fina webbsite har bott i Göteborg, USA, Holland och Östersund';
		steve_comments[6] = 'Namnet hamsterpaj kommer från ordet MUSTERAPI som stog skrivet på tavlan när klass 1A började på Portalens Gymnasium under hösten 2003';
		steve_comments[7] = 'Adolf Hitler hade bara en pungkula';
		steve_comments[8] = 'I Norge heter han inte stålmannen, han heter Metallgutten!';
		steve_comments[9] = 'Det visade sig att dom på nåt vänster hade rostat Lennart och lagt honom i en liten brun kruka som dom skulle gräva ner';
		steve_comments[10] = 'När Gun ringde lite senare lät hon lite som en pnenumatisk borrannordning';
		steve_comments[11] = 'Jag har haft baksug i mitt köksavlopp, så nu är tapeten brun';
		steve_comments[12] = 'Rökare är också människor, fast inte lika länge';
		steve_comments[13] = 'Vad är volymen av en pizza om radien är z och höjden a? Svar: Pi z z a';
		steve_comments[14] = 'Du måste låta henne slappna av, komma i rätt stämning, först då kan... (Henrik under arbetet)';
		steve_comments[15] = 'I Ungern är snorkråkor fika.';
		steve_comments[16] = '2001 försökte Honda lansera bilen Honda Fitta i Sverige. Så småningom bytte de namn till Honda Jazz.';
		steve_comments[17] = 'Alzheimers - nya vänner varje dag!';
		steve_comments[18] = 'Jag försov mig den dagen det delades ut hjärnor';		
		steve_comments[19] = 'Sett på vodkaflaskan: Bäst före: Dagen efter';
		steve_comments[20] = 'En del lejon parar sig upp till 50 gånger om dagen';
		steve_comments[21] = 'Råttor kan inte kräkas';
		steve_comments[22] = 'Fladdermusen är det enda däggdjur som kan flyga';
		steve_comments[23] = 'Geparden är det enda kattdjuret som inte kan gömma sina klor';
		steve_comments[24] = 'Lössen kan hoppa 350 gånger sin kroppslängd';
		steve_comments[25] = 'En termitsdrottning kan leva i 50 år och avla fram 30000 tusen termiter varje dag';
		steve_comments[26] = 'Om en guldfisk hålls i ett mörkt rum så blir den vit';
		steve_comments[27] = 'Den genomsnittliga husflugan lever endast i två veckor';
		steve_comments[28] = 'Isbjörnens päls är egentligen inte vit utan transparent';
		steve_comments[29] = 'En ekorre äter 40000 tallkottar varje år';
		steve_comments[30] = 'Sniglar kan sova i fem år';
		steve_comments[31] = 'En blåvals hjärta slår endast 9 gånger per minut';
		steve_comments[32] = 'Kolibrin är den enda fågeln som kan flyga baklänges';
		steve_comments[33] = 'Fiskfjäll används i läppstift';
		steve_comments[34] = 'Färska ägg flyter inte i vatten men det gör gamla ägg';
		steve_comments[35] = 'En mulåsna flyter på kvicksand men det gör inte en åsna';
		steve_comments[36] = 'Man kan göra 11 omeletter på ett stutsägg';
		steve_comments[37] = 'Den senaste gången det snöade i Saharaöknen var den 18 februari 1979';
		steve_comments[38] = 'Ingen vet var Mozart är begraven';
		steve_comments[39] = 'Charles Lindbergh hade endast 4 smörgåsar med sig när han flög över Atlanten';
		steve_comments[40] = 'Det finns över 300 olika typer av honung';
		steve_comments[41] = 'Beethoven blötte sitt hår innan han skulle komponera musik';
		steve_comments[42] = 'Hjärnskador börjar uppstå redan vid kroppstemperaturer på 40,5 grader';
		steve_comments[43] = 'Anne Boleyn hade tre bröst';
		steve_comments[44] = 'Brandlarmet uppfanns 1969';
		steve_comments[45] = 'Adolf Hitlers mamma blev övertygad av sin husläkare att inte göra abort';
		steve_comments[46] = 'Nästan 25% av jordens befolkning lever i Kina';
		steve_comments[47] = 'När månen står som högst så väger du något mindre';
		steve_comments[48] = 'Världshistoriens kortaste krig utspelades år 1896 mellan Zanzibar och Storbritannien och varade i hela 38 minuter';
		steve_comments[49] = 'Om man skulle rada upp alla röda blodkroppar som du har i kroppen så skulle ledet räcka 2,5 varv runt jorden';
		steve_comments[50] = 'När man nyser så avstannar alla kroppsfunktioner t.o.m. hjärtat';
		steve_comments[51] = 'En nyfödd har 300 st ben i kroppen men en vuxen bara 206 st';
		steve_comments[52] = 'Hjärnskador börjar uppstå redan vid kroppstemperaturer på 40,5 grader';
		steve_comments[53] = 'Joar: Ligger inte Sarajevo i lappland?';
		steve_comments[54] = 'Lef-91: Varför är det så svårt att få upp saker ibland?';
		steve_comments[55] = 'Felstvaningarna är en del av skärmen med Hamsterpaj';
*/	
		var quote = Math.round(Math.random()*(steve_comments.length-1));

		alert(steve_comments[quote]);
	}
}

womAdd('steve_activate()');
