<?php

function get_IPTC( $Data_Str )
{

	// Initialise the start position
	$pos = 0;
	// Create the array to receive the data
	$OutputArray = array( );

	// Cycle through the IPTC records, decoding and storing them
	while( $pos < strlen($Data_Str) )
	{
		// TODO - Extended Dataset record not supported

		// Check if there is sufficient data for reading the record
		if ( strlen( substr($Data_Str,$pos) ) < 5 )
		{
			// Not enough data left for a record - Probably corrupt data - ERROR
			// Change: changed to return partial data as of revision 1.01
			return $OutputArray;
		}

		$iptc_raw = unpack( "CIPTC_Tag_Marker/CIPTC_Record_No/CIPTC_Dataset_No/nIPTC_Size", substr($Data_Str,$pos) );

		// Skip position over the unpacked data
		$pos += 5;

		// Construct the IPTC type string eg 2:105
		$iptctype = sprintf( "%01d:%02d", $iptc_raw['IPTC_Record_No'], $iptc_raw['IPTC_Dataset_No']);

		// Check if there is sufficient data for reading the record contents
		if ( strlen( substr( $Data_Str, $pos, $iptc_raw['IPTC_Size'] ) ) !== $iptc_raw['IPTC_Size'] )
		{
			// Not enough data left for the record content - Probably corrupt data - ERROR
			// Change: changed to return partial data as of revision 1.01
			return $OutputArray;
		}

		// Add the IPTC record to the output array
		$OutputArray[] = array( "IPTC_Type" => $iptctype ,
		"RecName" => $GLOBALS[ "IPTC_Entry_Names" ][ $iptctype ],
		"RecDesc" => $GLOBALS[ "IPTC_Entry_Descriptions" ][ $iptctype ],
		"RecData" => substr( $Data_Str, $pos, $iptc_raw['IPTC_Size'] ) );

		// Skip over the IPTC record data
		$pos += $iptc_raw['IPTC_Size'];
	}
	return $OutputArray;

}


/******************************************************************************
* End of Function:     get_IPTC
******************************************************************************/




/******************************************************************************
*
* Function:     put_IPTC
*
* Description:  Encodes an array of IPTC-NAA records into a string encoded
*               as IPTC-NAA IIM. (The reverse of get_IPTC)
*
* Parameters:   new_IPTC_block - the IPTC-NAA array to be encoded. Should be
*                                the same format as that received from get_IPTC
*
* Returns:      iptc_packed_data - IPTC-NAA IIM encoded string
*
******************************************************************************/


function put_IPTC( $new_IPTC_block )
{
	// Check if the incoming IPTC block is valid
	if ( $new_IPTC_block == FALSE )
	{
		// Invalid IPTC block - abort
		return FALSE;
	}
	// Initialise the packed output data string
	$iptc_packed_data = "";

	// Cycle through each record in the new IPTC block
	foreach ($new_IPTC_block as $record)
	{
		// Extract the Record Number and Dataset Number from the IPTC_Type field
		list($IPTC_Record, $IPTC_Dataset) = sscanf( $record['IPTC_Type'], "%d:%d");

		// Write the IPTC-NAA IIM Tag Marker, Record Number, Dataset Number and Data Size to the packed output data string
		$iptc_packed_data .= pack( "CCCn", 28, $IPTC_Record, $IPTC_Dataset, strlen($record['RecData']) );

		// Write the IPTC-NAA IIM Data to the packed output data string
		$iptc_packed_data .= $record['RecData'];
	}

	// Return the IPTC-NAA IIM data
	return $iptc_packed_data;
}

/******************************************************************************
* End of Function:     put_IPTC
******************************************************************************/


function Interpret_IPTC_to_HTML2( $IPTC_info )
{
	// Create a string to receive the HTML
	$output_str ="<br />";


	foreach( $IPTC_info as $IPTC_Record )
	{
		if ($IPTC_Record['IPTC_Type'] == '2:122')
		{
			$data['userid'] = $IPTC_Record['RecData'];
		}
		if ($IPTC_Record['IPTC_Type'] == '2:80')
		{
			$data['author'] = $IPTC_Record['RecData'];
		}
	}

	// Return HTML
	return $data;
}

/******************************************************************************
* Global Variable:      IPTC_Entry_Names
*
* Contents:     The names of the IPTC-NAA IIM fields
*
******************************************************************************/

$GLOBALS[ "IPTC_Entry_Names" ] = array(
// Envelope Record
"1:00" => "Model Version",
"1:05" => "Destination",
"1:20" => "File Format",
"1:22" => "File Format Version",
"1:30" => "Service Identifier",
"1:40" => "Envelope Number",
"1:50" => "Product ID",
"1:60" => "Envelope Priority",
"1:70" => "Date Sent",
"1:80" => "Time Sent",
"1:90" => "Coded Character Set",
"1:100" => "UNO (Unique Name of Object)",
"1:120" => "ARM Identifier",
"1:122" => "ARM Version",

// Application Record
"2:00" => "Record Version",
"2:03" => "Object Type Reference",
"2:05" => "Object Name (Title)",
"2:07" => "Edit Status",
"2:08" => "Editorial Update",
"2:10" => "Urgency",
"2:12" => "Subject Reference",
"2:15" => "Category",
"2:20" => "Supplemental Category",
"2:22" => "Fixture Identifier",
"2:25" => "Keywords",
"2:26" => "Content Location Code",
"2:27" => "Content Location Name",
"2:30" => "Release Date",
"2:35" => "Release Time",
"2:37" => "Expiration Date",
"2:35" => "Expiration Time",
"2:40" => "Special Instructions",
"2:42" => "Action Advised",
"2:45" => "Reference Service",
"2:47" => "Reference Date",
"2:50" => "Reference Number",
"2:55" => "Date Created",
"2:60" => "Time Created",
"2:62" => "Digital Creation Date",
"2:63" => "Digital Creation Time",
"2:65" => "Originating Program",
"2:70" => "Program Version",
"2:75" => "Object Cycle",
"2:80" => "By-Line (Author)",
"2:85" => "By-Line Title (Author Position) [Not used in Photoshop 7]",
"2:90" => "City",
"2:92" => "Sub-Location",
"2:95" => "Province/State",
"2:100" => "Country/Primary Location Code",
"2:101" => "Country/Primary Location Name",
"2:103" => "Original Transmission Reference",
"2:105" => "Headline",
"2:110" => "Credit",
"2:115" => "Source",
"2:116" => "Copyright Notice",
"2:118" => "Contact",
"2:120" => "Caption/Abstract",
"2:122" => "Caption Writer/Editor",
"2:125" => "Rasterized Caption",
"2:130" => "Image Type",
"2:131" => "Image Orientation",
"2:135" => "Language Identifier",
"2:150" => "Audio Type",
"2:151" => "Audio Sampling Rate",
"2:152" => "Audio Sampling Resolution",
"2:153" => "Audio Duration",
"2:154" => "Audio Outcue",
"2:200" => "ObjectData Preview File Format",
"2:201" => "ObjectData Preview File Format Version",
"2:202" => "ObjectData Preview Data",

// Pre-ObjectData Descriptor Record
"7:10"  => "Size Mode",
"7:20"  => "Max Subfile Size",
"7:90"  => "ObjectData Size Announced",
"7:95"  => "Maximum ObjectData Size",

// ObjectData Record
"8:10"  => "Subfile",

// Post ObjectData Descriptor Record
"9:10"  => "Confirmed ObjectData Size"

);

/******************************************************************************
* End of Global Variable:     IPTC_Entry_Names
******************************************************************************/





/******************************************************************************
* Global Variable:      IPTC_Entry_Descriptions
*
* Contents:     The Descriptions of the IPTC-NAA IIM fields
*
******************************************************************************/

$GLOBALS[ "IPTC_Entry_Descriptions" ] = array(
// Envelope Record
"1:00" => "2 byte binary version number",
"1:05" => "Max 1024 characters of Destination",
"1:20" => "2 byte binary file format number, see IPTC-NAA V4 Appendix A",
"1:22" => "Binary version number of file format",
"1:30" => "Max 10 characters of Service Identifier",
"1:40" => "8 Character Envelope Number",
"1:50" => "Product ID - Max 32 characters",
"1:60" => "Envelope Priority - 1 numeric characters",
"1:70" => "Date Sent - 8 numeric characters CCYYMMDD",
"1:80" => "Time Sent - 11 characters HHMMSS±HHMM",
"1:90" => "Coded Character Set - Max 32 characters",
"1:100" => "UNO (Unique Name of Object) - 14 to 80 characters",
"1:120" => "ARM Identifier - 2 byte binary number",
"1:122" => "ARM Version - 2 byte binary number",

// Application Record
"2:00" => "Record Version - 2 byte binary number",
"2:03" => "Object Type Reference -  3 plus 0 to 64 Characters",
"2:05" => "Object Name (Title) - Max 64 characters",
"2:07" => "Edit Status - Max 64 characters",
"2:08" => "Editorial Update - 2 numeric characters",
"2:10" => "Urgency - 1 numeric character",
"2:12" => "Subject Reference - 13 to 236 characters",
"2:15" => "Category - Max 3 characters",
"2:20" => "Supplemental Category - Max 32 characters",
"2:22" => "Fixture Identifier - Max 32 characters",
"2:25" => "Keywords - Max 64 characters",
"2:26" => "Content Location Code - 3 characters",
"2:27" => "Content Location Name - Max 64 characters",
"2:30" => "Release Date - 8 numeric characters CCYYMMDD",
"2:35" => "Release Time - 11 characters HHMMSS±HHMM",
"2:37" => "Expiration Date - 8 numeric characters CCYYMMDD",
"2:35" => "Expiration Time - 11 characters HHMMSS±HHMM",
"2:40" => "Special Instructions - Max 256 Characters",
"2:42" => "Action Advised - 2 numeric characters",
"2:45" => "Reference Service - Max 10 characters",
"2:47" => "Reference Date - 8 numeric characters CCYYMMDD",
"2:50" => "Reference Number - 8 characters",
"2:55" => "Date Created - 8 numeric characters CCYYMMDD",
"2:60" => "Time Created - 11 characters HHMMSS±HHMM",
"2:62" => "Digital Creation Date - 8 numeric characters CCYYMMDD",
"2:63" => "Digital Creation Time - 11 characters HHMMSS±HHMM",
"2:65" => "Originating Program - Max 32 characters",
"2:70" => "Program Version - Max 10 characters",
"2:75" => "Object Cycle - 1 character",
"2:80" => "By-Line (Author) - Max 32 Characters",
"2:85" => "By-Line Title (Author Position) - Max 32 characters",
"2:90" => "City - Max 32 Characters",
"2:92" => "Sub-Location - Max 32 characters",
"2:95" => "Province/State - Max 32 Characters",
"2:100" => "Country/Primary Location Code - 3 alphabetic characters",
"2:101" => "Country/Primary Location Name - Max 64 characters",
"2:103" => "Original Transmission Reference - Max 32 characters",
"2:105" => "Headline - Max 256 Characters",
"2:110" => "Credit - Max 32 Characters",
"2:115" => "Source - Max 32 Characters",
"2:116" => "Copyright Notice - Max 128 Characters",
"2:118" => "Contact - Max 128 characters",
"2:120" => "Caption/Abstract - Max 2000 Characters",
"2:122" => "Caption Writer/Editor - Max 32 Characters",
"2:125" => "Rasterized Caption - 7360 bytes, 1 bit per pixel, 460x128pixel image",
"2:130" => "Image Type - 2 characters",
"2:131" => "Image Orientation - 1 alphabetic character",
"2:135" => "Language Identifier - 2 or 3 aphabetic characters",
"2:150" => "Audio Type - 2 characters",
"2:151" => "Audio Sampling Rate - 6 numeric characters",
"2:152" => "Audio Sampling Resolution - 2 numeric characters",
"2:153" => "Audio Duration - 6 numeric characters",
"2:154" => "Audio Outcue - Max 64 characters",
"2:200" => "ObjectData Preview File Format - 2 byte binary number",
"2:201" => "ObjectData Preview File Format Version - 2 byte binary number",
"2:202" => "ObjectData Preview Data - Max 256000 binary bytes",

// Pre-ObjectData Descriptor Record
"7:10"  => "Size Mode - 1 numeric character",
"7:20"  => "Max Subfile Size",
"7:90"  => "ObjectData Size Announced",
"7:95"  => "Maximum ObjectData Size",

// ObjectData Record
"8:10"  => "Subfile",

// Post ObjectData Descriptor Record
"9:10"  => "Confirmed ObjectData Size"

);

/******************************************************************************
* End of Global Variable:     IPTC_Entry_Descriptions
******************************************************************************/




/******************************************************************************
* Global Variable:      IPTC_File Formats
*
* Contents:     The names of the IPTC-NAA IIM File Formats for field 1:20
*
******************************************************************************/

$GLOBALS[ "IPTC_File Formats" ] = array(
00 => "No ObjectData",
01 => "IPTC-NAA Digital Newsphoto Parameter Record",
02 => "IPTC7901 Recommended Message Format",
03 => "Tagged Image File Format (Adobe/Aldus Image data)",
04 => "Illustrator (Adobe Graphics data)",
05 => "AppleSingle (Apple Computer Inc)",
06 => "NAA 89-3 (ANPA 1312)",
07 => "MacBinary II",
08 => "IPTC Unstructured Character Oriented File Format (UCOFF)",
09 => "United Press International ANPA 1312 variant",
10 => "United Press International Down-Load Message",
11 => "JPEG File Interchange (JFIF)",
12 => "Photo-CD Image-Pac (Eastman Kodak)",
13 => "Microsoft Bit Mapped Graphics File [*.BMP]",
14 => "Digital Audio File [*.WAV] (Microsoft & Creative Labs)",
15 => "Audio plus Moving Video [*.AVI] (Microsoft)",
16 => "PC DOS/Windows Executable Files [*.COM][*.EXE]",
17 => "Compressed Binary File [*.ZIP] (PKWare Inc)",
18 => "Audio Interchange File Format AIFF (Apple Computer Inc)",
19 => "RIFF Wave (Microsoft Corporation)",
20 => "Freehand (Macromedia/Aldus)",
21 => "Hypertext Markup Language - HTML (The Internet Society)",
22 => "MPEG 2 Audio Layer 2 (Musicom), ISO/IEC",
23 => "MPEG 2 Audio Layer 3, ISO/IEC",
24 => "Portable Document File (*.PDF) Adobe",
25 => "News Industry Text Format (NITF)",
26 => "Tape Archive (*.TAR)",
27 => "Tidningarnas Telegrambyrå NITF version (TTNITF DTD)",
28 => "Ritzaus Bureau NITF version (RBNITF DTD)",
29 => "Corel Draw [*.CDR]"
);


/******************************************************************************
* End of Global Variable:     IPTC_File Formats
******************************************************************************/

/******************************************************************************
* Global Variable:      ImageType_Names
*
* Contents:     The names of the colour components for IPTC-NAA IIM field 2:130
*
******************************************************************************/

$GLOBALS['ImageType_Names'] = array(    "M" => "Monochrome",
"Y" => "Yellow Component",
"M" => "Magenta Component",
"C" => "Cyan Component",
"K" => "Black Component",
"R" => "Red Component",
"G" => "Green Component",
"B" => "Blue Component",
"T" => "Text Only",
"F" => "Full colour composite, frame sequential",
"L" => "Full colour composite, line sequential",
"P" => "Full colour composite, pixel sequential",
"S" => "Full colour composite, special interleaving" );



/******************************************************************************
* End of Global Variable:     ImageType_Names
******************************************************************************/

?>