<?
include_once "IPTC.php";
function get_Photoshop_IRB( $jpeg_header_data )
{
	// Photoshop Image Resource blocks can span several JPEG APP13 segments, so we need to join them up if there are more than one
	$joined_IRB = "";


	//Cycle through the header segments
	for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
	{
		// If we find an APP13 header,
		if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP13" ) == 0 )
		{
			// And if it has the photoshop label,
			if( strncmp ( $jpeg_header_data[$i]['SegData'], "Photoshop 3.0\x00", 14) == 0 )
			{
				// join it to the other previous IRB data
				$joined_IRB .= substr ( $jpeg_header_data[$i]['SegData'], 14 );
			}
		}
	}

	// If there was some Photoshop IRB information found,
	if ( $joined_IRB != "" )
	{
		// Found a Photoshop Image Resource Block - extract it.
		// Change: Moved code into unpack_Photoshop_IRB_Data to allow TIFF reading as of 1.11
		return unpack_Photoshop_IRB_Data( $joined_IRB );

	}
	else
	{
		// No Photoshop IRB found
		return FALSE;
	}

}

/******************************************************************************
* End of Function:     get_Photoshop_IRB
******************************************************************************/

/******************************************************************************
*
* Function:     put_Photoshop_IRB
*
* Description:  Adds or modifies the Photoshop Information Resource Block (IRB)
*               information from an App13 JPEG segment. If a Photoshop IRB already
*               exists, it is replaced, otherwise a new one is inserted, using the
*               supplied data. Uses information supplied by the get_jpeg_header_data
*               function
*
* Parameters:   jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data
*               new_IRB_data - an array of the data to be stored in the Photoshop
*                              IRB segment. Should be in the same format as received
*                              from get_Photoshop_IRB
*
* Returns:      jpeg_header_data - the JPEG header data array with the
*                                  Photoshop IRB added.
*               FALSE - if an error occured
*
******************************************************************************/

function put_Photoshop_IRB( $jpeg_header_data, $new_IRB_data )
{
	// Delete all existing Photoshop IRB blocks - the new one will replace them

	//Cycle through the header segments
	for( $i = 0; $i < count( $jpeg_header_data ) ; $i++ )
	{
		// If we find an APP13 header,
		if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP13" ) == 0 )
		{
			// And if it has the photoshop label,
			if( strncmp ( $jpeg_header_data[$i]['SegData'], "Photoshop 3.0\x00", 14) == 0 )
			{
				// Delete the block information - it needs to be rebuilt
				array_splice( $jpeg_header_data, $i, 1 );
			}
		}
	}


	// Now we have deleted the pre-existing blocks

	// Retrieve the Packed Photoshop IRB Data
	// Change: Moved code into pack_Photoshop_IRB_Data to allow TIFF writing as of 1.11
	$packed_IRB_data = pack_Photoshop_IRB_Data( $new_IRB_data );

	//Cycle through the header segments in reverse order (to find where to put the APP13 block - after any APP0 to APP12 blocks)
	$i = count( $jpeg_header_data ) - 1;
	while (( $i >= 0 ) && ( ( $jpeg_header_data[$i]['SegType'] > 0xED ) || ( $jpeg_header_data[$i]['SegType'] < 0xE0 ) ) )
	{
		$i--;
	}

	// Cycle through the packed output data until it's size is less than 32000 bytes, outputting each 32000 byte block to an APP13 segment
	while ( strlen( $packed_IRB_data ) > 32000 )
	{
		// Change: Fixed put_Photoshop_IRB to output "Photoshop 3.0\x00" string with every APP13 segment, not just the first one, as of 1.03

		// Write a 32000 byte APP13 segment
		array_splice($jpeg_header_data, $i +1  , 0, array(  "SegType" => 0xED,
		"SegName" => "APP13",
		"SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xED ],
		"SegData" => "Photoshop 3.0\x00" . substr( $packed_IRB_data,0,32000) ) );

		// Delete the 32000 bytes from the packed output data, that were just output
		$packed_IRB_data = substr_replace($packed_IRB_data, '', 0, 32000);
		$i++;
	}

	// Write the last block of packed output data to an APP13 segment - Note array_splice doesn't work with multidimensional arrays, hence inserting a blank string
	array_splice($jpeg_header_data, $i + 1 , 0, "" );
	$jpeg_header_data[$i + 1] =  array( "SegType" => 0xED,
	"SegName" => "APP13",
	"SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xED ],
	"SegData" => "Photoshop 3.0\x00" . $packed_IRB_data );

	return $jpeg_header_data;
}

/******************************************************************************
* End of Function:     put_Photoshop_IRB
******************************************************************************/


/******************************************************************************
*
* Function:     get_Photoshop_IPTC
*
* Description:  Retrieves IPTC-NAA IIM information from within a Photoshop
*               IRB (if it is present) and returns it in an array. Uses
*               information supplied by the get_jpeg_header_data function
*
* Parameters:   Photoshop_IRB_data - an array of Photoshop IRB records, as
*                                    returned from get_Photoshop_IRB
*
* Returns:      IPTC_Data_Out - The array of IPTC-NAA IIM records
*               FALSE - if an IPTC-NAA IIM record could not be found, or if
*                       an error occured
*
******************************************************************************/

function get_Photoshop_IPTC( $Photoshop_IRB_data )
{

	// Change: Initialise array correctly, as of revision 1.10
	$IPTC_Data_Out = array();

	//Cycle through the Photoshop 8BIM records looking for the IPTC-NAA record
	for( $i = 0; $i < count( $Photoshop_IRB_data ); $i++ )
	{
		// Check if each record is a IPTC record (which has id 0x0404)
		if ( $Photoshop_IRB_data[$i]['ResID']  == 0x0404 )
		{
			// We've found an IPTC block - Decode it
			$IPTC_Data_Out = get_IPTC( $Photoshop_IRB_data[$i]['ResData'] );
		}
	}

	// If there was no records put into the output array,
	if ( count( $IPTC_Data_Out ) == 0 )
	{
		// Then return false
		return FALSE;
	}
	else
	{
		// Otherwise return the array
		return $IPTC_Data_Out;
	}

}
/******************************************************************************
* End of Function:     get_Photoshop_IPTC
******************************************************************************/






/******************************************************************************
*
* Function:     put_Photoshop_IPTC
*
* Description:  Inserts a new IPTC-NAA IIM resource into a Photoshop
*               IRB, or replaces an the existing resource if one is present.
*               Uses information supplied by the get_Photoshop_IRB function
*
* Parameters:   Photoshop_IRB_data - an array of Photoshop IRB records, as
*                                    returned from get_Photoshop_IRB, into
*                                    which the IPTC-NAA IIM record will be inserted
*               new_IPTC_block - an array of IPTC-NAA records in the same format
*                                as those returned by get_Photoshop_IPTC
*
* Returns:      Photoshop_IRB_data - The Photoshop IRB array with the
*                                     IPTC-NAA IIM resource inserted
*
******************************************************************************/

function put_Photoshop_IPTC( $Photoshop_IRB_data, $new_IPTC_block )
{
	$iptc_block_pos = -1;

	//Cycle through the 8BIM records looking for the IPTC-NAA record
	for( $i = 0; $i < count( $Photoshop_IRB_data ); $i++ )
	{
		// Check if each record is a IPTC record (which has id 0x0404)
		if ( $Photoshop_IRB_data[$i]['ResID']  == 0x0404 )
		{
			// We've found an IPTC block - save the position
			$iptc_block_pos = $i;
		}
	}

	// If no IPTC block was found, create a new one
	if ( $iptc_block_pos == -1 )
	{
		// New block position will be at the end of the array
		$iptc_block_pos = count( $Photoshop_IRB_data );
	}


	// Write the new IRB resource to the Photoshop IRB array with no data
	$Photoshop_IRB_data[$iptc_block_pos] = array(   "ResID" =>   0x0404,
	"ResName" => $GLOBALS['Photoshop_ID_Names'][ 0x0404 ],
	"ResDesc" => $GLOBALS[ "Photoshop_ID_Descriptions" ][ 0x0404 ],
	"ResEmbeddedName" => "\x00\x00",
	"ResData" => put_IPTC( $new_IPTC_block ) );


	// Return the modified IRB
	return $Photoshop_IRB_data;
}

/******************************************************************************
* End of Function:     put_Photoshop_IPTC
******************************************************************************/

function Interpret_IRB_to_HTML2( $IRB_array, $filename )
{
	// Create a string to receive the HTML
	$output_str = "";

	// Check if the Photoshop IRB array is valid
	if ( $IRB_array !== FALSE )
	{

		// Create another string to receive secondary HTML to be appended at the end
		$secondary_output_str = "";

		// Cycle through each of the Photoshop IRB records, creating HTML for each
		foreach( $IRB_array as $IRB_Resource )
		{
			// Add HTML for the resource as appropriate
			switch ( $IRB_Resource['ResID'] )
			{

				case 0x0404 : // IPTC-NAA IIM Record
				$secondary_output_array = Interpret_IPTC_to_HTML2( get_IPTC( $IRB_Resource['ResData'] ) );
				break;

				case 0x040A : // Copyright Marked
				if ( hexdec( bin2hex( $IRB_Resource['ResData'] ) ) == 1 )
				{
					$img_info['copyright'] = 1;
				}
				else
				{
					$img_info['copyright'] = 0;
				}
				break;

			}
		}

	}
	$output = array_merge($img_info, $secondary_output_array);
	// Return the HTML
	return $output;
}




/******************************************************************************
*
*         INTERNAL FUNCTIONS
*
******************************************************************************/







/******************************************************************************
*
* Function:     unpack_Photoshop_IRB_Data
*
* Description:  Extracts Photoshop Information Resource Block (IRB) information
*               from a binary string containing the IRB, as read from a file
*
* Parameters:   IRB_Data - The binary string containing the IRB
*
* Returns:      IRBdata - The array of Photoshop IRB records
*
******************************************************************************/

function unpack_Photoshop_IRB_Data( $IRB_Data )
{
	$pos = 0;

	// Cycle through the IRB and extract its records - Records are started with 8BIM, so cycle until no more instances of 8BIM can be found
	while ( ( $pos < strlen( $IRB_Data ) ) && ( ($pos = strpos( $IRB_Data, "8BIM", $pos) ) !== FALSE ) )
	{
		// Skip the position over the 8BIM characters
		$pos += 4;

		// Next two characters are the record ID - denoting what type of record it is.
		$ID = ord( $IRB_Data{ $pos } ) * 256 + ord( $IRB_Data{ $pos +1 } );

		// Skip the positionover the two record ID characters
		$pos += 2;

		// Next comes a Record Name - usually not used, but it should be a null terminated string, padded with 0x00 to be an even length
		$namestartpos = $pos;

		// Change: Fixed processing of embedded resource names, as of revision 1.10

		// NOTE: Photoshop does not process resource names according to the standard :
		// "Adobe Photoshop 6.0 File Formats Specification, Version 6.0, Release 2, November 2000"
		//
		// The resource name is actually formatted as follows:
		// One byte name length, followed by the null terminated ascii name string.
		// The field is then padded with a Null character if required, to ensure that the
		// total length of the name length and name is even.

		// Name - process it
		// Get the length
		$namelen = ord ( $IRB_Data{ $namestartpos } );

		// Total length of name and length info must be even, hence name length must be odd
		// Check if the name length is even,
		if ( $namelen % 2 == 0 )
		{
			// add one to length to make it odd
			$namelen ++;
		}
		// Extract the name
		$resembeddedname = trim( substr ( $IRB_Data, $namestartpos+1,  $namelen) );
		$pos += $namelen + 1;


		// Next is a four byte size field indicating the size in bytes of the record's data  - MSB first
		$datasize =     ord( $IRB_Data{ $pos } ) * 16777216 + ord( $IRB_Data{ $pos + 1 } ) * 65536 +
		ord( $IRB_Data{ $pos + 2 } ) * 256 + ord( $IRB_Data{ $pos + 3 } );
		$pos += 4;

		// The record is stored padded with 0x00 characters to make the size even, so we need to calculate the stored size
		$storedsize =  $datasize + ($datasize % 2);

		$resdata = substr ( $IRB_Data, $pos, $datasize );

		// Get the description for this resource
		// Check if this is a Path information Resource, since they have a range of ID's
		if ( ( $ID >= 0x07D0 ) && ( $ID <= 0x0BB6 ) )
		{
			$ResDesc = "ID Info : Path Information (saved paths).";
		}
		else
		{
			if ( array_key_exists( $ID, $GLOBALS[ "Photoshop_ID_Descriptions" ] ) )
			{
				$ResDesc = $GLOBALS[ "Photoshop_ID_Descriptions" ][ $ID ];
			}
			else
			{
				$ResDesc = "";
			}
		}

		// Get the Name of the Resource
		if ( array_key_exists( $ID, $GLOBALS[ "Photoshop_ID_Names" ] ) )
		{
			$ResName = $GLOBALS['Photoshop_ID_Names'][ $ID ];
		}
		else
		{
			$ResName = "";
		}


		// Store the Resource in the array to be returned

		$IRB_Array[] = array(     "ResID" => $ID,
		"ResName" => $ResName,
		"ResDesc" => $ResDesc,
		"ResEmbeddedName" => $resembeddedname,
		"ResData" => $resdata );

		// Jump over the data to the next record
		$pos += $storedsize;
	}

	// Return the array created
	return $IRB_Array;
}

/******************************************************************************
* End of Function:     unpack_Photoshop_IRB_Data
******************************************************************************/


/******************************************************************************
*
* Function:     pack_Photoshop_IRB_Data
*
* Description:  Packs a Photoshop Information Resource Block (IRB) array into it's
*               binary form, which can be written to a file
*
* Parameters:   IRB_data - an Photoshop IRB array to be converted. Should be in
*                          the same format as received from get_Photoshop_IRB
*
* Returns:      packed_IRB_data - the binary string of packed IRB data
*
******************************************************************************/

function pack_Photoshop_IRB_Data( $IRB_data )
{
	$packed_IRB_data = "";

	// Cycle through each resource in the IRB,
	foreach ($IRB_data as $resource)
	{

		// Change: Fix to avoid creating blank resources, as of revision 1.10

		// Check if there is actually any data for this resource
		if( strlen( $resource['ResData'] ) == 0 )
		{
			// No data for resource - skip it
			continue;
		}

		// Append the 8BIM tag, and resource ID to the packed output data
		$packed_IRB_data .= pack("a4n", "8BIM", $resource['ResID'] );


		// Change: Fixed processing of embedded resource names, as of revision 1.10

		// NOTE: Photoshop does not process resource names according to the standard :
		// "Adobe Photoshop 6.0 File Formats Specification, Version 6.0, Release 2, November 2000"
		//
		// The resource name is actually formatted as follows:
		// One byte name length, followed by the null terminated ascii name string.
		// The field is then padded with a Null character if required, to ensure that the
		// total length of the name length and name is even.

		// Append Name Size
		$packed_IRB_data .= pack( "c", strlen(trim($resource['ResEmbeddedName'])));

		// Append the Resource Name to the packed output data
		$packed_IRB_data .= trim($resource['ResEmbeddedName']);

		// If the resource name is even length, then with the addition of
		// the size it becomes odd and needs to be padded to an even number
		if ( strlen( trim($resource['ResEmbeddedName']) ) % 2 == 0 )
		{
			// then it needs to be evened up by appending another null
			$packed_IRB_data .= "\x00";
		}

		// Append the resource data size to the packed output data
		$packed_IRB_data .= pack("N", strlen( $resource['ResData'] ) );

		// Append the resource data to the packed output data
		$packed_IRB_data .= $resource['ResData'];

		// If the resource data is odd length,
		if ( strlen( $resource['ResData'] ) % 2 == 1 )
		{
			// then it needs to be evened up by appending another null
			$packed_IRB_data .= "\x00";
		}
	}

	// Return the packed data string
	return $packed_IRB_data;
}

/******************************************************************************
* End of Function:     pack_Photoshop_IRB_Data
******************************************************************************/



/******************************************************************************
* Global Variable:      Photoshop_ID_Names
*
* Contents:     The Names of the Photoshop IRB resources, indexed by their
*               resource number
*
******************************************************************************/

$GLOBALS[ "Photoshop_ID_Names" ] = array(
0x03E8 => "Number of channels, rows, columns, depth, and mode. (Obsolete)",
0x03E9 => "Macintosh print manager info ",
0x03EB => "Indexed color table (Obsolete)",
0x03ED => "Resolution Info",
0x03EE => "Alpha Channel Names",
0x03EF => "Display Info",
0x03F0 => "Caption String",
0x03F1 => "Border information",
0x03F2 => "Background color",
0x03F3 => "Print flags",
0x03F4 => "Grayscale and multichannel halftoning information",
0x03F5 => "Color halftoning information",
0x03F6 => "Duotone halftoning information",
0x03F7 => "Grayscale and multichannel transfer function",
0x03F8 => "Color transfer functions",
0x03F9 => "Duotone transfer functions",
0x03FA => "Duotone image information",
0x03FB => "Black and white values",
0x03FC => "Obsolete Resource.",
0x03FD => "EPS options",
0x03FE => "Quick Mask information",
0x03FF => "Obsolete Resource",
0x0400 => "Layer state information",
0x0401 => "Working path (not saved)",
0x0402 => "Layers group information",
0x0403 => "Obsolete Resource",
0x0404 => "IPTC-NAA record",
0x0405 => "Raw Format Image mode",
0x0406 => "JPEG quality",
0x0408 => "Grid and guides information",
0x0409 => "Thumbnail resource",
0x040A => "Copyright flag",
0x040B => "URL",
0x040C => "Thumbnail resource",
0x040D => "Global Angle",
0x040E => "Color samplers resource",
0x040F => "ICC Profile",
0x0410 => "Watermark",
0x0411 => "ICC Untagged",
0x0412 => "Effects visible",
0x0413 => "Spot Halftone",
0x0414 => "Document Specific IDs",
0x0415 => "Unicode Alpha Names",
0x0416 => "Indexed Color Table Count",
0x0417 => "Tansparent Index. Index of transparent color, if any.",
0x0419 => "Global Altitude",
0x041A => "Slices",
0x041B => "Workflow URL",
0x041C => "Jump To XPEP",
0x041D => "Alpha Identifiers",
0x041E => "URL List",
0x0421 => "Version Info",
0x0BB7 => "Name of clipping path.",
0x2710 => "Print flags information"
);

/******************************************************************************
* End of Global Variable:     Photoshop_ID_Names
******************************************************************************/





/******************************************************************************
* Global Variable:      Photoshop_ID_Descriptions
*
* Contents:     The Descriptions of the Photoshop IRB resources, indexed by their
*               resource number
*
******************************************************************************/

$GLOBALS[ "Photoshop_ID_Descriptions" ] = array(
0x03E8 => "ObsoletePhotoshop 2.0 only. number of channels, rows, columns, depth, and mode.",
0x03E9 => "Optional. Macintosh print manager print info record.",
0x03EB => "ObsoletePhotoshop 2.0 only. Contains the indexed color table.",
0x03ED => "ResolutionInfo structure. See Appendix A in Photoshop SDK Guide.pdf",
0x03EE => "Names of the alpha channels as a series of Pascal strings.",
0x03EF => "DisplayInfo structure. See Appendix A in Photoshop SDK Guide.pdf",
0x03F0 => "Optional. The caption as a Pascal string.",
0x03F1 => "Border information. border width, border units",
0x03F2 => "Background color.",
0x03F3 => "Print flags. labels, crop marks, color bars, registration marks, negative, flip, interpolate, caption.",
0x03F4 => "Grayscale and multichannel halftoning information.",
0x03F5 => "Color halftoning information.",
0x03F6 => "Duotone halftoning information.",
0x03F7 => "Grayscale and multichannel transfer function.",
0x03F8 => "Color transfer functions.",
0x03F9 => "Duotone transfer functions.",
0x03FA => "Duotone image information.",
0x03FB => "Effective black and white values for the dot range.",
0x03FC => "Obsolete Resource.",
0x03FD => "EPS options.",
0x03FE => "Quick Mask information. Quick Mask channel ID, Mask initially empty.",
0x03FF => "Obsolete Resource.",
0x0400 => "Layer state information. Index of target layer.",
0x0401 => "Working path (not saved).",
0x0402 => "Layers group information. Group ID for the dragging groups. Layers in a group have the same group ID.",
0x0403 => "Obsolete Resource.",
0x0404 => "IPTC-NAA record. This contains the File Info... information. See the IIMV4.pdf document.",
0x0405 => "Image mode for raw format files.",
0x0406 => "JPEG quality. Private.",
0x0408 => "Grid and guides information.",
0x0409 => "Thumbnail resource.",
0x040A => "Copyright flag. Boolean indicating whether image is copyrighted. Can be set via Property suite or by user in File Info...",
0x040B => "URL. Handle of a text string with uniform resource locator. Can be set via Property suite or by user in File Info...",
0x040C => "Thumbnail resource.",
0x040D => "Global Angle. Global lighting angle for effects layer.",
0x040E => "Color samplers resource.",
0x040F => "ICC Profile. The raw bytes of an ICC format profile, see the ICC34.pdf and ICC34.h files from the Internation Color Consortium.",
0x0410 => "Watermark.",
0x0411 => "ICC Untagged. Disables any assumed profile handling when opening the file. 1 = intentionally untagged.",
0x0412 => "Effects visible. Show/hide all the effects layer.",
0x0413 => "Spot Halftone. Version, length, variable length data.",
0x0414 => "Document specific IDs for layer identification",
0x0415 => "Unicode Alpha Names. Length and the string",
0x0416 => "Indexed Color Table Count. Number of colors in table that are actually defined",
0x0417 => "Transparent Index. Index of transparent color, if any.",
0x0419 => "Global Altitude.",
0x041A => "Slices.",
0x041B => "Workflow URL. Length, string.",
0x041C => "Jump To XPEP. Major version, Minor version, Count. Table which can include: Dirty flag, Mod date.",
0x041D => "Alpha Identifiers.",
0x041E => "URL List. Count of URLs, IDs, and strings",
0x0421 => "Version Info. Version, HasRealMergedData, string of writer name, string of reader name, file version.",
0x0BB7 => "Name of clipping path.",
0x2710 => "Print flags information. Version, Center crop marks, Bleed width value, Bleed width scale."
);

/******************************************************************************
* End of Global Variable:     Photoshop_ID_Descriptions
******************************************************************************/






?>