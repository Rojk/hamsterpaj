<?php
session_start();
include 'exif_files/JPEG.php';
include 'exif_files/Photoshop_IRB.php';
include 'exif_files/Photoshop_File_Info.php';

function read_copy_protection($filename)
{
	$jpeg_header_data = get_jpeg_header_data( $filename );
	return Interpret_IRB_to_HTML2( get_Photoshop_IRB( $jpeg_header_data ), $filename );
}

function write_copy_protection($outputfilename, $copystatus)
{
	$new_ps_file_info_array = array (
	'title'                 => "",
	'author'                => "Hamsterpaj.net",
	'authorsposition'       => "",
	'caption'               => "",
	'captionwriter'         => $_SESSION['login']['id'],
	'jobname'               => "",
	'copyrightstatus'       => $copystatus,
	'copyrightnotice'       => "",
	'ownerurl'              => "",
	'keywords'              => array(),
	'category'              => "",
	'supplementalcategories'=> array(),
	'date'                  => date("Y-m-d"),
	'city'                  => "",
	'state'                 => "",
	'country'               => "",
	'credit'                => "",
	'source'                => "",
	'headline'              => "",
	'instructions'          => "",
	'transmissionreference' => "",
	'urgency'               => ""
	);


	foreach( $new_ps_file_info_array as $var_key => $var_val )
	{
		$new_ps_file_info_array[ $var_key ] = stripslashes( $var_val );
	}

	// Keywords should be an array - explode it on newline boundarys
	$new_ps_file_info_array[ 'keywords' ] = explode( "\n", trim( $new_ps_file_info_array[ 'keywords' ] ) );

	// Supplemental Categories should be an array - explode it on newline boundarys
	$new_ps_file_info_array[ 'supplementalcategories' ] = explode( "\n", trim( $new_ps_file_info_array[ 'supplementalcategories' ] ) );

	// Protect against hackers editing other files
	$path_parts = pathinfo( $outputfilename );
	if ( strcasecmp( $path_parts["extension"], "jpg" ) != 0 )
	{
		return "Incorrect File Type - JPEG Only\n";
		exit( );
	}
	// Change: removed limitation on file being in current directory - as of version 1.11

	// Retrieve the header information
	$jpeg_header_data = get_jpeg_header_data( $outputfilename );

	// Update the JPEG header information with the new Photoshop File Info
	$jpeg_header_data = put_photoshop_file_info( $jpeg_header_data, $new_ps_file_info_array);

	// Check if the Update worked
	if ( $jpeg_header_data == FALSE )
	{
		// Update of file info didn't work - output error message
		return "Error - Failure update File Info : $outputfilenam <br>\n";
		exit( );
	}

	// Attempt to write the new JPEG file
	if ( FALSE == put_jpeg_header_data( $outputfilename, $outputfilename, $jpeg_header_data ) )
	{
		// Writing of the new file didn't work - output error message
		return "Error - Failure to write new JPEG : $filename <br>\n";
		// Abort processing
		exit( );
	}

	//return "<p><a href=\"Edit_File_Info_Example.php?jpeg_fname=$outputfilename\" >View Full Metatdata Information</a></p>\n";

}
?>