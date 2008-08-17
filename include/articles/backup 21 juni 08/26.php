<?php
if($_SESSION['userinfo']['forum_course_1'] == 1)
{
?>
<div class="green_faded_div">
<h2>Du är godkänd i den här kursen och kan därför skriva i forumet!</h2>
</div>
<?php
}
elseif(isset($_POST['question_1']))
{
$pass = true;
if($_POST['question_1'] != 'correct')
{
echo '<h3 style="color: red">Fel svar på fråga #1</h3>' . "\n";
echo 'Allt för motbjudande bilder eller länkar får inte skickas med i inlägg.' . "\n";
$pass = false;
}

$q2_fail = false;
$q2_fail = ($_POST['question_2_alt_1'] != 'correct') ? true : $q2_fail;
$q2_fail = ($_POST['question_2_alt_2'] == 'incorrect') ? true : $q2_fail;
$q2_fail = ($_POST['question_2_alt_3'] == 'incorrect') ? true : $q2_fail;
if($q2_fail == true)
{
echo '<h3 style="color: red;">Fel svar på fråga #2</h3>' . "\n";
echo 'Privata meddelanden och sådant som inte berör diskussionsämnet ska inte skrivas i diskussionen!' . "\n";
$pass = false;
}


if($_POST['question_3'] != 'correct')
{
echo '<h3 style="color: red">Fel svar på fråga #3</h3>' . "\n";
echo 'Diskussioner utanför ämnet ska tas privat eller i en annan diskussionstråd.' . "\n";
$pass = false;
}

if($_POST['question_4'] != 'correct')
{
echo '<h3 style="color: red">Fel svar på fråga #4</h3>' . "\n";
echo 'Språkfel ska bara rättas om det sker i samband med ett inlägg som anknyter till ämnet, dessutom ska man försöka hålla en vänlig ton.' . "\n";
$pass = false;
}

$q5_fail = false;
$q5_fail = ($_POST['question_5_alt_1'] == 'incorrect') ? true : $q5_fail;
$q5_fail = ($_POST['question_5_alt_2'] != 'correct') ? true : $q5_fail;
$q5_fail = ($_POST['question_5_alt_3'] == 'incorrect') ? true : $q5_fail;
$q5_fail = ($_POST['question_5_alt_4'] != 'correct') ? true : $q5_fail;
if($q5_fail == true)
{
echo '<h3 style="color: red;">Fel svar på fråga #5</h3>' . "\n";
echo 'Man får både fortsätta diskutera och rapportera till ordningsvakter, däremot får man inte skriva någon tillsägelse i diskussionen eller skicka meddelanden till Superjohan/Heggan' . "\n";
$pass = false;
}

if($_POST['question_6'] != 'correct')
{
echo '<h3 style="color: red">Fel svar på fråga #6</h3>' . "\n";
echo 'Givetvis får man argumentera för sin sak, men man måste göra det på rätt ställe!' . "\n";
$pass = false;
}

if($pass == true)
{
$_SESSION['userinfo']['forum_course_1'] = 1;
$query = 'UPDATE userinfo SET forum_course_1 = 1 WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
?>
<div class="green_faded_div">
<h2>Du är godkänd i den här kursen och kan därför skriva i forumet!</h2>
</div>
<?php
}
}
?>

<p class="ingress">
Det här är en kort introduktion till Hamsterpajs forum. Innan du får skriva i forumet måste du läsa igenom det här dokumentet och svara rätt på frågorna i slutet.
</p>

<h2>En kort introduktion till hur forum fungerar</h2>
<h5>Om du har använt något annat forum innan kan du hoppa över det här stycket</h5>
<p>
Hamsterpajs diskussionsforum består av många små forum som har sorterats in i kategorier.
Exempelvis är Café en kategori som innehåller modeforum, musikforum, matforum och
några andra forum.<br />
I de här forumen finns det trådar, eller diskussioner som vi kommer kalla dem i den 
här kursen. Vem som helst kan skapa en egen diskussion, den som söker efter ett
recept på en smarrig efterrätt utan mjölk kan alltså starta en diskussion om
mjölkfria efterrätter i matforumet.<br />
I diskussionen samlas det så småningom inlägg eller poster/posts som de ibland
kallas. Dessa inlägg är svar som medlemmarna själva skrivit.
Alla kan svara i alla diskussioner, du kan även svara i diskussioner som du själv
starat.<br />
För att det här ska fungera är det väldigt viktigt att man håller sig till ämnet,
i diskussionen om mjölkfria efterrätter får man inte fråga vilken mobiltelefon som
är bäst, då startar man en egen diskussion i forumet "Mellan himmel och jord" istället.
</p>

<h2>Viktigt att tänka på när du skriver ett inlägg</h2>
<p>
Forumet är till för att diskutera i och vi vill att alla ska kunna vara 
med i dikussionen, därför är vi tacksamma om du:
</p>
<ul>
<li>
Håller dig till ämnet och inte svävar iväg eller svarar på någon annans
skämt. När diskussionen en gång har lämnat ämnet är det lätt hänt att
denspårar ur fullständigt.
</li>
<li>
Undviker att kommentera andras stavning. Givetvis vill vi att den som
skriver ett inlägg ska anstränga sig för att skriva ordentligt, men vi vet
av erfarenhet att tjafsande om stav- och språkfel lätt förstör en diskussion.  
</li>
<li>
Motiverar dina åsikter och anger källa när det är motiverat. Om de som skriver
inlägg inte motiverar och förklarar så är det lätt hänt att det här blir ett
tjafsforum istället för ett diskussionsforum.
<h5>Beakta följande exempel</h5>
<div class="bad_example">
Alla våldtäktsmän borde dö!
</div>
<div class="good_example">
Jag tycker att det borde vara dösstraff för våldtäkt! Dels är brottet 
oförlåtligt och kan inte sonas i fängelse - dels ser man till att förövaren
inte kan göra om det.
</div>
<div class="bad_example">
Invandrare begår fler brott än svenskar!
</div>
<div class="good_example">
Invandrare begår brott i större utsträckning än svenskar.
Källa: "Brottslighet bland personer födda i Sverige och i utlandet",
BRÅ 2005, <a href="http://www.bra.se/extra/measurepoint/?module_instance=4&name=1brottslsveutland.pdf&url=/dynamaster/file_archive/051214/e7dae113eb493479665ffe649e0edf57/1brottslsveutland.pdf">Länk</a>
</div>
<div class="bad_example">
George Bush är en idiot, jag hatar honom, han är ju helt dum i huvet!
</div>
<div class="good_example">
George Bush är en stor dåre, på en stor post! Så mycket elände den mannen
ställt till med, Iraq och Afghanistan, utan att ha lyckats få tag i någon
terrorist - bara jagat påhittade massförstörelsevapen! Dessutom vägrar 
jubelidioten inse att jordens klimat är på väg mot katastrof, inget får ju störa "The american way of life".
</div>
</li>
<li>
Undviker påhopp och nedvärderande språk, det leder allt som oftast till
frustration och slutligen pajkastning. Presidenten i exemplet ovan är dock
undantagen den här regeln, i alla fall ibland.
</li>
<li>
Skriver mer än ett par rader i dina inlägg, korta inlägg uppmanar sällan till
fortsatt diskussion, utan bidrar bara till att diskussionen blir svår att överblicka.
</li>
<li>
Undviker obsceniteter, porr, olagligheter och annat som hör hemma på Internets
baksida. Om du är tveksam - läs reglerna som du hittar i undermenyn i forumet!
</li>
</ul>

<h2>Det är ordningsvakterna som ansvarar för ordningen här!</h2>
<p>
Även om vi självklart vill att du hjälper till att hålla ordning genom att följa reglerna
och säga till någon ordningsvakt när du ser någon som inte gör som man ska så vill
vi inte att du säger till den som felar i diskussionen. Lämna det åt ordningsvakterna!<br />
Samma sak gäller om du tycker att en diskussion ska låsas - om du verkligen känner att
du måste säga till någon, gör det genom ett gästboksinlägg i så fall!
</p>

<h1>Kontrollfrågor</h1>
<?php
echo '<form action="' . $_SERVER['PHP_SELF'] . '?article=' . $_GET['article'] . '" method="post">';
?>
<h2>#1, stötande inlägg/bilder</h2>
<p>
Zn4rk har hittat en bild på en äldre man med sina genitalier i munnen på en ovanligt
stor Marulk, nu vill han visa den för andra hamsterpajanvändare. Hur ska Zn4rk göra?
<select name="question_1">
<option value="incorrect">Han ska använda [Infoga bild]-knappen för att få in bilden.</option>
<option value="incorrect">Zn4rk skriver länken i sitt inlägg och ber läsaren klicka på länken</option>
<option value="incorrect">Eftersom bilden är stötande ska Zn4rk skriva en varning först i inlägget</option>
<option value="correct">Zn4rk får inte lägga in bilden i forumet, inte ens som länk. Det finns andra sidor för sånt!</option>
</select>
</p>

<h2>#2, omotiverade åsikter</h2>
<p>
Emma har bråttom iväg, men har fått ett svar i forumet och vill hinna svara innan hon
måste sticka. Därför skriver hon:<br />
<em>"Jag tycker också att Patrick ska vinna Idol! Ha det bra, vi ses på träningen Gullan!"</em><br />
<h3>Kryssa i de rutor som stämmer</h3>
<input type="checkbox" name="question_2_alt_1" id="question_2_alt_1" value="correct" />
<label for="question_2_alt_1">Emma skulle ha låtit bli att svara, hon måste förklara varför Patrick ska vinna om hon vill skriva ett inlägg.</label>
<br />
<input type="checkbox" name="question_2_alt_2" id="question_2_alt_2" value="incorrect" />
<label for="question_2_alt_2">Eftersom Emma använder [Svara]-knappen så får hon skriva en privat hälsning.</label>
<br />
<input type="checkbox" name="question_2_alt_3" id="question_2_alt_3" value="incorrect" />
<label for="question_2_alt_3">Det är inget fel med Emmas inlägg, ju mer som skrivs, desto bättre!</label>
</p>

<h2>#3, Privata diskussioner utanför ämnet</h2>
<p>
Onormal avslutade sitt inlägg inlägg i en tråd om häftiga killnamn med att påpeka att hon
är den enda i Sverige som heter Loria. Systeryster läser detta och kommer på att hon känner en annan
med namnet Loria. Hur ska systeryster göra?<br />
<select name="question_3">
<option value="incorrect">Systeryster skriver att hon vet en som heter Loria</option>
<option value="incorrect">Hon ska sökna på hitta.se och skicka med länken, man måste ju motivera sina inlägg!</option>
<option value="incorrect">Hon får inte skriva mer i diskussionen eftersom den är på väg att spåra ur.</option>
<option value="correct">Systeryster skriver privat till Onormal i hennes gästbok och berättar om den andra Loria</option>
</select>
</p>

<h2>#4, Språkfel och spydighet</h2>
<p>
Androoz behöver hjälp med sin dator eftersom hans Counter-Strike har börjat hacka och bilden
är grumlig. Han startar en tråd i forumet "Hjälp mig" under datorer och förklarar att det är
fel på hans data.<br />
Qmixx retar sig på att Androoz skriver data istället för dator och bestämmer sig för att skriva
ett spydigt inlägg där han förklarar att datan minsann ligger på CD-skivan och att Androoz ska gå
tillbaks till butiken om det är fel på skivan.<br />
Får man göra såhär?<br />
<input type="radio" name="question_4" value="correct" id="question_4_alt_1" />
<label for="question_4_alt_1">Nej, men om man tänkte skriva ett hjälpsamt svar så får man vänligt påpeka skillnaden mellan data/dator.</label><br />

<input type="radio" name="question_4" value="incorrect" id="question_4_alt_2" />
<label for="question_4_alt_2">Ja, rätt ska vara rätt! Dessutom får man skylla sig själv om man gör fel!</label><br />

<input type="radio" name="question_4" value="correct" id="question_4_alt_3" />
<label for="question_4_alt_3">Nej, man får aldrig påpeka en felskrivning eller ett stavfel, det står i reglerna!<br />
</p>

<h2>#5, när någon annan bryter mot reglerna</h2>
<p>
Pillerparty upptäcker att Mogel (som vanligt) skriver meningslösa skitinlägg i en tråd. Eftersom Mogel är på väg att förstöra diskussionen med sina korta inlägg utan något vettigt innehåll bestämmer hon sig för att ta tag i saken. Vad får hon göra?<br />
<strong>OBS! Kryssa i de två rätta rutorna, inte bara en!</strong>
<br />
<input type="checkbox" name="question_5_alt_1" id="question_5_alt_1" value="incorrect" />
<label for="question_5_alt_1">Hon får skriva privat till Superjohan eller Heggan och påpeka att Mogel spammar</label>
<br />
<input type="checkbox" name="question_5_alt_2" id="question_5_alt_2" value="correct" />
<label for="question_5_alt_2">Skriva privat till en ordningsvakt som är online och bifoga en länk till inlägget.</label>
<br />
<input type="checkbox" name="question_5_alt_3" id="question_5_alt_3" value="incorrect" />
<label for="question_5_alt_3">Skapa ett nytt inlägg i diskussionen och förklara för Mogel att han förstör</label>
<br />
<input type="checkbox" name="question_5_alt_4" id="question_5_alt_4" value="correct" />
<label for="question_5_alt_4">Fortsätta diskussionen, utan att bemöta eller besvara det Mogel skrivit.</label>
</p>

<h2>#6, att hålla sig till ämnet</h2>
<p>
Ace, Fast och Sebastian diskuterar dyra läderskor i modeforumet. Ace föredrar bestämt Prada
medans Fast och Sebastian hävdar att Dolce&Gabbana är helt rätt i Juni.<br />
Flum, som är inbiten vegetarian tycker att läderskor är mord och barbari, hon ser det som sin mission
här i livet att omvända alla andra till att äta kikärtor och ha dreadlocks.<br />
Vad ska Flum göra?<br />
<select name="question_6">
<option value="incorrect">Hålla tyst, man inte får försöka omvända folk på Hamsterpaj!</option>
<option value="correct">Hon får starta en egen tråd i "Mellan himmel och jord" där hon argumenterar för ett förbud mot djurmord</option>
<option value="incorrect">Skriva ett inlägg i sko-tråden, alla har rätt till sin åsikt!</option>
</select>
</p>

<input type="submit" class="button" value="Skicka svar" />
</form>