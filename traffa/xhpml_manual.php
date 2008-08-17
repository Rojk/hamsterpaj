<style>
	body
	{
		background: #e0e7ec;
		font-family: arial, verdana;
	}
	#main
	{
		background: white;
		width: 550px;
	}
	h1
	{
		font-size: 25px;
	}
	h2
	{
		font-size: 18px;
		border-bottom: 1px solid #787878;
	}
	.example
	{
		background: #e2e2e2;
	}
	.code
	{
		font-size: 12px;
		font-family: courier new, courier, terminal, system;
	}
	
	.text_1
	{
		font-family: comic sans ms;
		color: #65b009;
		font-size: 12px;
	}
	
	.text_3
	{
		font-family: "trebuchet ms";
		font-size: 14px;
		color: #415866;
	}
	.box_1
	{
	
	}
</style>

<div id="main">
<h1>Presentationskoder</h1>

<h2>Fetstilt</h2>
<div class="example">
	<div class="code">
		Jag &lt;fet&gt;älskar&lt;/fet&gt; choklad med apelsinsmak.
	</div>
	<div class="result">
		Jag <strong>älskar</strong> choklad med apelsinsmak.
	</div>
</div>


<h2>Kursiv text</h2>
<div class="example">
	<div class="code">
		Jag &lt;fet&gt;älskar&lt;/fet&gt; &lt;kursiv&gt;choklad&lt;/kursiv&gt; med apelsinsmak.
	</div>
	<div class="result">
		Jag <strong>älskar</strong> <em>choklad</em> med apelsinsmak.
	</div>
</div>

<h2>Understruken text</h2>
<div class="example">
	<div class="code">
		Jag &lt;fet&gt;älskar&lt;/fet&gt; &lt;kursiv&gt;choklad&lt;/kursiv&gt; med &lt;understruken&gt;apelsinsmak&lt;/understruken&gt;.
	</div>
	<div class="result">
		Jag <strong>älskar</strong> <em>choklad</em> med <u>apelsinsmak</u>.
	</div>
</div>

<h2>Rubriker</h2>
<div class="example">
	<div class="code">
		&lt;rubrik&gt;Hej och välkommen&lt;/rubrik&gt;
	</div>
	<div class="result">
		<h5>Hej och välkommen</h5>
	</div>
</div>

<h2>Underrubriker</h2>
<div class="example">
	<div class="code">
		&lt;underrubrik&gt;Mitt ansvar:&lt;/underrubrik&gt;
	</div>
	<div class="result">
		<h6>Mitt ansvar:</h6>
	</div>
</div>

<h2>Text-typer</h2>
<div class="example">
	<img src="http://images.hamsterpaj.net/images/illustrations/xhpml_manual/text_1.png" style="border: 1px solid #494949; clear: both;" />
	<div class="code">
		&lt;text typ=1&gt;Hej, jag heter &lt;fet&gt;Pelle&lt;/fet&gt; och luktar skinka.
	</div>
	<div class="result">
		<span class="text_1">Hej, jag heter <strong>Pelle</strong> och luktar skinka.
	</div>
</div>
<div class="example">
	<img src="http://images.hamsterpaj.net/images/illustrations/xhpml_manual/text_3.png" style="border: 1px solid #494949; clear: both;" />
	<div class="code">
		&lt;text typ=3&gt;Min kompis, &lt;länk&gt;Ihefsu&lt;/länkt&gt;, sprider en ljuvlig doft av salami.
	</div>
	<div class="result">
		<span class="text_3">Min kompis, <a href="/traffa/quicksearch.php?username=ihefsu">Ihefsu</a>, sprider en ljuvlig doft av salami.
	</div>
</div>

<h2>Boxar</h2>
<div class="example">
	<div class="code">
		&lt;box typ=2&gt;Detta är<br />
		Min egna lilla ruta :)&lt;/box&gt;
	</div>
	<div class="result">
		<div class="box_2">Detta är<br />
			Min egna lilla ruta :)
		</div>
	</div>
</div>
<div class="example">
	<div class="code">
		&lt;box typ=5&gt;&lt;rubrik&gt;Hey där!&lt;/rubrik&gt;<br />
		&lt;text typ=3&gt;Hur är läget då?&lt;/text&gt;<br />
		Jodå, jag mår &lt;fet&gt;bara bra&lt;/fet&gt;.
	</div>
	<div class="result">
		<div class="box_5"><h5>Hey där</h5><br />
			<span class="text_3">Hur är läget då?</span><br />
			Jodå, jag mår <strong>bara bra</strong>.
		</div>
	</div>
</div>

<h2>Länkar</h2>
<div class="example">
	<div class="code">
		Jag träffade &lt;länk&gt;Heggan&lt;/länk&gt; på &lt;länk mål="http://www.lunarstorm.se/"&gt;Lunarstorm&lt;/länk&gt;.<br />
		Han är hemligt kär i &lt;länk användare="totte"&gt;västerås sötaste pojke&lt;/länk&gt;!
	</div>
	<div class="result">
		Jag träffade <a href="/traffa/profile.php?id=15">Heggan</a> på <a href="http://www.lunarstorm.se/">Lunarstorm</a>.<br />
		Han är hemligt kär i <a href="/traffa/profile.php?id=10362">västerås sötaste pojke</a>!
	</div>
</div>
</div>