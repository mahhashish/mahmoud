<?

function mp3info($filename) { 

    // MH: MPEG Audio Tag ID3v1 stuff 
    $genre[0]="Blues"; 
    $genre[1]="Classic Rock"; 
    $genre[2]="Country"; 
    $genre[3]="Dance"; 
    $genre[4]="Disco"; 
    $genre[5]="Funk"; 
    $genre[6]="Grunge"; 
    $genre[7]="Hip-Hop"; 
    $genre[8]="Jazz"; 
    $genre[9]="Metal"; 
    $genre[10]="New Age"; 
    $genre[11]="Oldies"; 
    $genre[12]="Other"; 
    $genre[13]="Pop"; 
    $genre[14]="R&amp;B"; 
    $genre[15]="Rap"; 
    $genre[16]="Reggae"; 
    $genre[17]="Rock"; 
    $genre[18]="Techno"; 
    $genre[19]="Industrial"; 
    $genre[20]="Alternative"; 
    $genre[21]="Ska"; 
    $genre[22]="Death Metal"; 
    $genre[23]="Pranks"; 
    $genre[24]="Soundtrack"; 
    $genre[25]="Euro-Techno"; 
    $genre[26]="Ambient"; 
    $genre[27]="Trip-Hop"; 
    $genre[28]="Vocal"; 
    $genre[29]="Jazz+Funk"; 
    $genre[30]="Fusion"; 
    $genre[31]="Trance"; 
    $genre[32]="Classical"; 
    $genre[33]="Instrumental"; 
    $genre[34]="Acid"; 
    $genre[35]="House"; 
    $genre[36]="Game"; 
    $genre[37]="Sound Clip"; 
    $genre[38]="Gospel"; 
    $genre[39]="Noise"; 
    $genre[40]="AlternRock"; 
    $genre[41]="Bass"; 
    $genre[42]="Soul"; 
    $genre[43]="Punk"; 
    $genre[44]="Space"; 
    $genre[45]="Meditative"; 
    $genre[46]="Instrumental Pop"; 
    $genre[47]="Instrumental Rock"; 
    $genre[48]="Ethnic"; 
    $genre[49]="Gothic"; 
    $genre[50]="Darkwave"; 
    $genre[51]="Techno-Industrial"; 
    $genre[52]="Electronic"; 
    $genre[53]="Pop-Folk"; 
    $genre[54]="Eurodance"; 
    $genre[55]="Dream"; 
    $genre[56]="Southern Rock"; 
    $genre[57]="Comedy"; 
    $genre[58]="Cult"; 
    $genre[59]="Gangsta"; 
    $genre[60]="Top 40"; 
    $genre[61]="Christian Rap"; 
    $genre[62]="Pop/Funk"; 
    $genre[63]="Jungle"; 
    $genre[64]="Native American"; 
    $genre[65]="Cabaret"; 
    $genre[66]="New Wave"; 
    $genre[67]="Psychadelic"; 
    $genre[68]="Rave"; 
    $genre[69]="Showtunes"; 
    $genre[70]="Trailer"; 
    $genre[71]="Lo-Fi"; 
    $genre[72]="Tribal"; 
    $genre[73]="Acid Punk"; 
    $genre[74]="Acid Jazz"; 
    $genre[75]="Polka"; 
    $genre[76]="Retro"; 
    $genre[77]="Musical"; 
    $genre[78]="Rock &amp; Roll"; 
    $genre[79]="Hard Rock"; 
    # WinAmp expanded the above with the following: 
    $genre[80]="Folk"; 
    $genre[81]="Folk-Rock"; 
    $genre[82]="National Folk"; 
    $genre[83]="Swing"; 
    $genre[84]="Fast Fusion"; 
    $genre[85]="Bebob"; 
    $genre[86]="Latin"; 
    $genre[87]="Revival"; 
    $genre[88]="Celtic"; 
    $genre[89]="Bluegrass"; 
    $genre[90]="Avantgarde"; 
    $genre[91]="Gothic Rock"; 
    $genre[92]="Progressive Rock"; 
    $genre[93]="Psychedelic Rock"; 
    $genre[94]="Symphonic Rock"; 
    $genre[95]="Slow Rock"; 
    $genre[96]="Big Band"; 
    $genre[97]="Chorus"; 
    $genre[98]="Easy Listening"; 
    $genre[99]="Acoustic"; 
    $genre[100]="Humour"; 
    $genre[101]="Speech"; 
    $genre[102]="Chanson"; 
    $genre[103]="Opera"; 
    $genre[104]="Chamber Music"; 
    $genre[105]="Sonata"; 
    $genre[106]="Symphony"; 
    $genre[107]="Booty Brass"; 
    $genre[108]="Primus"; 
    $genre[109]="Porn Groove"; 
    $genre[110]="Satire"; 
    $genre[111]="Slow Jam"; 
    $genre[112]="Club"; 
    $genre[113]="Tango"; 
    $genre[114]="Samba"; 
    $genre[115]="Folklore"; 
    $genre[116]="Ballad"; 
    $genre[117]="Poweer Ballad"; 
    $genre[118]="Rhytmic Soul"; 
    $genre[119]="Freestyle"; 
    $genre[120]="Duet"; 
    $genre[121]="Punk Rock"; 
    $genre[122]="Drum Solo"; 
    $genre[123]="A Capela"; 
    $genre[124]="Euro-House"; 
    $genre[125]="Dance Hall"; 


    // Ensure file exists! 
    if (!$fp = @fopen($filename,"rb")) { 
        return (1); 
    } 

    // Checking to make sure I can find Frame Sync 
    while (!feof($fp)) { 
            $tmp=fgetc($fp); 
            if (ord($tmp)==255) { 
                $tmp=fgetc($fp); 
                if (substr((decbin(ord($tmp))),0,3)=="111") { 
                    break; 
                } 
            } 
    } // eo while 

    // If end of file is reached before Frame Sync is found then bail... 
    if (feof($fp)) { 
        fclose($fp); 
        return (2); 
    } 

    // We have declared all engines go. 

    // Assign filesize 
    $fred['filesize']=filesize($filename); 

    // Assign all important information to $bitstream variable. 
    $inf=decbin(ord($tmp)); 
    $inf=sprintf("%08d",$inf); 
    $bitstream = $inf; 
    $tmp=fgetc($fp); 
    $inf=decbin(ord($tmp)); 
    $inf=sprintf("%08d",$inf); 
    $bitstream = $bitstream.$inf; 
    $tmp=fgetc($fp); 
    $inf=decbin(ord($tmp)); 
    $inf=sprintf("%08d",$inf); 
    $bitstream = $bitstream.$inf; 

    // $bitstream now totals the 3 important bytes of the header of this frame. 

    // Determine Version of Mpeg. 
    switch (substr($bitstream,3,2)) { 
            case "00": 
                $fred['version']="2.5"; 
                break; 
            case "01": 
                $fred['version']="0"; 
                break; 
            case "10": 
                $fred['version']="2"; 
                break; 
            case "11": 
                $fred['version']="1"; 
                break; 
    } // eo switch 

    // Determine Layer. 
    switch (substr($bitstream,5,2)) { 
            case "00": 
                $fred['layer']="0"; 
                break; 
            case "01": 
                $fred['layer']="3"; 
                break; 
            case "10": 
                $fred['layer']="2"; 
                break; 
            case "11": 
                $fred['layer']="1"; 
                break; 
    } // eo switch 

    // Determine CRC checking enabled / disabled 1==disabled 
    $fred['crc'] = substr($bitstream,7,1); 

    // Determine Bitrate 
    // Setting an index variable ... trust me in this 
    // state tis the only way I can think of doing it... 
    if (($fred['version']=="1")&($fred['layer']=="1")) { 
            $index="1"; 
    } elseif (($fred['version']=="1")&($fred['layer']=="2")) { 
            $index="2"; 
    } 
    elseif ($fred['version']=="1") { 
            $index="3"; 
    } 
    elseif ($fred['layer']=="1") { 
            $index="4"; 
    } 
    else    { 
            $index="5"; 
    } 

    switch (substr($bitstream,8,4)) { 
            case "0000": 
                $fred['bitrate']="free"; 
                break; 
            case "0001": 
                if (($fred['layer']>1)and($fred['version']>1)) 
                    { 
                        $fred['bitrate']="8000"; 
                    } 
                else 
                    { 
                        $fred['bitrate']="32000"; 
                    } 
                break; 
            case "0010": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="64000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="48000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="40000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="48000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="16000"; 
                            break; 
                    } 
                break; 
            case "0011": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="96000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="56000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="48000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="56000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="24000"; 
                            break; 
                    } 
                break; 
            case "0100": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="128000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="64000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="56000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="64000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="32000"; 
                            break; 
                    } 
                break; 
            case "0101": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="160000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="80000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="64000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="80000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="40000"; 
                            break; 
                    } 
                break; 
            case "0110": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="192000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="96000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="80000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="96000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="48000"; 
                            break; 
                    } 
                break; 
            case "0111": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="224000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="112000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="96000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="112000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="56000"; 
                            break; 
                    } 
                break; 
            case "1000": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="256000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="128000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="112000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="128000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="64000"; 
                            break; 
                    } 
                break; 
            case "1001": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="288000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="160000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="128000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="144000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="80000"; 
                            break; 
                    } 
                break; 
            case "1010": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="320000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="192000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="160000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="160000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="96000"; 
                            break; 
                    } 
                break; 
            case "1011": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="352000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="224000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="192000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="176000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="112000"; 
                            break; 
                    } 
                break; 
            case "1100": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="384000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="256000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="224000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="192000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="128000"; 
                            break; 
                    } 
                break; 
            case "1101": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="416000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="320000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="256000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="224000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="144000"; 
                            break; 
                    } 
                break; 
            case "1110": 
                switch ($index) 
                    { 
                        case "1": 
                            $fred['bitrate']="448000"; 
                            break; 
                        case "2": 
                            $fred['bitrate']="384000"; 
                            break; 
                        case "3": 
                            $fred['bitrate']="320000"; 
                            break; 
                        case "4": 
                            $fred['bitrate']="256000"; 
                            break; 
                        case "5": 
                            $fred['bitrate']="160000"; 
                            break; 
                    } 
                break; 
            case "1111": 
                $fred['bitrate']="bad"; 
                break; 
    } // eo switch 

    // Determine Sample Rate 
    switch ($fred['version']) { 
            case "1": 
                switch (substr($bitstream,12,2)) { 
                        case "00": 
                            $fred['samplerate']="44100"; 
                            break; 
                        case "01": 
                            $fred['samplerate']="48000"; 
                            break; 
                        case "10": 
                            $fred['samplerate']="32000"; 
                            break; 
                        case "11": 
                            $fred['samplerate']="reserved"; 
                            break; 
                } // eo switch 
                break; 
            case "2": 
                switch (substr($bitstream,12,2)) { 
                        case "00": 
                            $fred['samplerate']="22050"; 
                            break; 
                        case "01": 
                            $fred['samplerate']="24000"; 
                            break; 
                        case "10": 
                            $fred['samplerate']="16000"; 
                            break; 
                        case "11": 
                            $fred['samplerate']="reserved"; 
                            break; 
                } // eo switch 
                break; 
            case "2.5": 
                switch (substr($bitstream,12,2)) { 
                        case "00": 
                            $fred['samplerate']="11025"; 
                            break; 
                        case "01": 
                            $fred['samplerate']="12000"; 
                            break; 
                        case "10": 
                            $fred['samplerate']="8000"; 
                            break; 
                        case "11": 
                            $fred['samplerate']="reserved"; 
                            break; 
                } // eo switch 
                break; 
    } // eo switch 

    // Determine whether padding is set on. 0 == no & 1 == yes 
    $padding = substr($bitstream,14,1); 

    // Determine the private bit's value. Dont know what for though? 
    $private = substr($bitstream,15,1); 

    // Determine Channel mode 
    switch (substr($bitstream,16,2)) { 
            case "00": 
                $fred['cmode']="Stereo"; 
                break; 
            case "01": 
                $fred['cmode']="Joint Stereo"; 
                break; 
            case "10": 
                $fred['cmode']="Dual Channel"; 
                break; 
            case "11": 
                $fred['cmode']="Mono"; 
                break; 
    } // eo switch 
         
    /* 
    // Determine Mode Extension, actually who cares for the moment 
    switch (substr($bitstream,18,2)) { 
            case "00": 
                $mext="0"; 
                break; 
            case "01": 
                $mext="3"; 
                break; 
            case "10": 
                $mext="2"; 
                break; 
            case "11": 
                $mext="1"; 
                break; 
    } // eo switch 
    */ 
         
    // Determine Copyright 0 == no & 1 == yes 
    $fred['copyright'] = substr($bitstream,20,1); 

    // Determine Original 0 == Copy & 1 == Original 
    $fred['original'] = substr($bitstream,21,1); 

    // Determine Emphasis 
    switch (substr($bitstream,22,2)) { 
            case "00": 
                $fred['emphasis']="none"; 
                break; 
            case "01": 
                $fred['emphasis']="50/15 ms"; 
                break; 
            case "10": 
                $fred['emphasis']="reserved"; 
                break; 
            case "11": 
                $fred['emphasis']="CCIT J.17"; 
                break; 
    } // eo switch 

    // Determine number of frames. 
  if ((isset($fred['samplerate'])) and (isset($fred['bitrate']))) { 
        if ($fred['layer']=="1") { 
            $fred['frames']=floor($fred['filesize']/(floor(((12*$fred['bitrate'])/($fred['samplerate']+$padding))*4)));
		} else { 
            $fred['frames']=floor($fred['filesize']/(floor((144*$fred['bitrate'])/($fred['samplerate'])))); 
        } // eo if 
         
        // Determine number of seconds in song. 
        if ($fred['layer']=="1") { 
            $fred['time']=floor((384/$fred['samplerate'])*$fred['frames']); 
        } else { 
            $fred['time']=floor((1152/$fred['samplerate'])*$fred['frames']); 
        } // eo if 
    } // eo if 


    // MH: Get MPEG Audio Tag info 

    fseek($fp,$fred['filesize']-128); 
    $tag=fread($fp,128); 
    if (substr($tag,0,3) == "TAG") { 
        $fred['tagtitle']=substr($tag,3,30); 
        $fred['tagartist']=substr($tag,33,30); 
        $fred['tagalbum']=substr($tag,63,30); 
        $fred['tagyear']=substr($tag,93,4); 
        $fred['tagcomment']=substr($tag,97,30); 
        $fred['taggenreid']=ord(substr($tag,127,1)); 
        $fred['taggenrename']= ( $fred['taggenreid'] >= 0 && $fred['taggenreid'] <= 125) ? $genre[$fred['taggenreid']] : "(unkown)"; 
    } // has audio tag ? 

    fclose($fp); 

    $fred['filename']=$filename; 
    return($fred); 

} // eo fkt mp3info 

function thdots($s,$delim=".") { 
    while ($s) { 
        $d = substr($s,strlen($s)-3,strlen($s)) .$delim.$d; 
        $s=substr($s,0,strlen($s)-3); 
    } 
    return (substr($d,0,strlen($d)-1)); 
}


?>
