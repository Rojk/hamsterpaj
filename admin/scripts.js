//-------------------------------------------
//Detta scriptet öppnar onlinespelen
//-------------------------------------------
function openwindow(URL,winName,features) { 
  window.open(URL,winName,features);
}
//-------------------------------------------
//
//-------------------------------------------



//-------------------------------------------
//Scriptet är Copyright 2003. Upphovsman
//Johan Höglund. Alla rättigheter är
//Förbehållna Johan Höglund, även kallad
//"Schneaker".
//Har du frågor eller synpunkter? Maila mig!
// Schneaker@fyrskeppet.net
//-------------------------------------------


//-------------------------------------------
//Starta uträkningsfunktionen
//-------------------------------------------
function calc(gender)
{


//-------------------------------------------
//Plocka ut födelseår och lägg i "year"
//-------------------------------------------
year=document.forms[0].year.options[document.forms[0].year.selectedIndex].text
//-------------------------------------------
//Nu ligger årtalet i variabeln "year"
//-------------------------------------------



//-------------------------------------------
//Plocka ut månadsnummer och lägg i "month"
//-------------------------------------------
month=document.forms[0].dropdown.options[document.forms[0].dropdown.selectedIndex].text
if (month == "Januari") 
{
month = "01"
}
if (month == "Februari") 
{
month = "02"
}
if (month == "Mars") 
{
month = "03"
}
if (month == "April") 
{
month = "04"
}
if (month == "Maj") 
{
month = "05"
}
if (month == "Juni") 
{
month = "06"
}
if (month == "Juli") 
{
month = "07"
}
if (month == "Augusti") 
{
month = "08"
}
if (month == "September") 
{
month = "09"
}
if (month == "Oktober") 
{
month = "10"
}
if (month == "November") 
{
month = "11"
}
if (month == "December") 
{
month = "12"
}
//-------------------------------------------
//Nu ligger månadsnummret i variabeln "month"
//-------------------------------------------


//-------------------------------------------
//Plocka ut födelsedatum och lägg i "date"
//-------------------------------------------
ddate=document.forms[0].date.options[document.forms[0].date.selectedIndex].text
//-------------------------------------------
//Nu ligger datumet i variabeln "date"
//-------------------------------------------


//-------------------------------------------
//Om kille vald, sätt tresista
//-------------------------------------------
if (gender=="male")
{
var random_number = (Math.round(Math.random()*9 ))
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
two_random = (Math.round(Math.random()*99))
while (two_random < 10)
{
two_random = (Math.round(Math.random()*99))
}
tresista = String(two_random)+String(random_number)
}
//-------------------------------------------
//tresista satt om kille vald
//-------------------------------------------
//Om tjej vald, sätt tresista
//-------------------------------------------
if (gender=="female")
{
var random_number = (Math.round(Math.random()*9 ))
switch (random_number)
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
two_random = (Math.round(Math.random()*99))
while (two_random < 10)
{
two_random = (Math.round(Math.random()*99))
}
tresista = String(two_random)+String(random_number)
}
//-------------------------------------------
//tresista satt om tjej vald
//-------------------------------------------


//-------------------------------------------
//Lägg hela strängen i "number"
//-------------------------------------------
number=year+month+ddate+tresista
//-------------------------------------------
//Hela strängen ligger i "number"
//-------------------------------------------


//-------------------------------------------
//Plocka ut en siffra i taget ur "number"
//-------------------------------------------
digit1=number.substr(0,1)
digit2=number.substr(1,1)
digit3=number.substr(2,1)
digit4=number.substr(3,1)
digit5=number.substr(4,1)
digit6=number.substr(5,1)
digit7=number.substr(6,1)
digit8=number.substr(7,1)
digit9=number.substr(8,1)
//-------------------------------------------
//Sluta plocka ut siffror ur "number"
//-------------------------------------------


//-------------------------------------------
//Gångra varannan siffra med två
//-------------------------------------------
digit1=digit1 * 2
digit3=digit3 * 2
digit5=digit5 * 2
digit7=digit7 * 2
digit9=digit9 * 2
//-------------------------------------------
//Sluta gångra varannan siffra med två
//-------------------------------------------


//-------------------------------------------
//Splitta tiotal till två ental
//-------------------------------------------
if(Number(digit1) > 9)
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
//-------------------------------------------
//Sluta splitta tiotal till två ental
//-------------------------------------------

//-------------------------------------------
//Addera alla siffror till "result"
//-------------------------------------------
result=(Number(digit11)+Number(digit12)+Number(digit2)+Number(digit31)+Number(digit32)+Number(digit4)+Number(digit51)+Number(digit52)+Number(digit6)+Number(digit71)+Number(digit72)+Number(digit8)+Number(digit91)+Number(digit92))
//-------------------------------------------
//Sluta addera alla siffror till "result"
//-------------------------------------------


//-------------------------------------------
//Dra bort entalssiffran i "result" från 10
//-------------------------------------------
result=10-String(result).substr(1,1)
//-------------------------------------------
//Sluta dra bort entalssiffran från 10
//-------------------------------------------


//-------------------------------------------
//Blev entalssiffran 10, sätt den till 0
//-------------------------------------------
if (result == 10)
{
result = 0
}
//-------------------------------------------
//Sluta sätta entalssifran till 0
//-------------------------------------------


//-------------------------------------------
//Skapa en sträng med det färdiga numret
//-------------------------------------------
finalnumber=String(number).substr(0,6)+"-"+String(number).substr(6,9)+String(result)
//-------------------------------------------
//Sluta skapa strängen
//-------------------------------------------


alert("Ditt falska personnummer är: " + finalnumber + "\n Scriptat för: hamsterpaj.net")
}
//----------------------------------
//Slut på scriptet
//----------------------------------