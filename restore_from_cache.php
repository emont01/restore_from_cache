#!/usr/bin/env php
<?php

// just to make sure this script is run from CLI
if (PHP_SAPI != "cli") {
    print('This script must be runned from CLI');
    exit;
}

// --gzip flag present
$use_gzip = in_array('--gzip', $argv);

// cache.log is a copy of chrome or firefox cache page with only the file content section
$cacheString = file_get_contents("cache.log");

// recover file from cache.
if($use_gzip){
    gzip_text_hex($cacheString);
} else {
    plain_text_hex($cacheString);
}

/**
 * Extract and convert the hexadecimal representation into the text version, writing everything
 * to the standart output.
 * This function asumes that $cacheString contains plain text hex characters
 *
 * @see http://www.alexkorn.com/blog/2010/05/how-to-recover-deleted-javascript-files-using-the-cache-in-chrome-or-firefo/
 * @param  string $cacheString cache log string from browser cache
 * @return void
 */
function plain_text_hex($cacheString) {
    $matches = array();
    preg_match_all('/\s[0-9a-f]{2}\s/', $cacheString, $matches);
    foreach ($matches[0] as $match)
    {
        echo chr(hexdec($match));
    }
}

/**
 * Extract and convert the hexadecimal representation into the text version, writing everything
 * to the standart output.
 * This function asumes that $cacheString contains gzip encoded data
 *
 * @param  string $cacheString cache log string from browser cache
 * @return void
 */
function gzip_text_hex($cacheString){
    $matches = array();
    preg_match_all('/\s[0-9a-f]{2}\s/', $cacheString, $matches);
    $f = fopen("t.bin","wb");
    foreach ($matches[0] as $match)
    {
        fwrite($f,chr(hexdec($match)));
    }
    fclose($f);

    ob_start();
    readgzfile("t.bin");
    $decoded_data = ob_get_clean();
    echo $decoded_data;
}
?>