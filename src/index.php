<?php

// disable cache so that the image will be fetched every time
$timestamp = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: $timestamp");
header("Last-Modified: $timestamp");
header("Pragma: no-cache");
header("Cache-Control: no-cache, must-revalidate");
header("Content-type: image/svg+xml");

// increment the file and return the current number
function incrementFile($filename): int{
    if (file_exists($filename)) {
        $fp = fopen($filename, "r+") or die("Failed to open the file.");
        flock($fp, LOCK_EX);

        $count = fread($fp, filesize($filename)) + 1;

        ftruncate($fp, 0);
        fseek($fp, 0);
        fwrite($fp, $count);
        flock($fp, LOCK_UN);
  
        fclose($fp);
    }
 
    else {
        
        $count = 1;
        file_put_contents($filename, $count);
    }
    // return the current file contents
    return $count;
}


function shortNumber($num){
    $units = ['', 'K', 'M', 'B', 'T'];
    for ($i = 0; $num >= 1000; $i++) {
        $num /= 1000;
    }
    return round($num, 1) . $units[$i];
}

// get contents of a URL with curl
function curl_get_contents($url): string{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$message = incrementFile("views.txt");

$params = [
    "label" => "Page Views",
    "logo" => "github",
    "message" => shortNumber($message),
    "color" => "blueviolet",
    "style" => "for-the-badge"
];

// build the URL with an SVG image of the view counter
$url = "https://img.shields.io/static/v1?" . http_build_query($params);

// output the response (svg image)
echo curl_get_contents($url);