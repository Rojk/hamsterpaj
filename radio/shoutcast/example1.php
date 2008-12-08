<?php
/***************************************************************************
 *                          ShoutcastInfo Example 1
 *                          -----------------------
 *   begin                : Wednesday, Aug 18, 2004 - 06:27
 *   copyright            : (C) 2004 MC Breit
 *   email                : support@mcb.cc - MCB.CC - Free and Open Sources
 *   last modified        : 18/08/04 - 06:30 - MC Breit
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
 *   This example will show how easy to get all the facts about any stream.
 *   In this example we will connect to the stream from Radio-GaGa which
 *   gots the URL http://samtastic.net:8888/listen.pls
 *
 *   We will use a text/plain sheme, because this makes it easy to dump
 *   the datas via phps print_r function; by using this its allso usefull
 *   to havnt any html inside a connection error (If happend), this is
 *   why we wrote `die($scs->error(TRUE))` instead of just $scs->error().
 *
 *   In all examples $scs means the handle of our object, it is an acronym
 *   for ShoutCastServer; You can use any valid variable name you want to.
 *
 *   We will not give an port and an timeout, because 8888 is the default
 *   port, and mostly 30 is a good timeout value. So we didnt need.
 *
 *   To analyse the error handling used inner this, you can modfiy the
 *   hostname to example.com or can give a wrong port, so it wouldnt work.
 *
 ***************************************************************************/

//
// Begin ShoutcastInfo example with stream from Radio-GaGa..
//

//IMPORTANT: Incude the class..
include_once('./ShoutcastInfo.class.php');


//Send a text/plain header (IE will ignore this :-/)
header('Content-Type: text/plain');

//Now we want to create our handle.
//Normaly we have to give hostname and port and-or timeout.
//Because Radio-GaGa uses port 8888 we wouldnt need.
$scs = &new ShoutcastInfo('samtastic.net');

//Connect to server
//If an error is happen, it will return false
//and we can handle insite the if brackets.
if( !$scs->connect() )
{
  //Any error happend?
  //Print it out! (e.g. die($scs->error(TRUE)); because of our text/plain header)
  die($scs->error(TRUE));
}

//After connection is etablished, we want to send our request to the server.
//Also we will get the datas with this, but it doesnt matther that it is
//in one function, because we need those 2 thinks ecentialy.
$scs->send();

//Now we want to parse the complete datas we recived, because we want to dump
//all aviable infos :P
$data = $scs->parse();

//After rescuing our datas to $data we can close our connection,
//we wouldnt need it, else we can reconnect with $scs->refresh()
$scs->close();

//Now we want to print out the datas..
//This is very simple using an print_r,
//because we fetched an assoc array from $scs->parse..
print_r($data);

//
// Thats it folks!
//

?>