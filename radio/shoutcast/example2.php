<?php
/***************************************************************************
 *                          ShoutcastInfo Example 2
 *                          -----------------------
 *   begin                : Wednesday, Aug 18, 2004 - 06:45
 *   copyright            : (C) 2004 MC Breit
 *   email                : support@mcb.cc - MCB.CC - Free and Open Sources
 *   last modified        : 18/08/04 - 07:42 - MC Breit
 *   version              : 1.0.0
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This example will show how easy to get all the facts about any stream
 *   and build our own stream info site..
 *   This time we will use the single parse options, but it is also
 *   possible by the way of example 1.
 *
 *   Also I will show how to make a good error handling an check up
 *   that the stream is online or not.
 *   In this example we will connect to the stream from SoundTech which
 *   gots the URL http://GN-Hosting.de:8888/listen.pls
 *
 *   In all examples $scs means the handle of our object, it is an acronym
 *   for ShoutCastServer; You can use any valid variable name you want to.
 *
 *   To analyse the error handling used inner this, you can modfiy the
 *   hostname to example.com or can give a wrong port, so it wouldnt work.
 *
 ***************************************************************************/
 
//
// Begin ShoutcastInfo example with stream from SoundTechnology..
//

//IMPORTANT: Incude the class..
include_once('./ShoutcastInfo.class.php');

//First we want to print out some html code, e.g.: our page header *g*
?>
<html>
 <head>
  <title>ShoutcastInfo Class Example 2 - With Webradio SoundTechnology</title>
 </head>
 <body style="background:black;color:gray;font-family:Verdana">
 <table width="80%" align="center" style="border:1px solid black;border-collapse:collapse;background:skyblue;color:black;">
  <tr>
   <th colspan="2" style="font-size:120%">Webradio Sound Technology stream!</th>
  </tr>
<?php
//So, because at moment we dont know is the stream aviable or not, we will
//check this up first.

//So we want to create our handle.
//Normaly we have to give hostname and port and-or timeout.
//Because SoundTech uses port 8888 we wouldnt need.
$scs = &new ShoutcastInfo('GN-Hosting.de');

//Connect to server
//If an error is happen, it will return false
//and we can handle insite the if brackets.
if( !$scs->connect() )
{
  //Any error happend? Print it out!
  //But do not quit, we need our footer!
  //So we want to set $error to TRUE, to check up later what to do and so on..
  print '<tr><td colspan="2"> Sorry, but there was an error occurent, trying to connect to the Server.<br />';
  print 'In hope it helps, here an error message: ';
  $scs->error();
  print 'Iam really sorry about it!</td></tr>';
  //Set $error to true:
  $error = TRUE;
}


if( $error != TRUE )
{
  //If connection is etablished, we want to go further to get check up that server is up and private..
  //So we need to send somethin to the server, to get our datas :P
  $scs->send();
  
  //Now we want to check up, is the radio online or not?
  //So we use the function get_stat which returns 0 for offline and 1 for online,
  //coverting this to bool will make FALSE => offline, TRUE => Online
  if( !$scs->get_stat() ) //If server isn't online..
  {
    //Print out something like, sorry, but at moment no djs at work..
    print '<tr><td colspan="2">Sorry, but at moment the radio is offline. So you cant listening.<br> The djs are to lazy to provide a playlist, you know?</td></tr>';
  }
  else //If server is online..
  {
  
    //we want to publish a link that the potential listeners can listen :P
    print '<tr><td colspan="2"><center><a href="http://GN-Hosting.de:8888/listen.pls">Click here to listen!</a></center></td></tr>';
  
    //Now we want to bring some informations to the visitor,
    //like which song is playling, which dj/mod is onair and how much listeners are there.
    
    //The dj/mod at work using the ->get_title() method:
    print '<tr><td>Currently for you onAir:</td>';
    print '<td>'.$scs->get_title().'</td></tr>';
  
    //The song is playing at moment using the ->get_track() method:
    print '<tr><td>Current Track:</td>';
    print '<td>'.$scs->get_track().'</td></tr>';
    
    //The listeners are listening to stream, using ->get_listenrs:
    print '<tr><td>Listeners:</td>';
    //STOP! We also want to show how much is the maximal, but ther is no get_max_listerners, what to do?
    //Now you have to use the ->get_parsed_value method, because the max listeners will parsed in one with
    //with the current listeners, "listener_max" will be aviable after calling it.
    //So we will call for the listener:
    $listeners = $scs->get_listener();
    //Now we can also get the max listeners:
    $maxlisteners = $scs->get_parsed_value('listener_max');
    //And print it out:
    print '<td>'.$listeners.' of max: '.$maxlisteners.'</td></tr>';
    
    //NOTE:
    //The reason why i build in the get_parsed_value function is, that
    //after getting for example listenrs one time, it is faster to use the values
    //we allready used, but how o get them without parse for them agian?
    //Answer: use ->get_parsed_value('listener');
    
    //And at least, we want to provide our peak using ->get_peak().
    print '<tr><td>Our Peak:</td>';
    print '<td>'.$scs->get_peak().'</td></tr>';  
    
    
  } //Thats it!
  
  //At least, we have to close our connection.
  //This must be server is on or not, we opend one, we have to close one :P
  $scs->close();
  
} //end if no error was made

//So, now our page footer:
?>
  </table>
   <!-- You havn't to print this out, but i think it wuld be nice :P -->
  <center>Powered by ShoutcastInfo Class from <a href="http://www.mcb.cc/">MCB.CC</a></center>
 </body>
</html>
<?php
  
//
// Thats it folks!
//

?>