<?php

/******************************************************************************
*
* Function:     UTF8_fix
*
* Description:  Checks a string for badly formed Unicode UTF-8 coding and
*               returns the same string containing only the parts which
*               were properly formed UTF-8 data.
*
* Parameters:   utf8_text - a string with possibly badly formed UTF-8 data
*
* Returns:      output - the well formed UTF-8 version of the string
*
******************************************************************************/

function UTF8_fix( $utf8_text )
{
	// Initialise the current position in the string
	$pos = 0;

	// Create a string to accept the well formed output
	$output = "" ;

	// Cycle through each group of bytes, ensuring the coding is correct
	while ( $pos < strlen( $utf8_text ) )
	{
		// Retreive the current numerical character value
		$chval = ord($utf8_text{$pos});

		// Check what the first character is - it will tell us how many bytes the
		// Unicode value covers

		if ( ( $chval >= 0x00 ) && ( $chval <= 0x7F ) )
		{
			// 1 Byte UTF-8 Unicode (7-Bit ASCII) Character
			$bytes = 1;
		}
		else if ( ( $chval >= 0xC0 ) && ( $chval <= 0xDF ) )
		{
			// 2 Byte UTF-8 Unicode Character
			$bytes = 2;
		}
		else if ( ( $chval >= 0xE0 ) && ( $chval <= 0xEF ) )
		{
			// 3 Byte UTF-8 Unicode Character
			$bytes = 3;
		}
		else if ( ( $chval >= 0xF0 ) && ( $chval <= 0xF7 ) )
		{
			// 4 Byte UTF-8 Unicode Character
			$bytes = 4;
		}
		else if ( ( $chval >= 0xF8 ) && ( $chval <= 0xFB ) )
		{
			// 5 Byte UTF-8 Unicode Character
			$bytes = 5;
		}
		else if ( ( $chval >= 0xFC ) && ( $chval <= 0xFD ) )
		{
			// 6 Byte UTF-8 Unicode Character
			$bytes = 6;
		}
		else
		{
			// Invalid Code - skip character and do nothing
			$bytes = 0;
			$pos++;
		}


		// check that there is enough data remaining to read
		if (($pos + $bytes - 1) < strlen( $utf8_text ) )
		{
			// Cycle through the number of bytes specified,
			// copying them to the output string
			while ( $bytes > 0 )
			{
				$output .= $utf8_text{$pos};
				$pos++;
				$bytes--;
			}
		}
		else
		{
			break;
		}
	}

	// Return the result
	return $output;
}

/******************************************************************************
* End of Function:     UTF8_fix
******************************************************************************/

/******************************************************************************
*
* Function:     UTF8_to_unicode_array
*
* Description:  Converts a string encoded with Unicode UTF-8, to an array of
*               numbers which represent unicode character numbers
*
* Parameters:   utf8_text - a string containing the UTF-8 data
*
* Returns:      output - the array containing the unicode character numbers
*
******************************************************************************/

function UTF8_to_unicode_array( $utf8_text )
{
	// Create an array to receive the unicode character numbers output
	$output = array( );

	// Cycle through the characters in the UTF-8 string
	for ( $pos = 0; $pos < strlen( $utf8_text ); $pos++ )
	{
		// Retreive the current numerical character value
		$chval = ord($utf8_text{$pos});

		// Check what the first character is - it will tell us how many bytes the
		// Unicode value covers

		if ( ( $chval >= 0x00 ) && ( $chval <= 0x7F ) )
		{
			// 1 Byte UTF-8 Unicode (7-Bit ASCII) Character
			$bytes = 1;
			$outputval = $chval;    // Since 7-bit ASCII is unaffected, the output equals the input
		}
		else if ( ( $chval >= 0xC0 ) && ( $chval <= 0xDF ) )
		{
			// 2 Byte UTF-8 Unicode
			$bytes = 2;
			$outputval = $chval & 0x1F;     // The first byte is bitwise ANDed with 0x1F to remove the leading 110b
		}
		else if ( ( $chval >= 0xE0 ) && ( $chval <= 0xEF ) )
		{
			// 3 Byte UTF-8 Unicode
			$bytes = 3;
			$outputval = $chval & 0x0F;     // The first byte is bitwise ANDed with 0x0F to remove the leading 1110b
		}
		else if ( ( $chval >= 0xF0 ) && ( $chval <= 0xF7 ) )
		{
			// 4 Byte UTF-8 Unicode
			$bytes = 4;
			$outputval = $chval & 0x07;     // The first byte is bitwise ANDed with 0x07 to remove the leading 11110b
		}
		else if ( ( $chval >= 0xF8 ) && ( $chval <= 0xFB ) )
		{
			// 5 Byte UTF-8 Unicode
			$bytes = 5;
			$outputval = $chval & 0x03;     // The first byte is bitwise ANDed with 0x03 to remove the leading 111110b
		}
		else if ( ( $chval >= 0xFC ) && ( $chval <= 0xFD ) )
		{
			// 6 Byte UTF-8 Unicode
			$bytes = 6;
			$outputval = $chval & 0x01;     // The first byte is bitwise ANDed with 0x01 to remove the leading 1111110b
		}
		else
		{
			// Invalid Code - do nothing
			$bytes = 0;
		}

		// Check if the byte was valid
		if ( $bytes !== 0 )
		{
			// The byte was valid

			// Check if there is enough data left in the UTF-8 string to allow the
			// retrieval of the remainder of this unicode character
			if ( $pos + $bytes - 1 < strlen( $utf8_text ) )
			{
				// The UTF-8 string is long enough

				// Cycle through the number of bytes required,
				// minus the first one which has already been done
				while ( $bytes > 1 )
				{
					$pos++;
					$bytes--;

					// Each remaining byte is coded with 6 bits of data and 10b on the high
					// order bits. Hence we need to shift left by 6 bits (0x40) then add the
					// current characer after it has been bitwise ANDed with 0x3F to remove the
					// highest two bits.
					$outputval = $outputval*0x40 + ( (ord($utf8_text{$pos})) & 0x3F );
				}

				// Add the calculated Unicode number to the output array
				$output[] = $outputval;
			}
		}

	}

	// Return the resulting array
	return $output;
}

/******************************************************************************
* End of Function:     UTF8_to_unicode_array
******************************************************************************/


/******************************************************************************
*
* Function:     unicode_array_to_UTF8
*
* Description:  Converts an array of unicode character numbers to a string
*               encoded by UTF-8
*
* Parameters:   unicode_array - the array containing unicode character numbers
*
* Returns:      output - the UTF-8 encoded string representing the data
*
******************************************************************************/

function unicode_array_to_UTF8( $unicode_array )
{

	// Create a string to receive the UTF-8 output
	$output = "";

	// Cycle through each Unicode character number
	foreach( $unicode_array as $unicode_char )
	{
		// Check which range the current unicode character lies in
		if ( ( $unicode_char >= 0x00 ) && ( $unicode_char <= 0x7F ) )
		{
			// 1 Byte UTF-8 Unicode (7-Bit ASCII) Character

			$output .= chr($unicode_char);          // Output is equal to input for 7-bit ASCII
		}
		else if ( ( $unicode_char >= 0x80 ) && ( $unicode_char <= 0x7FF ) )
		{
			// 2 Byte UTF-8 Unicode - binary encode data as : 110xxxxx 10xxxxxx

			$output .= chr(0xC0 + ($unicode_char/0x40));
			$output .= chr(0x80 + ($unicode_char & 0x3F));
		}
		else if ( ( $unicode_char >= 0x800 ) && ( $unicode_char <= 0xFFFF ) )
		{
			// 3 Byte UTF-8 Unicode - binary encode data as : 1110xxxx 10xxxxxx 10xxxxxx

			$output .= chr(0xE0 + ($unicode_char/0x1000));
			$output .= chr(0x80 + (($unicode_char/0x40) & 0x3F));
			$output .= chr(0x80 + ($unicode_char & 0x3F));
		}
		else if ( ( $unicode_char >= 0x10000 ) && ( $unicode_char <= 0x1FFFFF ) )
		{
			// 4 Byte UTF-8 Unicode - binary encode data as : 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx

			$output .= chr(0xF0 + ($unicode_char/0x40000));
			$output .= chr(0x80 + (($unicode_char/0x1000) & 0x3F));
			$output .= chr(0x80 + (($unicode_char/0x40) & 0x3F));
			$output .= chr(0x80 + ($unicode_char & 0x3F));
		}
		else if ( ( $unicode_char >= 0x200000 ) && ( $unicode_char <= 0x3FFFFFF ) )
		{
			// 5 Byte UTF-8 Unicode - binary encode data as : 111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx

			$output .= chr(0xF8 + ($unicode_char/0x1000000));
			$output .= chr(0x80 + (($unicode_char/0x40000) & 0x3F));
			$output .= chr(0x80 + (($unicode_char/0x1000) & 0x3F));
			$output .= chr(0x80 + (($unicode_char/0x40) & 0x3F));
			$output .= chr(0x80 + ($unicode_char & 0x3F));
		}
		else if ( ( $unicode_char >= 0x4000000 ) && ( $unicode_char <= 0x7FFFFFFF ) )
		{
			// 6 Byte UTF-8 Unicode - binary encode data as : 1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx

			$output .= chr(0xFC + ($unicode_char/0x40000000));
			$output .= chr(0x80 + (($unicode_char/0x1000000) & 0x3F));
			$output .= chr(0x80 + (($unicode_char/0x40000) & 0x3F));
			$output .= chr(0x80 + (($unicode_char/0x1000) & 0x3F));
			$output .= chr(0x80 + (($unicode_char/0x40) & 0x3F));
			$output .= chr(0x80 + ($unicode_char & 0x3F));
		}
		else
		{
			// Invalid Code - do nothing
		}

	}

	// Return resulting UTF-8 String
	return $output;
}

/******************************************************************************
* End of Function:     unicode_array_to_UTF8
******************************************************************************/


/******************************************************************************
*
* Function:     HTML_UTF8_Escape
*
* Description:  A HTML page can display UTF-8 data properly if it has a
*               META http-equiv="Content-Type" tag with the content attribute
*               including the value: "charset=utf-8".
*               Otherwise the ISO-8859-1 character set is usually assumed, and
*               Unicode values above 0x7F must be escaped.
*               This function takes a UTF-8 encoded string and escapes the
*               characters above 0x7F as well as reserved HTML characters such
*               as Quotes, Greater than, Less than and Ampersand.
*
* Parameters:   utf8_text - a string containing the UTF-8 data
*
* Returns:      htmloutput - a string containing the HTML equivalent
*
******************************************************************************/

function HTML_UTF8_Escape( $UTF8_text )
{

	// Ensure that the Unicode UTF8 encoding is valid.
	$UTF8_text = UTF8_fix( $UTF8_text );

	// Change: changed to use smart_htmlspecialchars, so that characters which were already escaped would remain intact, as of revision 1.10
	// Escape any special HTML characters present
	$UTF8_text =  smart_htmlspecialchars( $UTF8_text, ENT_QUOTES );

	// Convert the UTF-8 string to an array of unicode character numbers
	$unicode_array = UTF8_to_unicode_array( $UTF8_text );

	// Create a string to receive the escaped HTML
	$htmloutput = "";

	// Cycle through the unicode character numbers
	foreach( $unicode_array as  $unichar )
	{
		// Check if the character needs to be escaped
		if ( ( $unichar >= 0x00 ) && ( $unichar <= 0x7F ) )
		{
			// Character is less than 0x7F - add it to the html as is
			$htmloutput .= chr( $unichar );
		}
		else
		{
			// Character is greater than 0x7F - escape it and add it to the html
			$htmloutput .= "&#x" . dechex($unichar) . ";";
		}
	}

	// Return the resulting escaped HTML
	return $htmloutput;
}

/******************************************************************************
* End of Function:     HTML_UTF8_Escape
******************************************************************************/



/******************************************************************************
*
* Function:     HTML_UTF8_UnEscape
*
* Description:  Converts HTML which contains escaped decimal or hex characters
*               into UTF-8 text
*
* Parameters:   HTML_text - a string containing the HTML text to convert
*
* Returns:      utfoutput - a string containing the UTF-8 equivalent
*
******************************************************************************/

function HTML_UTF8_UnEscape( $HTML_text )
{
	preg_match_all( "/\&\#(\d+);/", $HTML_text, $matches);
	preg_match_all( "/\&\#[x|X]([A|B|C|D|E|F|a|b|c|d|e|f|0-9]+);/", $HTML_text, $hexmatches);
	foreach( $hexmatches[1] as $index => $match )
	{
		$matches[0][] = $hexmatches[0][$index];
		$matches[1][] = hexdec( $match );
	}

	for ( $i = 0; $i < count( $matches[ 0 ] ); $i++ )
	{
		$trans = array( $matches[0][$i] => unicode_array_to_UTF8( array( $matches[1][$i] ) ) );

		$HTML_text = strtr( $HTML_text , $trans );
	}
	return $HTML_text;
}

/******************************************************************************
* End of Function:     HTML_UTF8_UnEscape
******************************************************************************/


/******************************************************************************
*
* Function:     smart_HTML_Entities
*
* Description:  Performs the same function as HTML_Entities, but leaves entities
*               that are already escaped intact.
*
* Parameters:   HTML_text - a string containing the HTML text to be escaped
*
* Returns:      HTML_text_out - a string containing the escaped HTML text
*
******************************************************************************/

function smart_HTML_Entities( $HTML_text )
{
	// Get a table containing the HTML entities translations
	$translation_table = get_html_translation_table( HTML_ENTITIES );

	// Change the ampersand to translate to itself, to avoid getting &amp;
	$translation_table[ chr(38) ] = '&';

	// Perform replacements
	// Regular expression says: find an ampersand, check the text after it,
	// if the text after it is not one of the following, then replace the ampersand
	// with &amp;
	// a) any combination of up to 4 letters (upper or lower case) with at least 2 or 3 non whitespace characters, then a semicolon
	// b) a hash symbol, then between 2 and 7 digits
	// c) a hash symbol, an 'x' character, then between 2 and 7 digits
	// d) a hash symbol, an 'X' character, then between 2 and 7 digits
	return preg_replace( "/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,7}|#x[0-9]{2,7}|#X[0-9]{2,7};)/","&amp;" , strtr( $HTML_text, $translation_table ) );
}

/******************************************************************************
* End of Function:     smart_HTML_Entities
******************************************************************************/



/******************************************************************************
*
* Function:     smart_htmlspecialchars
*
* Description:  Performs the same function as htmlspecialchars, but leaves characters
*               that are already escaped intact.
*
* Parameters:   HTML_text - a string containing the HTML text to be escaped
*
* Returns:      HTML_text_out - a string containing the escaped HTML text
*
******************************************************************************/

function smart_htmlspecialchars( $HTML_text )
{
	// Get a table containing the HTML special characters translations
	$translation_table=get_html_translation_table (HTML_SPECIALCHARS);

	// Change the ampersand to translate to itself, to avoid getting &amp;
	$translation_table[ chr(38) ] = '&';

	// Perform replacements
	// Regular expression says: find an ampersand, check the text after it,
	// if the text after it is not one of the following, then replace the ampersand
	// with &amp;
	// a) any combination of up to 4 letters (upper or lower case) with at least 2 or 3 non whitespace characters, then a semicolon
	// b) a hash symbol, then between 2 and 7 digits
	// c) a hash symbol, an 'x' character, then between 2 and 7 digits
	// d) a hash symbol, an 'X' character, then between 2 and 7 digits
	return preg_replace( "/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,7}|#x[0-9]{2,7}|#X[0-9]{2,7};)/","&amp;" , strtr( $HTML_text, $translation_table ) );
}

/******************************************************************************
* End of Function:     smart_htmlspecialchars
******************************************************************************/


?>