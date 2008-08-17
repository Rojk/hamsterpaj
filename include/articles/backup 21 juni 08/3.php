<p class="intro">
I denna guide kommer vi att gå igenom hur en router fungerar, vad NAT är, med framför allt, hur man pekar om portar och
vad fördelen med en router är</p>

<p class="subtitle">Vad är en router/NAT och varför använder man dem?</p>
<div id="contentPostBox">
På Internet används <i>IP-adresser</i> för att identifiera datorer och servrar, dessa består av fyra grupper med siffror
och max 255 per grupp. Exempelvis: 127.0.0.1. Flera datorer kan inte ha samma IP-adress, det skulle bli lika knasigt som
om två personer skulle ha samma personnummer.<BR>

De flesta internetleverantörer ger dig bara en IP-adress, vilket innebär att du bara kan ha en dator uppkopplad åt gången.
För att komma runt det här problemet har man skapat såkallade <i>NAT:s Networks Address Translators</i>, 
eller i dagligt tal, routrar. En router skapar ett <i>internt nätverk</i> och ger datorerna inuti nätverket <i>interna
IP-nummer</i>, IP-nummer som bara gäller i det lilla nätverket.<br/>
Routern ser sedan till att trafiken mellan det lilla nätverket och Internet fungerar.
</div>

<p class="subtitle">Problem med NAT</p>
<div id="contentPostBox">
I nätverksvärlden pratar man om <i>klienter</i> och <i>servrar</i>. Många tror att en server är en stor och dyr specialdator,
något som sällan stämmer. I själva verket så är alla program som "lyssnar" efter någonting en server. Om du är <i>active</i>

på DC så lyssnar din dator efter andra datorer som vill ladda hem från dig, din dator har blivit en server. För att flera 
program på samma dator skall kunna "lyssna" så har man skapat portar, eller ingångar i datorn som kan användas för trafik.
Exempelvis så går vanlig surf-trafik på port nummer 80.<BR>
Detta ställer till lite problem om man har router. Tänk dig att jag sitter på DC och lyssnar på port 1412 och du skall ansluta
till mitt IP-nummer, 81.26.232.235. Då kommer du till min router, som tar hand om trafiken mot Internet.<BR>
<i>- Hejsan, jag skulle vilja ansluta till dig på port 1412.<br />
- Jag en router och jag vet inte vilken av datorerna i mitt nätverk som lyssnar på port 1412, så du kan tyvärr inte ansluta.<BR></i>
Du kanske börjar ana vilka problem en router som inte är ordentligt inställd kan medföra...<BR>
Om jag däremot hade haft en router som var rätt inställd så skulle konversationen se ut såhär:<BR>
<i>- Hejsan, jag skulle vilja ansluta till dig på port 1412.<br />
- DC++ här, du är ansluten och kan börja ladda hem från mig.</i><br />
Detta eftersom routern automatiskt skickar vidare trafiken utan att lägga sig i. Hur du ställer in vilka portar som skall
 pekas till vilka datorer kan du läsa här nedan.

</div>

<p class="subtitle">Att peka om en port</p>
<div id="contentPostBox">
<b>Steg 1 - Ta reda på ditt interna IP-nummer</b><BR>
&nbsp;&nbsp;Klicka på <i>Start</i> -> <i>Kör</i>, skriv in <i>cmd</i> och tryck på <i>OK</i>.<BR>

&nbsp;&nbsp;Nu kommer ett svart fönster upp, i detta skriver du <i>ipconfig</i> och trycker på <i>[ENTER]</i>.<BR>
&nbsp;&nbsp;Du bör få ut någonting som liknar detta:<BR>
&nbsp;&nbsp;&nbsp;&nbsp;<i>IP-adress . . . . . . . . . . . . : <b>192.168.0.4</b><BR>
&nbsp;&nbsp;&nbsp;&nbsp;Nätmask . . . . . . . . . . . . . : 255.255.255.0<BR>
&nbsp;&nbsp;&nbsp;&nbsp;Standard-gateway  . . . . . . . . : 192.168.0.1</i><BR>

&nbsp;&nbsp;Skriv ner din IP-adress (här markerad med fetstil).<BR>
&nbsp;&nbsp;Stäng ner kommandotolken (den svarta rutan).<BR>
&nbsp;&nbsp;Använder du Windows 95/98/ME så skriver du istället <i>winipcfg</i> och antecknar IP-adressen.<BR>
		
<b>Steg 2 - Logga in i routern.</b><BR>
&nbsp;&nbsp;På de allra flesta routrar så kan du surfa till <a href="http://192.168.0.1" target="_BLANK"><i>192.168.0.1</i></a>
 i Internet Explorer.<BR>

&nbsp;&nbsp; I annat fall kan du testa att surfa till den adress du fick upp som "Standard-gateway".<BR>
&nbsp;&nbsp;Kolla i din manual vilket användarnamn/lösenord som gäller till din router. D-link brukar ha användarnamnet 
<i>admin</i> och ett tomt lösenordsfält. Medans NETGEAR har <i>admin</i> både som lösenord och användarnamn.<BR>
		
<b>Steg 3 - Port Forwarding</b><BR>
&nbsp;&nbsp;I D-links prylar kallas detta "Virtual Server", olika tillverkare har valt olika namn. Iallafall, leta fram 
<i>Port Forwarding</i> eller <i>Virtual Server</i>.<BR>

&nbsp;&nbsp;Ställ sedan in <i>External port</i> och <i>Internal port</i> till det portnummer du vill peka.<BR>

&nbsp;&nbsp;Internal IP ska vara ditt interna IP-nummer, som i vårat exempel är <i>192.168.0.4</i>.<BR>
&nbsp;&nbsp;Spara inställningarna och logga ut.<BR>
</div>

