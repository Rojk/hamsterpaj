var xmlHttp

function reportPhoto(photo_id)
{
if (str.length==0)
  { 
  document.getElementById("txtHint").innerHTML="";
  return;
  }
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert("Din webbläsare stödjer inte AJAX :( skaffa en ny :) \\n http://getfirefox.com");
  return;
  } 
var target_url = "/ajax_gateways/photos_abuse_gateway.php";
target_url =target_url+"?photo_id=" + photo_id;
xmlHttp.onreadystatechange=reportDone;
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
} 

function reportDone() 
{ 
if (xmlHttp.readyState==4)
{ 
	alert('Bilden är rapporterad.\\n tack för din rapport :)');
}
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}