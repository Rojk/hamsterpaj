var xmlhttp=false;
/*@cc_on @*/
/*@if (@_jscript_version >= 5)
// JScript gives us Conditional compilation, we can cope with old IE versions.
// and security blocked creation of the objects.
  try {
  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
   try {
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
   } catch (E) {
    xmlhttp = false;
   }
  }
@end @*/
if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
  xmlhttp = new XMLHttpRequest();
}

function loadFragmentInToElement(fragment_url, element_id) {
    var element = document.getElementById(element_id);
    element.innerHTML = 'Laddar...';
    xmlhttp.open("GET", fragment_url);
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
  	    element.innerHTML = xmlhttp.responseText;
		    //This makes sure that an initial script-tag is actually evaluated and run
		    if(xmlhttp.responseText.indexOf('<script>') == 0)
		    {
		    	eval(xmlhttp.responseText.substring(8, xmlhttp.responseText.indexOf('</script>')));
		    }
      }
    }
    xmlhttp.send(null);
} 

function loadFragmentInToElementByPOST(fragment_url, element_id, post_data)
{
	var element = document.getElementById(element_id);
	element.innerHTML = 'Laddar...';
	xmlhttp.open("POST", fragment_url);
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xmlhttp.onreadystatechange = function()
  {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
	    element.innerHTML = xmlhttp.responseText;
	    //This makes sure that an initial script-tag is actually evaluated and run
	    if(xmlhttp.responseText.indexOf('<script>') == 0)
	    {
	    	eval(xmlhttp.responseText.substring(8, xmlhttp.responseText.indexOf('</script>')));
	    }
    }
  }
	xmlhttp.send(post_data);
	
}
			

/* Fisk-kod...
function xmlhttp_ping(ping_url)
{
	xmlhttp.open('GET', ping_url);
	xmlhttp.send(null);
}*/

function xmlhttp_post_ping(ping_url, post_data)
{
	var loader = hp.give_me_an_AJAX();
	loader.open('POST', ping_url);
	loader.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	loader.send(post_data);
}

function xmlhttp_ping(ping_url, avoid_caching)
{
	if(typeof(avoid_caching) != 'undefined' && avoid_caching == true)
	{
		ping_url = ping_url + ((ping_url.indexOf('?') == -1) ? '&' : '?') + 'xmlhttp_ping_avoid_cache=' + Math.random();
	}
	
	var loader = hp.give_me_an_AJAX();
	loader.open('GET', ping_url);
	loader.send(null);
}