
function setOpacity(inputobj,value)
{inputobj.style.filter="alpha(opacity="+value*10+")";inputobj.style.opacity=value/10;inputobj.style.MozOpacity=value/10;}
function selectGender(selectid,gender)
{setOpacity(document.getElementById(selectid),5);window.location='?gender='+gender;}