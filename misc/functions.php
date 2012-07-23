<?php

/*


           -
         /   \
      /         \
   /   MINECRAFT   \
/         PHP         \
|\       CLIENT      /|
|.   \     2     /   .|
| ..     \   /     .. |
|    ..    |    ..    |
|       .. | ..       |
\          |          /
   \       |       /
      \    |    /
         \ | /
         
         
	by @shoghicp

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.


*/


function arg($name, $default){
	global $arguments, $argv;
	if(!isset($arguments)){
		$arguments = arguments($argv);
	}
	
	if(isset($arguments["commands"][$name])){
		return $arguments["commands"][$name];
	}else{
		return $default;
	}
}

function arguments ( $args ){
	if(!is_array($args)){
		$args = array();
	}
    array_shift( $args );
    $args = join( $args, ' ' );

    preg_match_all('/ (--\w+ (?:[= ] [^-]+ [^\s-] )? ) | (-\w+) | (\w+) /x', $args, $match );
    $args = array_shift( $match );

    /*
        Array
        (
            [0] => asdf
            [1] => asdf
            [2] => --help
            [3] => --dest=/var/
            [4] => -asd
            [5] => -h
            [6] => --option mew arf moo
            [7] => -z
        )
    */

    $ret = array(
        'input'    => array(),
        'commands' => array(),
        'flags'    => array()
    );

    foreach ( $args as $arg ) {

        // Is it a command? (prefixed with --)
        if ( substr( $arg, 0, 2 ) === '--' ) {

            $value = preg_split( '/[= ]/', $arg, 2 );
            $com   = substr( array_shift($value), 2 );
            $value = join($value);

            $ret['commands'][$com] = !empty($value) ? $value : true;
            continue;

        }

        // Is it a flag? (prefixed with -)
        if ( substr( $arg, 0, 1 ) === '-' ) {
            $ret['flags'][] = substr( $arg, 1 );
            continue;
        }

        $ret['input'][] = $arg;
        continue;

    }

    return $ret;
}

function console($message, $EOL = true, $log = true, $level = 1){
	//global $path;
	if(DEBUG >= $level){
		$message .= $EOL == true ? PHP_EOL:"";
		$message = date("H:i:s"). " ". $message;
		if($log and LOG == true){
			logg($message, "console", false, $level);
		}
	
		echo $message;
	}
}

function logg($message, $name, $EOL = true, $level = 2){
	if(DEBUG >= $level and LOG == true){
		$message .= $EOL == true ? PHP_EOL:"";
		file_put_contents(FILE_PATH."/".$name.".log", $message, FILE_APPEND);
	}
}

function hexdump($data, $htmloutput = true, $uppercase = false, $return = false)
{
    // Init
    $hexi   = '';
    $ascii  = '';
    $dump   = ($htmloutput === true) ? '<pre>' : '';
    $offset = 0;
    $len    = strlen($data);
 
    // Upper or lower case hexadecimal
    $x = ($uppercase === false) ? 'x' : 'X';
 
    // Iterate string
    for ($i = $j = 0; $i < $len; $i++)
    {
        // Convert to hexidecimal
        $hexi .= sprintf("%02$x ", ord($data[$i]));
 
        // Replace non-viewable bytes with '.'
        if (ord($data[$i]) >= 32 and ord($data[$i]) < 0x80) {
            $ascii .= ($htmloutput === true) ?
                            htmlentities($data[$i]) :
                            $data[$i];
        } else {
            $ascii .= '.';
        }
 
        // Add extra column spacing
        if ($j === 7) {
            $hexi  .= ' ';
            $ascii .= ' ';
        }
 
        // Add row
        if (++$j === 16 || $i === $len - 1) {
            // Join the hexi / ascii output
            $dump .= sprintf("%04$x  %-49s  %s", $offset, $hexi, $ascii);
 
            // Reset vars
            $hexi   = $ascii = '';
            $offset += 16;
            $j      = 0;
 
            // Add newline
            if ($i !== $len - 1) {
                $dump .= "\n";
            }
        }
    }
 
    // Finish dump
    $dump .= $htmloutput === true ?
                '</pre>' :
                '';
    $dump .= "\n";
	
	$dump = preg_replace("/[^[:print:]\\r\\n]/", ".", $dump);
 
    // Output method
    if ($return === false) {
        echo $dump;
    } else {
        return $dump;
    }
}