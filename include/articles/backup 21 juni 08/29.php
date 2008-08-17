<p>
Många av er undrar hur våra lösenord egentligen sparas. Kan vi på Hamsterpaj se lösenorden? Kan man få tillbaka lösenordet när man glömt bort det? Kan två användare ha samma lösenord? Osv. Jag skall här försöka förklara hur allt detta fungerar.
</p>
<h3>Registrering</h3>
<p>
När du registrerar dig på Hamsterpaj så får du välja ett lösenord. Du skriver lösenordet i två olika fält i formuläret. Dessa lösenord skickas till hamsterpajservern från din webbläsare när du klickar på "Bli medlem"-knappen.
</p>
<p>
På servern jämförs det två lösenorden med varandra för att se om de är likadana. Om de inte är det så får du upp formuläret igen med en liten röd text som talar om detta. Om de två lösenorden däremot är likadana så antar vi att du vet ganska bra vad du skrivit och att du kommer komma ihåg det när du skall logga in nästa gång. Det som händer nu är att lösenordet krypteras och lagras i databasen. Den kryptering vi använder kallas för <a href="http://en.wikipedia.org/wiki/MD5">MD5</a> och är en av de vanligaste krypteringarn för lösenord. Denna kryptering är en envägskryptering, dvs det går inte att få fram lösenordet igen. Det enda man har är det krypterade lösenordet.
</p>
<p>
	Vadå envägs? Tänk dig att vi skall kryptera en fyrsiffrig kod. Om koden är 6988 så räknar vi ut summan av 6 + 9 + 8 + 8 som blir 31. Vi vet att 6 + 9 + 8 + 8 alltid blir 31, däremot kan man inte gå åt andra hållet. En envägskryptering fungerar på samma sätt fast mycket mer avancerat så att det blir väldigt liten sannolikhet att två olika texter blir samma sak krypterat. Resultatet av en envägskryptering kallas för hashvärde, checksumma, md5-summa eller finger print. Det är samma teknik som används för att kontrollera att till exempel nedladdade filer överförts korrekt och inte innehåller några fel. Om ett enda litet tecken i filen har ändrats så får man inte samma checksumma längre och då vet man att något är fel med filen.
</p>
<h3>Inloggning</h3>
<p>
Men...? Hur vet då Hamsterpaj att man skriver rätt lösenord när man försöker logga in igen? Jo, det lösenord som du skriver i lösenordsrutan när du loggar in skickas till servern och krypteras på samma sätt. Det som man får ut av krypteringen den här gången jämförs sedan med det som ligger i databasen. Om de är lika så vet man att det var rätt lösenord eftersom samma ord alltid blir samma sak när det krypteras. Jämförelsen görs med det krypterade lösenord som är kopplat till det kontonamn man angivit. Det finns alltså inget som hindrar att två användare har samma lösenord.
</p>
<h3>Gissa</h3>
<p>
Det finns alltså bara ett sätt att ta reda på vad ett lösenord är om man bara har den krypterade varianten att tillgå. Man får gissa sig fram. Om man då har skrivit ett lösenord med både små och stora bokstäver och dessutom siffror och som är åtta tecken långt, så har man 360 040 606 269 696 olika lösenord att gissa på. Det tar alltså en stund för HackerPelle om han skall prova alla möjliga lösenord till EmoStinas konto på Hamsterpaj.
</p>
<p>
Om HackerPelle skriver ett litet program på sin datamaskin som automatiskt provar alla lösenord kanske det som bäst klarar att prova 1000 lösenord i sekunden. Då tar det bara 360 040 606 270 sekunder att gå igenom alla lösenord. Det är ungefär 11 000 år!
</p>
