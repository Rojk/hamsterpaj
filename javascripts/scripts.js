// If hp, "The Hamsterpaj Namespace", wasn't defined before then define it!
if(typeof(hp) == 'undefined'){ var hp=new Object(); }

/* Very handy little function... */
hp.give_me_an_AJAX = function(){
	try{
		return new XMLHttpRequest();
	}catch(e){
		try{
			return new ActiveXObject("Msxml2.XMLHTTP");
		}catch(e){
			try{
				return new ActiveXObject("Microsoft.XMLHTTP");
			}catch(e){
				return false;
			}
		}
	}
}


hp.mouse = {
	x: 0,
	y: 0,
	update: function(moz_event)
	{
		this.x = (window.event) ? window.event.x : moz_event.pageX;
		this.y = (window.event) ? window.event.y : moz_event.pageY;
	},
	add_onmousemove_handler: function(){
		/* This might be a bit confusing, but it ensures no previous document.onmousemove will be overwritten */
		var old_onmousemove = (document.onmousemove)? document.onmousemove : function(){  };
		document.onmousemove=function(evt)
		{
			eval(old_onmousemove);
			hp.mouse.update(evt);
		}
	}
}


hp.go_to_user = {
	profile: function(id){
		this.navigate('/traffa/profile.php?id=' + id);
	},
	guestbook: function(id){
		this.navigate('/traffa/guestbook.php?view=' + id);
	},
	
	navigate: function(href){
		window.location.href = href;
	}
}

hp.debug = {
	alert_r: function(inspect_object)
	{
		alert(this.r_get_childs(inspect_object, ''));
	},
	
	r_get_childs: function(object, iterate_level)
	{
		var output = '';
		for(var property in object)
		{
			output = output + iterate_level + ((typeof(object[property]) == 'object' || typeof(object[property]) == 'array') ? (property + ' => ' + property + '\n' + this.r_get_childs(object[property], iterate_level + '\t')) : (property + ' => ' + object[property] + '\n'));
		}
		
		return output;
	}
}

womAdd("hp.mouse.add_onmousemove_handler()");

//------------------------------------------
// Kryssar i alla checkboxar på sidan.
//-----------------------------------------
/*function check_all_checkboxes()
{
	inputs = document.getElementsByTagName('input');
  for(var i=0; i < inputs.length; i++)
	{
    if(inputs[i].getAttribute('type') == 'checkbox')
		{
				inputs[i].checked = true;
		}
	}
}*/

function showLayer(LAYERNAME) {
	document.LAYERNAME.style.visibility = visible;
}


//------------------------------------------
// Byter div som visas i vänsterkrysset på förstasidan
//------------------------------------------
function fp_div_show(divid, location)
{
	for(i = 1; i <= 5; i++)
	{
		document.getElementById(location+"_"+i).style.display = "none";
	}
	document.getElementById(location+"_"+divid).style.display = "block";
}

//-------------------------------------------
//Detta scriptet öppnar onlinespelen
//-------------------------------------------
//function openwindow(URL,winName,features) { 
//  window.open(URL,winName,features);
//}

function openwindow(URL,winName,features)
{
window.open(URL,winName,"toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, copyhistory=no," + features)
}

//------------------------------------------
// Visar/döljer "Laddar..."-rutan
//------------------------------------------
function loading_state(state)
{
	if(state == 'hide')
	{
		document.getElementById('loading_div').style.display = 'none';
	}
	else
	{
		document.getElementById('loading_div').style.display = 'block';
	}
}


//-------------------------------------------
//Detta scriptet Begränsar antalet tecken i
//ett inmatningsfält.
//-------------------------------------------
function textCounter( field,  maxlimit ) {
  if ( field.value.length > maxlimit )
  {
    field.value = field.value.substring( 0, maxlimit );
    alert('Du får inte skriva fler än ' + maxlimit + ' tecken.' );
    return false;
  }
}


//-------------------------------------------
//Detta scriptet öppnar en bild och auto-
//justerar fönsterstorleken efter denna
//-------------------------------------------
function autoSizeWindow(filename,winTitle)
{
    var myImage = new Image();
    myImage.src=filename;
    properties='height=10,width=10';
    var imgWindow = window.open('','',properties);
    
    html = '<html>';
    html += '<head>';
    html += '<title>'+winTitle+'</title>';
    html += '</head>';
    html += '<body topmargin="0" marginheight="0" leftmargin="0" marginwidth="0">';
    html += '<a href="javascript: window.close();">';
    html += '<img src="'+myImage.src+'" onLoad="resizeTo(document.bild.width+10,document.bild.height+20);" name="bild" OnClick="self.close();" style="border: 1px solid #737373;">';
    html += '</a>';
    html += '</body>';
    html += '</html>';
    
    imgWindow.document.write(html);
}


//-------------------------------------------
//Detta script sköter uträkningen av falska
//personnummer.
//-------------------------------------------
function calc(gender)
{
year=document.forms['nummer'].year.options[document.forms['nummer'].year.selectedIndex].text
month=document.forms['nummer'].dropdown.options[document.forms['nummer'].dropdown.selectedIndex].text
ddate=document.forms['nummer'].date.options[document.forms['nummer'].date.selectedIndex].text
if (month == "Januari"){month = "01"}	//Fixa till månaderna
if (month == "Februari"){month = "02"}
if (month == "Mars"){month = "03"}
if (month == "April"){month = "04"}
if (month == "Maj"){month = "05"}
if (month == "Juni"){month = "06"}
if (month == "Juli"){month = "07"}
if (month == "Augusti"){month = "08"}
if (month == "September"){month = "09"}
if (month == "Oktober"){month = "10"}
if (month == "November"){month = "11"}
if (month == "December"){month = "12"}

if (gender=="male")//Om det är en kille
{
	var random_number = (Math.round(Math.random()*9 ))//Se till så att random_number är ojämnt
	switch (random_number)
	{
	case 0:
		random_number = random_number + 1
	break
	case 2:
		random_number = random_number + 1
	break
	case 4:
		random_number = random_number + 1
	break
	case 6:
		random_number = random_number + 1
	break
	case 8:
		random_number = random_number + 1
	}
	two_random = (Math.round(Math.random()*99))//Skapa ett slumptal som är max 100
	while (two_random < 10)//Om det är ett ensiffrigt tal, gör om tills det blir rätt.
	{
		two_random = (Math.round(Math.random()*99))
	}
	tresista = String(two_random)+String(random_number)//Lagra de tre talen i variabeln "tresista"
}
if (gender=="female")//Om tjej är vald
{
	var random_number = (Math.round(Math.random()*9 ))//Skapa ensiffrigt slumptal
	switch (random_number)//Se till så att bara jämna tal slipper igenom
	{
	case 1:
		random_number = random_number - 1
	break
	case 3:
		random_number = random_number - 1
	break
	case 5:
		random_number = random_number - 1
	break
	case 7:
		random_number = random_number - 1
	break
	case 9:
		random_number = random_number - 1
}
two_random = (Math.round(Math.random()*99))//Skapa slumptal under 100
while (two_random < 10)//Om det är ensiffrigt, görom tills det blir rätt
{
	two_random = (Math.round(Math.random()*99))
}
tresista = String(two_random)+String(random_number)//Lagra de tre talen i variabeln "tresista"
}

number=year+month+ddate+tresista//Lägg hela strängen i "number"

digit1=number.substr(0,1)//Lägg alla siffror i separata variabler
digit2=number.substr(1,1)
digit3=number.substr(2,1)
digit4=number.substr(3,1)
digit5=number.substr(4,1)
digit6=number.substr(5,1)
digit7=number.substr(6,1)
digit8=number.substr(7,1)
digit9=number.substr(8,1)

digit1=digit1 * 2//Gångra varannan siffra med två
digit3=digit3 * 2
digit5=digit5 * 2
digit7=digit7 * 2
digit9=digit9 * 2

if(Number(digit1) > 9)//Splitta tiotal till två ental
{
	digit11=String(digit1).substr(0,1)
	digit12=String(digit1).substr(1,1)
}
else
{
	digit11=digit1
	digit12=0
}
if(Number(digit3) > 9)
{
	digit31=String(digit3).substr(0,1)
	digit32=String(digit3).substr(1,1)
}
else
{
	digit31=digit3
	digit32=0
}
if(Number(digit5) > 9)
{
	digit51=String(digit5).substr(0,1)
	digit52=String(digit5).substr(1,1)
}
else
{
	digit51=digit5
	digit52=0
}
if(Number(digit7) > 9)
{
	digit71=String(digit7).substr(0,1)
	digit72=String(digit7).substr(1,1)
}
else
{
	digit71=digit7
	digit72=0
}
if(Number(digit9) > 9)
{
	digit91=String(digit9).substr(0,1)
	digit92=String(digit9).substr(1,1)
}
else
{
	digit91=digit9
	digit92=0
}

//Lägg in alla värden i "result"
result=(Number(digit11)+Number(digit12)+Number(digit2)+Number(digit31)+Number(digit32)+Number(digit4)+Number(digit51)+Number(digit52)+Number(digit6)+Number(digit71)+Number(digit72)+Number(digit8)+Number(digit91)+Number(digit92))

result=10-String(result).substr(1,1)//Dra bort entalssiffran från 10
if (result == 10){result = 0}//Om entalssiffra blev tio, sätt den till noll. (Detta inträffar vid 10 - 0)

finalnumber=String(number).substr(0,6)+"-"+String(number).substr(6,9)+String(result)//Skapa en sträng med det slutliga nummret
alert("Ditt falska personnummer är: " + finalnumber + "\n Scriptat för: hamsterpaj.net")//Ge användaren ett meddelande
}

// Jag lägger det här i mitten :)
function threads_module_create_thread() {
	var index = document.getElementById('threads_module_create_thread').selectedIndex;
	
	window.location = '/diskussionsforum/' + (document.getElementById('threads_module_create_thread').options[index].value) + '/#new_thread';
}



//-------------------------------------------
//låter folk använda fetstilt, understruken och
//kursiv text utan att kunna taggar.
//-------------------------------------------
var fetstatus = 'off';
var kursivstatus = 'off';
var underlinestatus = 'off';
function textTags(path, type, button){
	if(type == 'fet'){
		if(fetstatus == 'off'){
			path.value = path.value + '[b]';
			fetstatus = 'on';
			button.value="Fetstilt av";
		}
		else if(fetstatus == 'on'){
			path.value = path.value + '[/b]';
			fetstatus = 'off';
			button.value="Fetstilt på";
		}
	}
	if(type == 'kursiv'){
		if(kursivstatus == 'off'){
			path.value = path.value + '[i]';
			kursivstatus = 'on';
			button.value="Kursivt av";
		}
		else if(kursivstatus == 'on'){
			path.value = path.value + '[/i]';
			kursivstatus = 'off';
			button.value="Kursivt på";
		}
	}
	if(type == 'underline'){
		if(underlinestatus == 'off'){
			path.value = path.value + '[u]';
			underlinestatus = 'on';
			button.value="Understruket av";
		}
		else if(underlinestatus == 'on'){
			path.value = path.value + '[/u]';
			underlinestatus = 'off';
			button.value="Understruket på";
		}
	}
		path.focus();
}

/*****************************\
 * COOKIE FUNCTIONS          *
\****************************/

function setCookie (name, value, lifespan, access_path) {
      
  var cookietext = name + "=" + escape(value)  
    if (lifespan != null) {  
      var today=new Date()     
      var expiredate = new Date()      
      expiredate.setTime(today.getTime() + 1000*60*60*24*lifespan)
      cookietext += "; expires=" + expiredate.toGMTString()
    }
    if (access_path != null) { 
      cookietext += "; PATH="+access_path 
    }
   document.cookie = cookietext 
   return null  
}


function setDatedCookie(name, value, expire, access_path) {
    var cookietext = name + "=" + escape(value)
      + ((expire == null) ? "" : ("; expires=" + expire.toGMTString()))
     if (access_path != null) { 
      cookietext += "; PATH="+access_path 
     }
   document.cookie = cookietext 
   return null        
}


function getCookie(Name) {
  var search = Name + "="                       
  var CookieString = document.cookie            
  var result = null                               
  if (CookieString.length > 0) {                
    offset = CookieString.indexOf(search)       
    if (offset != -1) {                         
      offset += search.length                   
      end = CookieString.indexOf(";", offset)   
      if (end == -1)                            
        end = CookieString.length               
      result = unescape(CookieString.substring(offset, end))         
                                                
      } 
    }
   return result                                
}


function deleteCookie(Name, Path) {
  setCookie(Name,"Deleted", -1, Path)
}

function load_url(form) {
     var url = form.forum_list.options[form.forum_list.selectedIndex].value;
     if (url != '') location.href = url;
     return false;
}

/* Closes the bubble set by "the hamster". Assumes that div ID for the bubble is "bubble_main" */
function CloseBubble()
{
	document.getElementById("bubble_main").style.visibility = "hidden";
}





/* Dropbox */

function collapse_expand(boxID)
{
	var box = document.getElementById('dropbox_' + boxID);
	var boximg = document.getElementById('dropbox_image_' + boxID);
	
	if (box.style.display == 'none')
	{
		box.style.display = 'block';
		boximg.src = '/images/collapse.png';
	} else {
		box.style.display = 'none';
		boximg.src = '/images/expand.png';
	}
}

/* User-status editing */
function user_status_enable()
{
	if(document.getElementById('user_status_save_button'))
	{
		document.getElementById('user_status_input').onfocus = function()
		{
				document.getElementById('user_status_save_button').style.display = 'inline';		
		}
		document.getElementById('user_status_save_button').onclick = user_status_save;
	}
	
}

function user_status_save()
{
	this.style.display = 'none';
	xmlhttp_ping('/ajax_gateways/set_user_status.php?status=' + escape(document.getElementById('user_status_input').value));
}

womAdd('user_status_enable()');




	function gb_enable_anti_p12()
	{
		document.getElementById('gb_anti_p12').style.display = 'block';
		document.getElementById('btn_enable_gb_anti_p12').style.display = 'none';
		document.getElementById('btn_disable_gb_anti_p12').style.display = 'block';
		xmlhttp_ping('/xmlhttp_ping.php?action=enable_gb_anti_p12');
	}
	function gb_disable_anti_p12()
	{
		document.getElementById('gb_anti_p12').style.display = 'none';
		document.getElementById('btn_enable_gb_anti_p12').style.display = 'block';
		document.getElementById('btn_disable_gb_anti_p12').style.display = 'none';
		xmlhttp_ping('/xmlhttp_ping.php?action=disable_gb_anti_p12');	
	}
	
	function note_save()
	{
		var note = document.getElementById('note').value;
		note = escape(note);
		xmlhttp_ping('/note.php?note=' + note);
		alert('Din minneslapp har sparats!');
	}
	
	
/*
	Written by Jonathan Snook, http://www.snook.ca/jonathan
	Add-ons by Robert Nyman, http://www.robertnyman.com
	
To get all a elements in the document with a info-links class.
    getElementsByClassName(document, "a", "info-links");
To get all div elements within the element named container, with a col class.
    getElementsByClassName(document.getElementById("container"), "div", "col"); 
To get all elements within in the document with a click-me class.
    getElementsByClassName(document, "*", "click-me"); 
*/

function getElementsByClassName(oElm, strTagName, strClassName)
{
	var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
	var arrReturnElements = new Array();
	strClassName = strClassName.replace(/-/g, "\-");
	var oRegExp = new RegExp("(^|\s)" + strClassName + "(\s|$)");
	var oElement;
	for(var i=0; i<arrElements.length; i++)
	{
		oElement = arrElements[i];
		if(oRegExp.test(oElement.className))
		{
			arrReturnElements.push(oElement);
		}
	}
	return (arrReturnElements)
}


function hide_ie6_warning()
{
	document.getElementById('ie6_warning').style.display = 'none';	
	xmlhttp_ping('/ie6_warning.php');
}

function idiot_report(user_id)
{
	alert('Ooops! Fel rapportfunktion har anropats! Säg till Johan, för såhär ska det inte vara!');
}

function idiot_unreport(user_id)
{
	alert('Ooops! Fel rapportfunktion har anropats! Säg till Johan, för såhär ska det inte vara!');
}

function abuse_button_click()
{
	return confirm('Om du rapporterar inlägg eller personer i onödan skapar du en massa extrajobb för Hamsterpajs ordningsvakter. Då blir vi sura och tar bort ditt konto. Säker på att du vill rapportera?');
}

function abuse_buttons_enable()
{
	var buttons = getElementsByClassName(document, 'a', 'abuse_button');
	for(var i = 0; i < buttons.length; i++)
	{
		buttons[i].onclick = abuse_button_click;
	}
}


womAdd('abuse_buttons_enable()');