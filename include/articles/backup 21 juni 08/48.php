<style>
	dl dt
	{
		font-weight: bold;
	}
	
	fieldset
	{
		margin-bottom: 10px;
		border: 1px solid #ababab;
	}
	
</style>

<p>
	Här visar vi hur du kommer igång med mIRC och kan chatta på Hamsterpaj-chatten som ett riktigt proffs!
</p>

<fieldset>
	<legend>Del #1</legend>
	<img src="http://images.hamsterpaj.net/article_illustrations/mirclogo.png" style="float: right; margin: 10px;" />
	<h1>Ladda hem och installera mIRC</h1>
	<p>
		mIRC är ett Sharewareprogram som laddas ner från mIRC.com och installeras precis som alla andra program.
	</p>

	<h3>Nedladdning</h3>
	<ol>
		<li>Peka din webbläsare till <a href="http://www.mirc.com/" target="_blank">www.mirc.com</a></li>
		<li>Tryck på "Download mIRC"</li>
		<li>Tryck på "Download mIRC" igen</li>
		<li>Tryck på "Download mIRC" en sista gång</li>
		<li>Spara filen på skrivbordet</li>
	</ol>
	
	<h3>Installation</h3>
	<ol>
		<li>Dubbelklicka på installationsfilen som du laddade hem</li>
		<li>Tryck på Next &gt;</li>
		<li>I Agree</li>
		<li>Next &gt;</li>
		<li>Du kan låta alla rutor vara ikryssade, välj Next &gt;</li>
		<li>Next &gt;</li>
		<li>Next &gt;</li>
		<li>Install</li>
		<li>Kryssa i Run mIRC</li>
		<li>Klicka på Finish</li>
	</ol>
	
	<h3>Start och inställning av mIRC</h3>
	<p>
		När mIRC startas kommer en ruta där du ombeds registrera, eftersom ditt studiebidrag går till
		godis, cola och Converse så har du inte råd. Tryck <strong>Continue</strong>.
	</p>
	<p>
		Dialogen <em>mIRC Options</em> visas.
	</p>
	<dl>
		<dt>Full Name</dt>
			<dd>Information om dig själv som visas när någon visar information om dig. Här kan du skriva en kort text om vem du är och varför du är inne i chatten</dd>
		<dt>Email Address</dt>
			<dd>För att undvika spam så kan du klottra lite vad som helst här. Informationen används aldrig.</dd>
		<dt>Nickname</dt>
			<dd>Ditt önskade användarnamn</dd>
		<dt>Alternative</dt>
			<dd>Om ditt önskade användarnamn är upptaget så används detta istället</dd>
	</dl>
	<p>
		I den här guiden har vi hoppat över mIRCs inbyggda serverlista. Tryck på <strong>OK</strong> och inte <strong>Connect</strong> när du fyllt i fälten.
	</p>
</fieldset>

<fieldset>
	<legend>Del #2</legend>
	<h2>Ansluta till en IRC-server och gå in i ett chattrum</h2>
	<p>
		När du startat mIRC, fyllt i dina inställningar och klickat ner dialogrutan bör du se ett vitt fönster med en skrivruta längst ner. I den rutan skriver du både meddelanden och kommandon.
	</p>

	<ol>
		<li>Skriv <strong>/server irc.hamsterpaj.net</strong> i den vita rutan och tryck på <strong>Enter</strong></li>
		
		<li>Det plingar till och du bör se en hel del text i olika färger. Du kan strunta i det så länge.</li>
		
		<li>Om rutan <em>mIRC Favorites</em> öppnas så bocka ur	
			<ul>
				<li><strong>Pop up favorites on connect</strong></li>
				<li><strong>Enable join on connect</strong></li>
			</ul>
			Stäng sedan rutan. Det fungerar inte att gå in i de chattrummen som listas, de finns inte på Hamsterpajs chatt.
		</li>
		
		<li>För att gå in i ett chattrum använder du <strong>/join #<em>chattrum</em></strong>.<br />
		Skriv <strong>/join #moget</strong> för att gå in i chattkanalen <em>#moget</em></li>
	</ol>
	<p>
		Om du vill kan du gå in i flera chattrum på samma gång, skriv bara <strong>/join #<em>chattrum</em></strong> igen, rummet kommer öppnas i ett nytt fönster.</li>
	</p>
	<p>
		För att skriva något i chattrummet, skriv bara texten och tryck enter, precis som när du använde /join.
	</p>
	
</fieldset>

<fieldset>
	<legend>Del #3</legend>
	<h2>Så här är IRC uppbyggt</h2>
	<p>
		Precis som det finns olika webbsidor på Internet så finns det olika IRC-nät. Ett IRC-nät är en eller flera servrar
		som kopplats ihop. På de här näten finns olika <em>channels</em> (på svenska <em>kanaler</em> eller <em>chattrum</em>) där man kan
		chatta tillsammans med andra.<br />
		Namnet på ett chattrum börjar alltid med #. Några av Hamsterpajs chattrum är <em>#träffa</em>, <em>#moget</em> och <em>#linux</em>.
	</p>
	<p>
		Oftast chattar man i chattrummen, där kan alla som är inne i rummet se och svara på det man skriver.<br />
		För att chatta privat med någon dubbelklickar man på personens namn, då öppnas ett nytt fönster där man kan prata
		ostört.<br />
		I chattrummen finns det nästan alltid <em>operatörer</em>, deras namn börjar med <strong>@</strong>. En operatör kan slänga ut kicka (slänga ut)
		och banna (stänga av) personer från chattrummet.
	</p>
</fieldset>

<fieldset>
	<legend>Del #4</li>
	<h2>Genvägar och kommandon i mIRC</h2>
	
	<dl>
		<dt>Autocomplete</dt>
			<dd>Genom att skriva de första bokstäverna i någons namn och trycka på <strong>TAB</strong> så skriver mIRC färdigt namnet åt dig</dd>

		<dt>/nick</dt>
			<dd>Genom att skriva <strong>/nick kalle</strong> kan du byta namn. Det du skriver efter /nick blir ditt nya namn, i det här fallet <strong>kalle</strong></dd>
			
		<dt>/join</dt>
			<dd>För att gå in i ett chattrum skriver du <strong>/join #moget</strong>, byt ut #moget mot namnet på det chattrum du vill in i.</dd>

		<dt>/part</dt>
			<dd>Med <strong>/part</strong> lämnar du en kanal, exempelvis <strong>/part #moget Ni är tjockisar</strong>. Det exemplet resulterar i att du lämnar
			<strong>#moget</strong> med kommentaren <em>Ni är tjockisar</em></dd>

		<dt>/msg</dt>
			<dd>Skickar ett privat meddelande till någon. Den här raden skickar <em>Hejsan</em> till <em>Soode</em>: <strong>/msg Soode Hejsan</strong></dd>

		<dt>/whois</dt>
			<dd>Visar information om en chattare. Skriv <strong>/whois kalle</strong> för att se vad <em>kalle</em> fyllt i som <em>Full Name</em></dd>
			
		<dt>/away</dt>
			<dd>Markerar dig som <em>inte vid datorn</em>. Skriv en anledning efter kommandot, så här: <strong>/away äter middag</strong>.<br />För att ta bort awaymeddelandet skriver du bara <strong>/away</strong></dd>
	</dl>
	
</fieldset>

<fieldset>
	<legend>Del #5</legend>
	<h2>Ordlista</h2>
	<dl>
		<dt>Nick/nickname</dt>
			<dd>Någons användarnamn på chatten</dd>

		<dt>Ban</dt>
			<dd>Avstängning från ett chattrum, den som blivit bannad kan inte gå in i chattrummet igen</dd>

		<dt>G-line</dt>
			<dd>Avstängning från chattnätet. Har man lyckats med detta har man antagligen ställt till mycket problem för en ircop</dd>
		
		<dt>K-line</dt>
			<dd>Avstängning från en enskild chattserver. Ganska ovanligt.</dd>
		
		<dt>Ircop</dt>
			<dd>En person som har makt över chattnätet. Denna kan gå in i alla chattrum och ge sig själv OP hur som helst</dd>
		
		<dt>Op</dt>
			<dd>En person som har makt över ett chattrum, kan kicka och banna personer samt dela up OP/voice/half-op</dd>
		
		<dt>Half-op/hop</dt>
			<dd>Nivån under OP. Kan kicka och banna personer samt dela ut voice</dd>
		
		<dt>Voice</dt>
			<dd>Status som ger ett plustecken framför namnet. Chattrum kan ställas in så att bara de med voice kan skriva, alla andra får bara läsa</dd>
		
		<dt>Chanserv</dt>
			<dd>Ett tilläggsprogram som gör det möjligt att registrera chattrum och göra inställningar för dessa</dd>
		
		<dt>Nickserv</dt>
			<dd>Ett tilläggsprogram som gör det möjligt att registrera nicknames, så att ingen annan kan använda det och för att
			spara rättigheter i chattrum</dd>

		<dt>Ban-evading</dt>
			<dd>Tjat om att få en ban bortplockad. Kan leda till sura ircops och G-line</dd>

	</dl>
</fieldset>