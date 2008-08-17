<?php
include "Unicode.php";

function put_photoshop_file_info( $jpeg_header_data, $new_ps_file_info_array)
{
	/*******************************************/
	// PREPROCESSING

	// Check that the date is in the correct format (YYYY-MM-DD)

	// Explode the date into pieces using the - symbol
	$date_pieces = explode( "-", $new_ps_file_info_array[ 'date' ] );

	// If there are not 3 pieces to the date, it is invalid
	if ( count( $date_pieces ) != 3 )
	{
		// INVALID DATE
		echo "Invalid Date - must be YYYY-MM-DD format<br>";
		return FALSE;
	}

	// Cycle through each piece of the date
	foreach( $date_pieces as $piece )
	{
		// If the piece is not numeric, then the date is invalid.
		if ( ! is_numeric( $piece ) )
		{
			// INVALID DATE
			echo "Invalid Date - must be YYYY-MM-DD format<br>";
			return FALSE;
		}
	}

	// Make a unix timestamp at midnight on the date specified
	$date_stamp = mktime( 0,0,0, $date_pieces[1], $date_pieces[2], $date_pieces[0] );

	// Create a translation table to remove carriage return characters
	$trans = array( "\x0d" => "" );

	// Cycle through each of the File Info elements
	foreach( $new_ps_file_info_array as $valkey => $val )
	{
		// If the element is 'Keywords' or 'Supplemental Categories', then
		// it is an array, and needs to be treated as one
		if ( ( $valkey != 'supplementalcategories' ) && ( $valkey != 'keywords' ) )
		{
			// Not Keywords or Supplemental Categories
			// Convert escaped HTML characters to UTF8 and remove carriage returns
			$new_ps_file_info_array[ $valkey ] = strtr( HTML_UTF8_UnEscape( $val ), $trans );
		}
		else
		{
			// Either Keywords or Supplemental Categories
			// Cycle through the array,
			foreach( $val as $subvalkey => $subval )
			{
				// Convert escaped HTML characters to UTF8 and remove carriage returns
				$new_ps_file_info_array[ $valkey ][ $subvalkey ] = strtr( HTML_UTF8_UnEscape( $subval ), $trans );
			}
		}
	}

	// Photoshop IRB Processing

	$new_IRB_array = array();

	// Remove any existing Copyright Flag, URL, or IPTC resources - these will be re-written
	foreach( $new_IRB_array as  $resno => $res )
	{
		if ( ( $res[ 'ResID' ] == 0x040A ) ||
		( $res[ 'ResID' ] == 0x040B ) ||
		( $res[ 'ResID' ] == 0x0404 ) )
		{
			array_splice( $new_IRB_array, $resno, 1 );
		}
	}

	// Add a new Copyright Flag resource
	if ( $new_ps_file_info_array[ 'copyrightstatus' ] == "Copyrighted Work" )
	{
		$PS_copyright_flag = "\x01"; // Copyrighted
	}
	else
	{
		$PS_copyright_flag = "\x00"; // Public domain or Unmarked
	}
	$new_IRB_array[] = array(       'ResID' => 0x040A,
	'ResName' => $GLOBALS[ "Photoshop_ID_Names" ][0x040A],
	'ResDesc' => $GLOBALS[ "Photoshop_ID_Descriptions" ][0x040A],
	'ResEmbeddedName' => "",
	'ResData' => $PS_copyright_flag );

	// Add a new URL resource
	$new_IRB_array[] = array(       'ResID' => 0x040B,
	'ResName' => $GLOBALS[ "Photoshop_ID_Names" ][0x040B],
	'ResDesc' => $GLOBALS[ "Photoshop_ID_Descriptions" ][0x040B],
	'ResEmbeddedName' => "",
	'ResData' => $new_ps_file_info_array[ 'ownerurl' ] );

	// Create IPTC resource

	// IPTC requires date to be in the following format YYYYMMDD
	$iptc_date = date( "Ymd", $date_stamp );

	// Create the new IPTC array
	$new_IPTC_array = array (
	0 =>
	array (
	'IPTC_Type' => '2:00',
	'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:00'],
	'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:00'],
	'RecData' => "\x00\x02",
	),
	2 =>
	array (
	'IPTC_Type' => '2:122',
	'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:122'],
	'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:122'],
	'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'captionwriter' ] ), 0 , 32 ),
	),
	5 =>
	array (
	'IPTC_Type' => '2:80',
	'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:80'],
	'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:80'],
	'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'author' ] ), 0, 32 ),
	),
	10 =>
	array (
	'IPTC_Type' => '2:55',
	'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:55'],
	'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:55'],
	'RecData' => "$iptc_date",
	),
	);

	// FINISHED UPDATING VALUES

	// Insert the new IPTC array into the Photoshop IRB array
	$new_IRB_array = put_Photoshop_IPTC( $new_IRB_array, $new_IPTC_array );

	// Write the Photoshop IRB array to the JPEG header
	$jpeg_header_data = put_Photoshop_IRB( $jpeg_header_data, $new_IRB_array );

	return $jpeg_header_data;

}

/******************************************************************************
* End of Function:     put_photoshop_file_info
******************************************************************************/

?>