<?php

$path = $_GET["path"];

if ($path=="") $path="./";

if (!file_exists($path)) {
  header("HTTP/1.0 404 Not Found");
  exit();
}


if (is_dir($path)) {
  if (substr($path, -1) != "/") {
    $sa = explode("/", $path);
    $last = $sa[count($sa)-1];
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $last/");
    exit();
  }
  $dir = scandir($path);
  header("Content-type: text/html");
  foreach($dir as $key => $value) {
    if (($value == ".") || ($value == "..")) continue;
    $fname=$path . "/" . $value;
    $mtime = filemtime($fname);
    $size = filesize($fname);

    // lighttpd format
    echo '<tr><td class="n"><a href="' . $value . '">';
    echo $value;
    echo '</a>';
    echo is_dir($fname)?"/":""; 
    echo '</td><td class="m">';
    echo gmdate("Y-M-d H:i:s",$mtime); //'2020-May-05 14:21:07';
    echo '</td><td class="s">';
    echo $size;
    echo '</td><td class="t">';
    echo is_dir($fname)?"Directory":'application/octet-stream';
    echo '</td></tr>';
  }
  exit();
}

$mtime = filemtime($path);
$size = filesize($path);
header("Last-Modified: " . gmdate("D, d M Y H:i:s ", $mtime) . "GMT");
header("Content-Length: $size");
header("Content-type: application/octet-stream");

if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
  exit();
}

$fp = fopen($path, 'rb');
while(!feof($fp)) {
    set_time_limit(0);
    print(fread($fp, 1024*8));
    flush();
    ob_flush();
}

fclose($fp);
exit;
