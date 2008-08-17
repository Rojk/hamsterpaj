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

function load_login_data(fragment_url, element_id,  inner_html) {
    var element = document.getElementById(element_id);

    xmlhttp.open("GET", fragment_url);
    xmlhttp.onreadystatechange = function()
    {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
      {
      	element.innerHTML = xmlhttp.responseText;
      	if(xmlhttp.responseText.indexOf('<script>') == 0)
		  	{
		  		eval(xmlhttp.responseText.substring(8, xmlhttp.responseText.indexOf('</script>')));
		  	}
      }
    }
    xmlhttp.send(null);
} 

function modules_fixer(fragment_url)
{
	xmlhttp.open("GET", fragment_url);
	xmlhttp.send(null);
} 
