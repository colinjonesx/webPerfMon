<?php
/**
 * This file runs a couple of tests to give an indicator of the performance
 * of the box for a) comparison b) check for transient problems...
 */
$results = array(
    'server' => $_SERVER['SERVER_NAME'],
    'serverTime' => time(true)*1000
);
function formatTime($time){
  return sprintf("%f",$time);
  if($time > 0.1)
    return $time . 's';
  elseif($time > 0.001)
    return $time * 1000 . 'ms';
  else return $time * 1000000 . 'us';
}

$overallStart = microtime(true);

$start = microtime(true);
$tempFilename = tempnam('.', 'phpPerf');
$tempFileHndl = fopen($tempFilename, 'w');
$results['fileCreation'] = formatTime(microtime(true) - $start);

$start = microtime(true);
$iterations = 5000;
$result = array();
for($i=0; $i< $iterations;$i++){
  $result[] = exp($i * rand() * pi());
}
$results['mathCalcs'] = formatTime(microtime(true) - $start);
$result = implode("Here Is A Little Padding to bulk up the filesize\r\n",$result);

$start = microtime(true);
$written = fwrite($tempFileHndl, $result);
$results['fileWrite'] = formatTime(microtime(true) - $start);
fclose($tempFileHndl);

$start = microtime(true);
if(is_file($tempFilename)) unlink($tempFilename);
$results['fileDelete'] = formatTime(microtime(true) - $start);

$results['total'] = formatTime(microtime(true) - $overallStart);

echo json_encode($results);