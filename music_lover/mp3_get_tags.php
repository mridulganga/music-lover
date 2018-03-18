<?PHP
/* 	Read MP3 ID3 Tag Script for PHP
	Created By Jeff Baker on December 12, 2014
	Copyright (C) 2014 Jeff Baker
	www.seabreezecomputers.com/tips/mp3_id3_tag.htm
	Version 2.0b - April 18, 2016	
*/

function mp3_get_tags($file)
{
	// http://www.seabreezecomputers.com/tips/mp3_id3_tag.htm
	$id3_tags = array(); // Function returns an array of id3 mp3 tags for $file
	// See: http://mpgedit.org/mpgedit/mpeg_format/mpeghdr.htm
	$versions = array("00" => "2.5", "01" => "x", "10" => "2", "11" => "1"); // MPEG Audio version ID
	$layers = array("00" => "x", "01" => "3", "10" => "2", "11" => "1"); // MPEG Audio layer description
	$bitrates = array(
		'V1L1'=>array(0,32,64,96,128,160,192,224,256,288,320,352,384,416,448),
        'V1L2'=>array(0,32,48,56, 64, 80, 96,112,128,160,192,224,256,320,384),
        'V1L3'=>array(0,32,40,48, 56, 64, 80, 96,112,128,160,192,224,256,320),
        'V2L1'=>array(0,32,48,56, 64, 80, 96,112,128,144,160,176,192,224,256),
        'V2L2'=>array(0, 8,16,24, 32, 40, 48, 56, 64, 80, 96,112,128,144,160),
        'V2L3'=>array(0, 8,16,24, 32, 40, 48, 56, 64, 80, 96,112,128,144,160),
        );  
    $sample_rates = array(
			'1'   => array(44100,48000,32000),
            '2'   => array(22050,24000,16000),
            '2.5' => array(11025,12000, 8000),
        );
	
	$handle = fopen($file, "r");
	 
	if (!$handle) return; // Version 2.0
	{
		// Look for ID3v2 - http://id3.org/id3v2.3.0 or http://id3.org/id3v2.4.0-frames
		$tags_array = array( // first ID3v2.3 and ID3v2.4 tags
							'TIT2' => 'title', 'TALB' => 'album', 'TPE1' => 'artist',
							'TYER' => 'year', 'COMM' => 'comment', 'TCON' => 'genre', 'TLEN' => 'length',
							// second ID3v2.2 tags - http://id3.org/id3v2-00
							'TT2' => 'title', 'TAL' => 'album', 'TP1' => 'artist',
							'TYE' => 'year', 'COM' => 'comment', 'TCO' => 'genre', 'TLE' => 'length'
							);
		$null = chr(0); // the stop bit or null in ID3 tags is the first ASCII character or chr(0)
		//fseek($handle, 0, SEEK_SET); // Read file from beginning // Version 2.0 - Removed all fseek for remote file support
		$data = fread($handle, 10); // 10 = Size of header - http://id3.org/id3v2.4.0-structure
		if (substr($data,0,3) == 'ID3') // If first 3 bytes == "ID3"
		{
			$id3_major_version = hexdec(bin2hex(substr($data,3,1)));
			$id3_tags["id3_tag_version"] = "2.".$id3_major_version;
			$id3_revision = hexdec(bin2hex(substr($data,4,1)));
			$id3_flags = decbin(ord(substr($data,5,1))); // 8 flag bits (first 4 may be set)
			$id3_flags = str_pad($id3_flags, 8, 0, STR_PAD_LEFT);
			$footer_flag = $id3_flags[3]; // footer flag is 4th flag bit
			// Calculate size of header including all tags and extended header and footer
			$mb_size = ord(substr($data,6,1)); // each number here is equal to 2 Megabytes
			$kb_size = ord(substr($data,7,1)); // each number here is equal to 16 Kilobytes
			$byte128_size = ord(substr($data,8,1)); // each number here is equal to 128 Bytes
			$byte_size = ord(substr($data,9,1)); // each number here is equal to 1 Byte
			$total_size = ($mb_size * 2097152) + ($kb_size * 16384) + ($byte128_size * 128) + $byte_size;
			//fseek($handle, 0, SEEK_SET); // Read file from beginning // Version 2.0 - Removed all fseek for remote file support
			//$data = fread($handle, 10 + $total_size + ($footer_flag * 10));
			$data .= stream_get_contents($handle, $total_size + ($footer_flag * 10)); // Version 2.0 - Using stream_get_contents instead of fread // Version 2.0a - Removed extra 10 + before $total_size
			foreach ($tags_array as $key => $value)
			{
				if ($id3_major_version == 3 || $id3_major_version == 4)
					$tag_header_length = 10; 
				else // if ($id3_major_version == 2)
					$tag_header_length = 6; 
				if ($tag_pos = strpos($data, $key.$null))
				{
					$tag_abbr = trim(substr($data, $tag_pos, 4)); // tag abbreviation
					$content_length = hexdec(bin2hex(substr($data, $tag_pos + ($tag_header_length/2),3)));
					$content = trim(substr($data, $tag_pos + $tag_header_length, $content_length));
					$tag_content = "";
					for ($i = 0; $i < strlen($content); $i++)
						if($content[$i] >= " " && $content[$i] <= "~") $tag_content .= $content[$i];
					$id3_tags[$value] = $tag_content; // Ex: $id3_tags['title'] = "Song Title";
				}
			}
			if ($id3_major_version != 2) // Version 2.0
				$data = ""; // wipe out data so we only add to it if it is ID3v1 below with: $data .= fread($handle, 10); 
		}
		//else // ID3v1
		//	fseek($handle, 0, SEEK_SET); // Read file from beginning // Version 2.0 - Removed all fseek for remote file support	
		$bits = null; // Version 2.0b - Declare $bits variable to avoid warning in PHP
		// Look for first mp3 frame.  Every frame begins with eleven 1s (bits)	
		while (!feof($handle))
		{
			//$data .= fread($handle, 10); // read 10 bytes of mp3 after header
			$data .= stream_get_contents($handle, 10); // Version 2.0 - Using stream_get_contents instead of fread
			for ($i = 0; $i < strlen($data); $i++)
				$bits .= str_pad(decbin(ord($data[$i])), 8, 0, STR_PAD_LEFT);
			$frame_pos = strpos($bits, "11111111111"); // Version 2.0 - Bug fix: Removed from if statement
			// http://mpgedit.org/mpgedit/mpeg_format/MP3Format.html
			if ($frame_pos !== false) // Version 2.0 - Bug fix: Having strpos in if statement always returned 1 or 0 instead of pos
			{
				//echo "<p>".substr($bits, $frame_pos);
				$id3_tags["version"] = $versions[substr($bits, $frame_pos + 11, 2)];
				$id3_tags["layer"] = $layers[substr($bits, $frame_pos + 13, 2)];
				$id3_tags["crc"] = substr($bits, $frame_pos + 15, 1); // 0 = Yes using CRC. 1 = No CRC
				$bitrate_index = bindec(substr($bits, $frame_pos + 16, 4));
				$id3_tags["bitrate"] = $bitrates["V".$id3_tags["version"][0]."L".$id3_tags["layer"]][$bitrate_index];
				$id3_tags["frequency"] = $sample_rates["1"][bindec(substr($bits, $frame_pos + 19, 2))]; // Sampling Rate Frequency
				if (preg_match("/^(https?|ftp):\/\//", $file))
					$id3_tags["filesize"] = get_headers($file,1)['Content-Length']; // Version 2.0 - Remote file
				else
					$id3_tags["filesize"] = filesize($file);
				//print_r(get_headers($file));
				$bps = ($id3_tags["bitrate"]*1000)/8;
        		// duration in seconds = filesize - total size of headers / bps
        		$id3_tags["duration"] = round(($id3_tags["filesize"] - $total_size) / $bps);
        		// Formatted time (i:s)
				//$mins = floor($id3_tags["duration"] / 60); // Version 2.0b - No longer used for formatted_time
				//$secs = time - ($mins * 60);	// Version 2.0b - No longer used for formatted_time
				//if ($secs < 10) $secs = "0".secs; // Version 2.0b - No longer used for formatted_time
				$id3_tags["formatted_time"] = gmdate("i:s", $id3_tags["duration"]);
				break;	// break while loop
			}
		}	
	}
	
	// Look for ID3v1 - http://id3.org/ID3v1
	if (!isset($id3_major_version)) // if we didn't alreay find ID3v2 v3 or v4 tags
	{
		$id3_tags["id3_tag_version"] = 1; // Version 2.0 
		while (!feof($handle))
		{
			//$data .= fread($handle, 128);
			$data .= stream_get_contents($handle, 128); // Version 2.0 - Using stream_get_contents instead of fread
			
		}
		$data = substr($data, -128);  // Get just last 128 bytes of file
		if(substr($data, 0, 3) == "TAG") // If first 3 bytes == "TAG"
		{
			$id3_tags["title"] = trim(substr($data, 3, 30));
			$id3_tags["artist"] = trim(substr($data, 33, 30));
			$id3_tags["album"] = trim(substr($data, 63, 30));
			$id3_tags["year"] = trim(substr($data, 93, 4));
			$id3_tags["comment"] = trim(substr($data, 97, 30));
			$id3_tags["genre"] = ord(trim(substr($data, 127, 1))); // http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/ID3.html
		}
	}
	
	fclose($handle);
		
	return($id3_tags);
	
} // end function mp3_get_tags($file)

function mp3_get_genre_name($genre_id)
{
	// See: http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/ID3.html
	$genre_names = array("Blues", "Classic Rock", "Country", "Dance", "Disco", "Funk", "Grunge", "Hip-Hop", "Jazz", "Metal", "New Age", "Oldies", "Other", "Pop", "R&B", "Rap", "Reggae", "Rock", "Techno", "Industrial", "Alternative", "Ska", "Death Metal", "Pranks", "Soundtrack", "Euro-Techno", "Ambient", "Trip-Hop", "Vocal", "Jazz+Funk", "Fusion", "Trance", "Classical", "Instrumental", "Acid", "House", "Game", "Sound Clip", "Gospel", "Noise", "Alt. Rock", "Bass", "Soul", "Punk", "Space", "Meditative", "Instrumental Pop", "Instrumental Rock", "Ethnic", "Gothic", "Darkwave", "Techno-Industrial", "Electronic", "Pop-Folk", "Eurodance", "Dream", "Southern Rock", "Comedy", "Cult", "Gangsta Rap", "Top 40", "Christian Rap", "Pop/Funk", "Jungle", "Native American", "Cabaret", "New Wave", "Psychedelic", "Rave", "Showtunes", "Trailer", "Lo-Fi", "Tribal", "Acid Punk", "Acid Jazz", "Polka", "Retro", "Musical", "Rock & Roll", "Hard Rock", "Folk", "Folk-Rock", "National Folk", "Swing", "Fast-Fusion", "Bebop", "Latin", "Revival", "Celtic", "Bluegrass", "Avantgarde", "Gothic Rock", "Progressive Rock", "Psychedelic Rock", "Symphonic Rock", "Slow Rock", "Big Band", "Chorus", "Easy Listening", "Acoustic", "Humour", "Speech", "Chanson", "Opera", "Chamber Music", "Sonata", "Symphony", "Booty Bass", "Primus", "Porn Groove", "Satire", "Slow Jam", "Club", "Tango", "Samba", "Folklore", "Ballad", "Power Ballad", "Rhythmic Soul", "Freestyle", "Duet", "Punk Rock", "Drum Solo", "A Cappella", "Euro-House", "Dance Hall", "Goa", "Drum & Bass", "Club-House", "Hardcore", "Terror", "Indie", "BritPop", "Afro-Punk", "Polsk Punk", "Beat", "Christian Gangsta Rap", "Heavy Metal", "Black Metal", "Crossover", "Contemporary Christian", "Christian Rock", "Merengue", "Salsa", "Thrash Metal", "Anime", "JPop", "Synthpop", "Abstract", "Art Rock", "Baroque", "Bhangra", "Big Beat", "Breakbeat", "Chillout", "Downtempo", "Dub", "EBM", "Eclectic", "Electro", "Electroclash", "Emo", "Experimental", "Garage", "Global", "IDM", "Illbient", "Industro-Goth", "Jam Band", "Krautrock", "Leftfield", "Lounge", "Math Rock", "New Romantic", "Nu-Breakz", "Post-Punk", "Post-Rock", "Psytrance", "Shoegaze", "Space Rock", "Trop Rock", "World Music", "Neoclassical", "Audiobook", "Audio Theatre", "Neue Deutsche Welle", "Podcast", "Indie Rock", "G-Funk", "Dubstep", "Garage Rock", "Psybient");
	/* According to http://id3.org/id3v2.3.0#Text_information_frames:
		"Several references can be made in the same frame, e.g. "(51)(39)" */
	$genres = explode(")", $genre_id);
	$n = 1;
	foreach($genres as $genre_num)
	{ 
			$genre_num = str_replace("(", "", str_replace(")", "", $genre_num)); // remove ( and )
			if ($n > 1 && !empty($genre_num))
				$genre_string .= ","; // Separate multiple genres by ,
			if (is_numeric($genre_num))
			{
				if ($genre_num >= 0 && $genre_num <= 191)
					$genre_string .= $genre_names[$genre_num];
				else
					$genre_string .= "None"; // 255 = None 
			}
			else
				$genre_string .= $genre_num;	
		$n++;
	}
	return($genre_string);
} // end function mp3_get_genre_name()



?>
