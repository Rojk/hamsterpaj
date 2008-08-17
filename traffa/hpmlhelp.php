<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>HPML</title><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="http://www.hamsterpaj.net/include/style.css" rel="stylesheet" type="text/css">
</head><body>
<div id="main" style="width: 700px;">
<p class="title">HPML - Såhär sminkar du din presentation</p>
<p class="intro">Hamsterpaj Markup Language är en uppsättning <i>taggar</i> du kan använda för att "sminka" din presentation.
Alla <i>taggar</i> måste avslutas med en avslutningstagg, annars fungerar det inte.</p>

<p class="subtitle">Enkla textegenskaper</p>
<div id="contentPostBox" style="width: 99%">
&lt;fet&gt;Hejsan&lt;/fet&gt;<br />
Ger: <b>Hejsan</b><br /><br />
&lt;kursiv&gt;Hejsan&lt;/kursiv&gt;<br />
Ger: <i>Hejsan</i><br /><br />
&lt;understruket&gt;Hejsan&lt;/understruket&gt;<br />
Ger: <u>Hejsan</u><br /><br />
&lt;centrera&gt;Hejsan!&lt;/centrera&gt;<br />
Ger: <center>Hejsan!</center><br />
&lt;upphöjt&gt;Hejsan&lt;/upphöjt&gt;<br />
Ger: <sup>Hejsan</sup><br /><br />
&lt;fet&gt;Hej! &lt;kursiv&gt;jag&lt;/kursiv&gt; &lt;understruket&gt;heter &lt;/fet&gt;&lt;upphöjt&gt;Emma&lt;/upphöjt&gt;&lt;/understruket&gt;<br />
Ger: <b>hej! <i>jag</i> <u>heter </b><sup>Emma</sup></u>
</div>

<p class="subtitle">Avancerade textegenskaper</p>
<p class="intro">Observera att dessa skrivs inuti samma tagg, och att alla egenskaper avslutas samtidigt.</p>
<div id="contentPostBox" style="width: 99%">
Storlek: <span style="font-size: 9px;">liten</span>, normal, stor<br />
Färg: <b><font color="red">röd</font>, <font color="green">grön</font>, <font color="blue">blå</font>, <font color="white">vit</font>, <font color="black">svart</font>, <font color="brown">brun</font>, <font color="orange">orange</font>, <font color="BlueViolet">lila</font>, <font color="grey">grå</font>.</b><br />
Stilar: <br />
<b>1</b> <span style="font-family: comic sans ms">Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit</span><br />
<b>2</b> <span style="font-family: trebuchet ms,verdana,arial,helvetica,sans-serif;">Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit</span><br />
<b>3</b> <span style="font-family: arial,helvetica,sans-serif;">Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit</span><br />
<b>4</b> Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit<br />
<br />
<b>Exempel:</b><br />
&lt;färg=röd storlek=liten stil=1&gt;Hejsan!&lt;/färg&gt;<br />
Ger: <span style="font-size: 9px; color: red; font-family: comic sans ms;">Hejsan!</span><br /><br />
&lt;färg=orange storlek=stor stil=2&gt;Hejsan!&lt;/färg&gt;<br />
Ger: <span style="font-size: 25px; color: orange; font-family: arial,helvetica,sans-serif;">Hejsan!</span><br /><br />
</div>


<p class="subtitle">Boxar</p>
<p class="intro">Det mest avancerade är boxarna. Det är många egenskaper att hålla ordning på!</p>
<div id="contentPostBox" style="width: 99%">
<table class="body">
<tr><td>Bredd:</td><td>0-500</td></tr>
<tr><td>Höjd:</td><td>0-1000</td></tr>
<tr><td>Bakgrund:</td><td>Färg: <b><font color="red">röd</font>, <font color="green">grön</font>, <font color="blue">blå</font>, <font color="white">vit</font>, <font color="black">svart</font>, <font color="brown">brun</font>, <font color="orange">orange</font>, <font color="BlueViolet">lila</font>, <font color="grey">grå</font></b> eller ett bildnummer.</td></tr>
<tr><td>Kant:</td><td> solid, prickad, streckad</td></tr>
<tr><td>Kantfärg:</td><td> <b><font color="red">röd</font>, <font color="green">grön</font>, <font color="blue">blå</font>, <font color="white">vit</font>, <font color="black">svart</font>, <font color="brown">brun</font>, <font color="orange">orange</font>, <font color="BlueViolet">lila</font>, <font color="grey">grå</font>.</b></td></tr>
<tr><td>Kantbredd:</td><td> 0-10</td></tr>
</table>
<b>Exempel:</b><br />
&lt;box bakgrund=grå kant=solid kantfärg=röd kantbredd=5&gt;Hejsan!&lt;/box&gt;<br />
Ger:
<div style="border:5px solid red; padding: 2px; background: grey;">Hejsan!</div>
<br />
&lt;box bakgrund=3 kant=prickad kantfärg=svart kantbredd=10&gt;
&lt;storlek=stor&gt; &lt;centrera&gt;Välkommen! &lt;/centrera&gt;&lt;/storlek&gt;
&lt;/box&gt;<br />
Ger:
<div style="border: 10px solid black; padding: 2px; background: url(/images/traffabgs/3.png);"> <span style="margin: 2px; font-size: 25px;"><center>Välkommen!</center></span> </div>
<br />
&lt;box bakgrund=orange bredd=300 kantbredd=0&gt;<br />
&amp;nbsp;98: $strCam = '&lt;span style="color: #CC0000"&gt;Nej&lt;/span&gt;';<br />
&amp;nbsp;99: }<br />
100: if(strlen($traffaDefHaircolors[$traffaProfile['haircolor']]) &gt; 0){<br />
101: $haircolor = $traffaDefHaircolors[$traffaProfile['haircolor']];<br />
102: }<br />
&lt;/box&gt;
<br /><br />
Ger:<br />
<div style="background: orange; border: 0; width: 400px;">
&nbsp;98: $strCam = '&lt;span style="color: #CC0000"&gt;Nej&lt;/span&gt;';<br />
&nbsp;99: }<br />
100: if(strlen($traffaDefHaircolors[$traffaProfile['haircolor']]) &gt; 0){<br />
101: $haircolor = $traffaDefHaircolors[$traffaProfile['haircolor']];<br />
102: }
</div>
</div>

<p class="subtitle">Profillänkning</p>
<p class="intro">&lt;användare&gt; använder man för att länka till andras profiler. Skriv &lt;användare&gt;Användarnamn&lt;/användare&gt;</p>
<div id="contentPostBox" style="width: 99%">
<b>Exempel:</b><br />
Besök &lt;användare&gt;Heggan&lt;/användare&gt;s profil<br /><br />
Ger: Besök <a href=/traffa/quicksearch?username=Heggan>Heggan</a>s profil<br /><br />
</div>

<p class="subtitle">Bra att veta</p>
<div id="contentPostBox" style="width: 99%;">
Om du vill göra mer än ett mellanslag i rad så kan du skriva &amp;nbsp; istället för mellanslag.<br />
Inga HTML-taggar är tillåtna av säkerhetsskäl.<br />
Vi i Crew hjälper inte till med sminkning av presentationer.
</div>

</div></body></html>


