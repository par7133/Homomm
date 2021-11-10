<?php

//REQUEST_METHOD
$requestMethod = (string)filter_input(INPUT_SERVER, "REQUEST_METHOD");
if ($requestMethod != "POST") {
  die("Wrong request method!");
}  

//userHint
$chatHintResolved = PHP_STR;
$chatHint = substr((string)filter_input(INPUT_POST, "chatHint"), 0, 50);

if ($chatHint == PHP_STR) {
  die("Wrong chat hint!");
} else {
 
   $found=false;
   foreach ($CONFIG['AUTH'] as $key => $val) {
     if ($chatHint==$val['USERNAME']) {
       $chatHintResolved = $key;
       $found=true;
       break;
     }      
   }
   if (!$found) {
     die("Invalid chat!"); 
   }  
}    

$pwd = $CONFIG['AUTH'][$chatHintResolved]['REPO_FOLDER'];
$curPath = APP_REPO_PATH . DIRECTORY_SEPARATOR . $pwd;

$mysha = PHP_STR;
$amsgs = file($curPath . DIRECTORY_SEPARATOR . ".HMM_history");
if (count($amsgs) > 0) {
  $val = rtrim($amsgs[count($amsgs)-1],"\n");
  $mysha = hash("sha256", $val . APP_SALT, false);  
}     

echo json_encode([200, $mysha]);




