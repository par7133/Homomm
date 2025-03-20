<?php

/**
 * Copyright 2021, 2024 5 Mode
 *
 * This file is part of Homomm.
 *
 * Homomm is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Homomm is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.  
 * 
 * You should have received a copy of the GNU General Public License
 * along with Homomm. If not, see <https://www.gnu.org/licenses/>.
 *
 * home.php
 * 
 * Homomm home page.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2024, 5 Mode      
 */
 
 $msgHistory = [];
 $cmd = PHP_STR;
 $opt = PHP_STR;
 $param1 = PHP_STR;
 $param2 = PHP_STR;
 $param3 = PHP_STR;
   
 $user = PHP_STR;
 $userName = PHP_STR;
 $chatHint = PHP_STR;
 $chatHintResolved = PHP_STR;
 $picPath = PHP_STR;
 $curPicture = PHP_STR;
 $curLocale = APP_LOCALE;
 $lastMessage = PHP_STR;
 

 function showHistory() {
   global $msgHistory;
   global $user;
   global $curPath;
   global $picPath;
   global $CONFIG;
   global $curLocale;
   global $LOCALE;
   global $EMOTICONS;
   global $lastMessage;
   
   $i = 1;	 
   
   $totMsgs = count($msgHistory);
    
   $oldDate = "";
   $m = 1;
   foreach($msgHistory as $val) {
     
     $delFunc = false;
     
     if ((mb_stripos($val, "-master") !== false) && ($user == "MASTER")) {
       $float = "right";
       $bgcolor = "#E3FAE3"; 
     } else if ((mb_stripos($val, "-master") === false) && ($user != "MASTER")) {
       $float = "right";
       $bgcolor = "#E3FAE3";
     } else {
       $float = "left";
       $bgcolor = "#FFFFFF";
     }
     echo("<div style='width:100%;height:auto;border:0px solid red;margin-bottom:12px;'>");
     $val = rtrim($val,"\n");
     // grab the date converting to the given time zone..
     //$dateori = left($val, 8);
     $dated = new DateTime(left($val,4)."-".substr($val,4,2)."-".substr($val,6,2)." ".substr($val,9,2).":".substr($val,11,2).":".substr($val,13,2));
     $dated = date_add1("H", ltrim($CONFIG['AUTH'][$user]['TIMEZONE'],"+")-APP_SERVER_TIMEZONE, $dated); 
     $date = $dated->format("l j F");
     //$date = date("l j F", mktime(0,0,0,substr($dateori,4,2),right($dateori,2),left($dateori,4))); 
     
     if (in_array($curLocale, ["CN", "JP", "KR"])) {
       $date = str_phrase_reverse($date);
     }  
     $date = getResource($date, $curLocale);
     
     if ($date!=$oldDate) {
       echo("<div style='text-align:center;'><span style='background-color:gray;color:#FFFFFF'>$date</span></div><br>");  
       $oldDate = $date;
     }  
     // grab the time
     //preg_match('/^.+-(\d{6})-/i', $val, $matches);
     //$timereg = $matches[1];
     //$time = ltrim(left($timereg,2),"0") . ":" . substr($timereg,2,2);
     $time = $dated->format("H:i");
     
     // Checking for del functionality..
     // If it is one of the logged user msg..
     if ((($m==$totMsgs) || ($m==$totMsgs-1)) && ($float === "right")) {
       // file date
       //$origin = new DateTime(left($dateori,4) ."-". substr($dateori,4,2) ."-". right($dateori,2) . " " . left($timereg,2) .":". substr($timereg,2,2) .":". "00");
       //echo($dated->format("YMd H:i:s"));
       // current date
       $target = new DateTime();
       $interval = $dated->diff($target);
       $minInterval = $interval->format("%i");
       
       if ($minInterval<2) {
         $delFunc = true; 
       } 
     }
     
     if (is_image($val)) {
       // display the img
       $img = substr($picPath, strlen(APP_PATH)) . DIRECTORY_SEPARATOR . $val; 
       
       $deldiv=PHP_STR;
       if ($delFunc) {
         $deldiv = "<div style='float:right;width:17px;position:relative;top:-4px;height:11px;cursor:pointer' onclick=\"deletePic('$val')\"><img src='/res/del.png' style='width:11px;'></div>";  
       }  
       
       echo("<div style='background-color:$bgcolor;float:$float;padding:5px;max-width:300px;min-width:260px;border-radius:2px;cursor:pointer;' onclick=\"openPic('$val')\"><img src='$img' style='width:100%;'><div style='float:right;font-size:9px;'>$time</div>$deldiv</div><br><br><br>");

     } else {  
       // display the msg
       $msg = HTMLencode(file_get_contents($curPath . DIRECTORY_SEPARATOR . "msgs" . DIRECTORY_SEPARATOR . $val));
       
       $msg = enableEmails($msg);
       $msg = enableLinks($msg);
       $msg = enableEmoticons($msg);
       
       $deldiv=PHP_STR;
       if ($delFunc) {
         $deldiv = "<div style='float:right;width:17px;position:relative;top:-4px;height:11px;cursor:pointer' onclick=\"deleteMsg('$val')\"><img src='/res/del.png' style='width:11px;'></div>";  
       }  

       echo("<div style='background-color:$bgcolor;float:$float;padding:5px;max-width:300px;min-width:260px;border-radius:2px;white-space:normal;'>".str_replace("\n", "<br>", $msg)."<div style='float:right;font-size:9px;'>$time</div>$deldiv</div><br><br><br>");
     }	   

     echo("<div style='clear:both;'></div>");
     echo("</div>");

     $lastMessage = hash("sha256", $val . APP_SALT, false);
	   $m++;   
   }
 }
 
 
function updateHistory(&$update, $maxItems) {
   global $msgHistory;
   global $curPath;
   global $picPath;
   
   // Making enough space in $msgHistory for the update..
   $shift = (count($msgHistory) + count($update)) - $maxItems;
   if ($shift > 0) {
     $msgHistory = array_slice($msgHistory, $shift, $maxItems); 
   }		  
   // Adding $msgHistory update..
   if (count($update) > $maxItems) {
     $beginUpd = count($update) - ($maxItems-1);
   } else {
	   $beginUpd = 0;
   }	        
   $update = array_slice($update, $beginUpd, $maxItems); 
   foreach($update as $val) {  
	   $msgHistory[] = $val;   
   }
   // Deleting unused message files..
   foreach (glob($curPath . DIRECTORY_SEPARATOR . "msgs" . DIRECTORY_SEPARATOR . "*.msg") as $msgFilePath) {
     $msgFileName = basename($msgFilePath);
     if (!in_array($msgFileName."\n", $msgHistory)) {
       unlink($curPath . DIRECTORY_SEPARATOR . "msgs" . DIRECTORY_SEPARATOR . $msgFileName);
     }  
   }
   // Deleting unused pic files..
   foreach (glob($picPath . DIRECTORY_SEPARATOR . "*") as $imgFilePath) {
     $imgFileName = basename($imgFilePath);
     if (!in_array($imgFileName."\n", $msgHistory)) {
       unlink($picPath . DIRECTORY_SEPARATOR . $imgFileName);
     }  
   }
   
   // Writing out $msgHistory on disk..
   $filepath = $curPath . DIRECTORY_SEPARATOR . ".HMM_history";
   file_put_contents($filepath, implode('', $msgHistory));	 
 }
 

 function parseCommand() {
   global $command;
   global $cmd;
   global $opt;
   global $param1;
   global $param2;
   global $param3;
   
   $str = trim($command);
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $cmd = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $cmd = $str;
	 return;
   }	     
   
   if (left($str, 1) === "-") {
	 $ipos = stripos($str, PHP_SPACE);
	 if ($ipos > 0) {
	   $opt = left($str, $ipos);
	   $str = substr($str, $ipos+1);
	 } else {
	   $opt = $str;
	   return;
	 }	     
   }
   
   if (left($str, 1) === "'") {
     $ipos = stripos($str, "'", 1);
     if ($ipos > 0) {
       $param1 = substr($str, 0, $ipos+1);
       $str = substr($str, $ipos+1);
     } else {
       $param1 = $str;
       return;
     }  
   } else {   
     $ipos = stripos($str, PHP_SPACE);
     if ($ipos > 0) {
       $param1 = left($str, $ipos);
       $str = substr($str, $ipos+1);
     } else {
       $param1 = $str;
       return;
     }	     
   } 
  
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param2 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param2 = $str;
	 return;
   }
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param3 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param3 = $str;
	 return;
   }	     
 	     
 }
  
 function upload() {

   global $curPath;
   global $user;
   global $userName;
   global $picPath;
   global $msgSign;

   //if (!empty($_FILES['files'])) {
   if (!empty($_FILES['files']['tmp_name'][0])) {
	   
     $uploads = (array)fixMultipleFileUpload($_FILES['files']);
     
     //no file uploaded
     if ($uploads[0]['error'] === PHP_UPLOAD_ERR_NO_FILE) {
       echo("WARNING: No file uploaded.");
       return;
     } 

     $google = "abcdefghijklmnopqrstuvwxyz";
     if (count($uploads)>strlen($google)) {
       echo("WARNING: Too many uploaded files."); 
       return;
     }

     // Checking for repeated upload cause ie. caching prb..
     $duplicateMsgs = glob($picPath . DIRECTORY_SEPARATOR . date("Ymd-H") . "*-$msgSign*.*");
     if (!empty($duplicateMsgs)) {
       echo("WARNING: destination already exists");
       return;
     }	   

     $i=1;
     foreach($uploads as &$upload) {
		
       switch ($upload['error']) {
       case PHP_UPLOAD_ERR_OK:
         break;
       case PHP_UPLOAD_ERR_NO_FILE:
         echo("WARNING: One or more uploaded files are missing.");
         return;
       case PHP_UPLOAD_ERR_INI_SIZE:
         echo("WARNING: File exceeded INI size limit.");
         return;
       case PHP_UPLOAD_ERR_FORM_SIZE:
         echo("WARNING: File exceeded form size limit.");
         return;
       case PHP_UPLOAD_ERR_PARTIAL:
         echo("WARNING: File only partially uploaded.");
         return;
       case PHP_UPLOAD_ERR_NO_TMP_DIR:
         echo("WARNING: TMP dir doesn't exist.");
         return;
       case PHP_UPLOAD_ERR_CANT_WRITE:
         echo("WARNING: Failed to write to the disk.");
         return;
       case PHP_UPLOAD_ERR_EXTENSION:
         echo("WARNING: A PHP extension stopped the file upload.");
         return;
       default:
         echo("WARNING: Unexpected error happened.");
         return;
       }
      
       if (!is_uploaded_file($upload['tmp_name'])) {
         echo("WARNING: One or more file have not been uploaded.");
         return;
       }
      
       // name	 
       $name = (string)substr((string)filter_var($upload['name']), 0, 255);
       if ($name == PHP_STR) {
         echo("WARNING: Invalid file name: " . $name);
         return;
       } 
       $upload['name'] = $name;
       
       // fileType
       $fileType = substr((string)filter_var($upload['type']), 0, 30);
       $upload['type'] = $fileType;	 
       
       // tmp_name
       $tmp_name = substr((string)filter_var($upload['tmp_name']), 0, 300);
       if ($tmp_name == PHP_STR || !file_exists($tmp_name)) {
         echo("WARNING: Invalid file temp path: " . $tmp_name);
         return;
       } 
       $upload['tmp_name'] = $tmp_name;
       
       //size
       $size = substr((string)filter_var($upload['size'], FILTER_SANITIZE_NUMBER_INT), 0, 12);
       if ($size == "") {
         echo("WARNING: Invalid file size.");
         return;
       } 
       $upload["size"] = $size;

       $tmpFullPath = $upload["tmp_name"];
       
       $originalFilename = pathinfo($name, PATHINFO_FILENAME);
       $originalFileExt = pathinfo($name, PATHINFO_EXTENSION);
       $fileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));

       $date = date("Ymd-His");
       $rnd = $msgSign;    
       
       if ($originalFileExt!==PHP_STR) {
         if ($user == "MASTER") {
           $destFileName = $date . "-" . $rnd . substr($google, $i-1, 1) . "-master.$fileExt";
         } else {
           $destFileName = $date . "-" . $rnd . substr($google, $i-1, 1) . "-$userName.$fileExt";
         }  
       } else {
         return; 
       }	   
       $destFullPath = $picPath . DIRECTORY_SEPARATOR . $destFileName;

       if (file_exists($destFullPath)) {
         echo("WARNING: destination already exists");
         return;
       }	   

       copy($tmpFullPath, $destFullPath);

       // Updating history..
       $output = [];
       $output[] = $destFileName . "\n";   
       updateHistory($output, HISTORY_MAX_ITEMS);
    
       // Cleaning up..
      
       // Delete the tmp file..
       unlink($tmpFullPath); 
       
       $i++;
        
     }	 
   }
 }
 	  
  
 function myExecSendMessage() {
    
    global $curPath;
    global $message;
    global $user;
    global $userName;
    global $sendSMS;
    global $CONFIG;
    global $chatHintResolved; 
    global $msgSign;
    
    $date = date("Ymd-His");
    $rnd = $msgSign;    
 
    $duplicateMsgs = glob($curPath . DIRECTORY_SEPARATOR . "msgs" . DIRECTORY_SEPARATOR . date("Ymd-H") . "*-$msgSign*.msg");
    if (!empty($duplicateMsgs)) {
      return;  
    }  
      
    if (!empty($message)) {
    
      if ($user == "MASTER") {
        $fileName = $date . "-" . $rnd . "-master.msg";
      } else {
        $fileName = $date . "-" . $rnd . "-$userName.msg";
      }  
      
      $msg = $message;
      if (right($msg,1)!="\n") {
        $msg = $msg . "\n";  
      }  

      // Creating the msg file..      
      file_put_contents($curPath . DIRECTORY_SEPARATOR . "msgs" . DIRECTORY_SEPARATOR . $fileName, $msg);
      
      // Updating message history..
      $output = [];
      $output[] = $fileName . "\n";
      updateHistory($output, HISTORY_MAX_ITEMS);
      
      if ($user == "MASTER") {
        $smsUser = $chatHintResolved; 
      } else {
        $smsUser = "MASTER";
      }  
         
      // Sending out the sms notifcation..
      if ($sendSMS && SMS_USERNAME!=PHP_STR) {
        $message = array(
         'To'=>$CONFIG['AUTH'][$smsUser]['PHONE'], 
         'MessagingServiceSid'=>SMS_MESSAGING_SERVICE, 
         'Body'=>SMS_BODY
        );
          
        sendSMS($message, SMS_API_URL, SMS_USERNAME, SMS_PASSWORD);
      }      
    }
 }
 
 function delMsgParamValidation() 
 {

	global $curPath;
	global $opt;
	global $param1;
	global $param2; 
	global $param3;

	//opt!=""
  if ($opt!==PHP_STR) {
	  //updateHistoryWithErr("invalid options");	
    return false;
  }	
	//param1!="" and isword  
	if (($param1===PHP_STR) || !is_word($param1)) {
	  //updateHistoryWithErr("invalid msg file");	
    return false;
  }
	//param2==""
	if ($param2!==PHP_STR) {
    //updateHistoryWithErr("invalid parameters");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    //updateHistoryWithErr("invalid parameters");
    return false;
  }
	//param1 exist
	$path = $curPath . DIRECTORY_SEPARATOR . "msgs" . DIRECTORY_SEPARATOR . $param1;
	if (!file_exists($path)) {
    //updateHistoryWithErr("file must exists");	
	  return false;
	}  	
	//param1 is_file
	if (!is_file($path)) {
    //updateHistoryWithErr("invalid msg file");	
	  return false;
	}  	
  //param1 file extension == msg
  if (!is_msg($param1)) {
	  //updateHistoryWithErr("invalid msg file");	
	  return false;
  }    

  // Checking file date
  
  // grab date
  $dateori = left($param1, 8);
  // grab time
  preg_match('/^.+-(\d{6})-/i', $param1, $matches);
  $timereg = $matches[1];
  
  $origin = new DateTime(left($dateori,4) ."-". substr($dateori,4,2) ."-". right($dateori,2) . " " . left($timereg,2) .":". substr($timereg,2,2) .":". right($timereg,2));
  //echo($origin->format("YMd H:i:s"));
  // current date
  $target = new DateTime();
  $interval = $origin->diff($target);
  $minInterval = $interval->format("%i");
         
  if ($minInterval>=2) {
    return false; 
  } 
  
	return true;
   
 }  
  
 
 function myExecDelMsgCommand() {
   
   global $curPath;
   global $param1;
   global $msgHistory;
   
   // searching the file name in the msgHsitory
   $msgkey = array_search($param1."\n", $msgHistory);
   if ($msgkey !== false) {
   
     // Clearing out the msg from the history..
     unset($msgHistory[$msgkey]);
     $hpath = $curPath . DIRECTORY_SEPARATOR . ".HMM_history";
     file_put_contents($hpath, implode('', $msgHistory));	 
     
     // Deleting the msg file..
     $msgpath = $curPath . DIRECTORY_SEPARATOR . "msgs" . DIRECTORY_SEPARATOR . $param1;
     if (file_exists($msgpath)) {
       unlink($msgpath); 
     }  
   }     
 }   
  

 function delPicParamValidation() 
 {

	global $picPath;
	global $opt;
	global $param1;
	global $param2; 
	global $param3;

	//opt!=""
  if ($opt!==PHP_STR) {
	  //updateHistoryWithErr("invalid options");	
    return false;
  }	
	//param1!="" and isword  
	if (($param1===PHP_STR) || !is_word($param1)) {
	  //updateHistoryWithErr("invalid pic file");	
    return false;
  }
	//param2==""
	if ($param2!==PHP_STR) {
    //updateHistoryWithErr("invalid parameters");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    //updateHistoryWithErr("invalid parameters");
    return false;
  }
	//param1 exist
	$path = $picPath . DIRECTORY_SEPARATOR . $param1;
	if (!file_exists($path)) {
    //updateHistoryWithErr("pic must exists");	
	  return false;
	}  	
	//param1 is_file
	if (!is_file($path)) {
    //updateHistoryWithErr("invalid pic file");	
	  return false;
	}  	
  //param1 is_image
  if (!is_image($param1)) {
  	//updateHistoryWithErr("invalid pic file");	
	  return false;
  }    

  // Checking file date
  
  // grab date
  $dateori = left($param1, 8);
  // grab time
  preg_match('/^.+-(\d{6})-/i', $param1, $matches);
  $timereg = $matches[1];
  
  $origin = new DateTime(left($dateori,4) ."-". substr($dateori,4,2) ."-". right($dateori,2) . " " . left($timereg,2) .":". substr($timereg,2,2) .":". right($timereg,2));
  //echo($origin->format("YMd H:i:s"));
  // current date
  $target = new DateTime();
  $interval = $origin->diff($target);
  $minInterval = $interval->format("%i");
         
  if ($minInterval>=2) {
    return false; 
  } 
  
	return true;
   
 }  
  
 
 function myExecDelPicCommand() {
   
   global $picPath; 
   global $curPath;
   global $param1;
   global $msgHistory;
   
   // searching the file name in the msgHistory
   $msgkey = array_search($param1."\n", $msgHistory);
   if ($msgkey !== false) {
   
     // Clearing out the msg from the history..
     unset($msgHistory[$msgkey]);
     $hpath = $curPath . DIRECTORY_SEPARATOR . ".HMM_history";
     file_put_contents($hpath, implode('', $msgHistory));	 
     
     // Deleting the pic file..
     $picpath = $picPath . DIRECTORY_SEPARATOR . $param1;
     if (file_exists($picpath)) {
       unlink($picpath); 
     }  
   }     
 }   

 
 function openPicParamValidation() 
 {

	global $picPath;
	global $opt;
	global $param1;
	global $param2; 
	global $param3;

	//opt!=""
  if ($opt!==PHP_STR) {
	  //updateHistoryWithErr("invalid options");	
    return false;
  }	
	//param1!="" and isword  
	if (($param1===PHP_STR) || !is_word($param1)) {
	  //updateHistoryWithErr("invalid pic file");	
    return false;
  }
	//param2==""
	if ($param2!==PHP_STR) {
    //updateHistoryWithErr("invalid parameters");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    //updateHistoryWithErr("invalid parameters");
    return false;
  }
	//param1 exist
	$path = $picPath . DIRECTORY_SEPARATOR . $param1;
	if (!file_exists($path)) {
    //updateHistoryWithErr("pic must exists");	
	  return false;
	}  	
	//param1 is_file
	if (!is_file($path)) {
    //updateHistoryWithErr("invalid pic file");	
	  return false;
	}  	
  //param1 is_image
  if (!is_image($param1)) {
	  //updateHistoryWithErr("invalid pic file");	
	  return false;
  }    

	return true;
   
 }  
 
 
 function myExecOpenPicCommand() {
   
   global $picPath; 
   global $curPicture;
   global $param1;
   
   $curPicture = substr($picPath.DIRECTORY_SEPARATOR.$param1, strlen(dirname(APP_PIC_PATH)));
 
 }   

  
 $password = filter_input(INPUT_POST, "Password")??"";
 $password = strip_tags($password);
 if ($password==PHP_STR) {
   $password = filter_input(INPUT_POST, "Password2")??"";
   $password = strip_tags($password);
 }  
 $command = filter_input(INPUT_POST, "CommandLine")??"";
 $command = strip_tags($command);
 $message = filter_input(INPUT_POST, "MessageLine")??"";
 $message = strip_tags($message);
 $sendSMS1 = filter_input(INPUT_POST, "chkSMS")??"";
 $sendSMS1 = strip_tags($sendSMS1);
 $oldMsgSign = filter_input(INPUT_POST, "old-msg-sign")??"";
 $oldMsgSign = strip_tags($oldMsgSign);
 $msgSign = filter_input(INPUT_POST, "msg-sign")??"";
 $msgSign = strip_tags($msgSign);
 
 if ($sendSMS1!=PHP_STR) {
   $sendSMS = true;
 } else {
   $sendSMS = false;
 }    
 $pwd = PHP_STR;
 
 $chatHint = filter_input(INPUT_POST, "chatHint")??""; 
 $chatHint = strip_tags($chatHint);
 
 // chat validation
 $chatHintResolved = PHP_STR;
 if ($chatHint!=PHP_STR) {
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
 
//echo ("chatHint*=".$chatHint."<br>");
//echo ("chatHintResolved*=".$chatHintResolved."<br>");
  
 $hideSplash = filter_input(INPUT_POST, "hideSplash")??"";
 $hideSplash = strip_tags($hideSplash);
 $hideHCSplash = filter_input(INPUT_POST, "hideHCSplash")??"";
 $hideHCSplash = strip_tags($hideHCSplash);

 //echo "password=*$password*<br>";
 if ($password != PHP_STR) {	
	$hash = hash("sha256", $password . APP_SALT, false);
  
  $found=false;
  foreach ($CONFIG['AUTH'] as $key => $val) {
    //echo ("username=".$val['USERNAME']."<br>");
    if ($hash==$val['HASH']) {
      $user = $key;          
      if ($chatHintResolved==PHP_STR) {
        $chatHint=$val['USERNAME'];
        $chatHintResolved = $key;
      } else {
        if ($user != "MASTER") {
          if ($user != $chatHintResolved) {
            $found=false;
            break;
          } 
        }
      }   
      
      $found=true;
          
      //echo ("user=".$user."<br>");
      //echo ("chatHint**=".$chatHint."<br>");
      //echo ("chatHintResolved**=".$chatHintResolved."<br>");
      
      break;
    }    
  }  
  if (!$found) {
    $password=PHP_STR; 
  }  
  
  if ($password != PHP_STR) {
    $userName = $CONFIG['AUTH'][$user]['USERNAME'];
    // xxx
    //$pwd = APP_REPO_PATH . DIRECTORY_SEPARATOR . $CONFIG['AUTH'][$chatHintResolved]['REPO_FOLDER'];
    $pwd = $CONFIG['AUTH'][$chatHintResolved]['REPO_FOLDER'];
    $picPath =  APP_PIC_PATH . DIRECTORY_SEPARATOR . $CONFIG['AUTH'][$chatHintResolved]['PIC_FOLDER'];
    $curLocale = $CONFIG['AUTH'][$user]['LOCALE'];
  }    
 } 
 
 $curPath = APP_REPO_PATH;
 if ($pwd!=PHP_STR) {
   //if (left($pwd, strlen(APP_REPO_PATH)) === APP_REPO_PATH) {
   //  $curPath = $pwd;
   if (file_exists(APP_REPO_PATH . DIRECTORY_SEPARATOR . $pwd)) {
     
     $curPath = APP_REPO_PATH . DIRECTORY_SEPARATOR . $pwd;
     
     chdir($curPath);
     
     if (!file_exists($curPath . DIRECTORY_SEPARATOR . ".HMM_history")) {
       $output = [];
       file_put_contents($curPath . DIRECTORY_SEPARATOR . ".HMM_history", $output);
     }

     if (!file_exists($curPath . DIRECTORY_SEPARATOR . "msgs")) {
       mkdir("msgs", 0777);
     }
     
   } else {
     // xxx
     $password = PHP_STR;
   }
   
 } else {
   // xxx
   $password = PHP_STR;
 }
   	 
 $ipos = strripos($curPath, PHP_SLASH);
 $curDir = substr($curPath, $ipos);
 
 
 if ($password != PHP_STR) {
   
   $msgHistory = file($curPath . DIRECTORY_SEPARATOR . ".HMM_history");
   
   parseCommand($command);
   //echo("cmd=" . $cmd . "<br>");
   //echo("opt=" . $opt . "<br>");
   //echo("param1=" . $param1 . "<br>");
   //echo("param2=" . $param2 . "<br>");
   
   //upload();
   
   if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $command . "|")) {
     
     if ($command === "sendmsg") {
       if (trim($message,"\n")!==PHP_STR) {
         myExecSendMessage();
         upload();  
       }  
     } else if ($command === "refreshbrd") {
       // refreshing Msg Board..
     }

  
   } else if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $cmd . "|")) {

     if ($cmd === "delmsg") {
       if (delMsgParamValidation()) {
         myExecDelMsgCommand();
       }	
     } else if ($cmd === "delpic") {
       if (delPicParamValidation()) {
         myExecDelPicCommand();
       }	
     } else if ($cmd === "openpic") {
       if (openPicParamValidation()) {
         myExecOpenPicCommand();
       }	
     }

       
   } else {
     
     // if I'm not saving data..
     //if (empty($editBoardParams) || $editBoardParams[0]['location']===PHP_STR) {
     //  if (empty($_FILES['files']['tmp_name'][0])) {  
     //    updateHistoryWithErr("invalid command");
     //  }
     //}      
   }
      
 } else {
   
   $msgHistory = [];	 
 
 }
 
 ?>
 

<!DOCTYPE html>
<head>
	
  <meta charset="UTF-8"/>
  
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  
<!--
    Copyright 2021, 2024 5 Mode

    This file is part of Homomm.

    Homomm is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Homomm is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Homomm. If not, see <https://www.gnu.org/licenses/>.
 -->
  
    
  <title>Homomm: every person its messages..</title>
	
  <link rel="shortcut icon" href="/favicon.ico?v=<?php echo(time()); ?>" />
    
  <meta name="description" content="Welcome to <?php echo(APP_NAME); ?>"/>
  <meta name="author" content="5 Mode"/> 
  <meta name="robots" content="noindex"/>
  
  <script src="/js/jquery-3.6.0.min.js" type="text/javascript"></script>
  <script src="/js/common.js" type="text/javascript"></script>
  <script src="/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="/js/sha.js" type="text/javascript"></script>
  
  <script src="/js/home.js?v=<?php echo(time()); ?>" type="text/javascript" defer></script>
  
  <link href="/css/bootstrap.min.css" type="text/css" rel="stylesheet">
  <link href="/css/style.css?v=<?php echo(time()); ?>" type="text/css" rel="stylesheet">
     
</head>
<body>

<?php if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . "jscheck.html")): ?>
<?php include(APP_PATH . DIRECTORY_SEPARATOR . "jscheck.html"); ?> 
<?php endif; ?>

<?php

  // Sorting friend list..

  function sort_friends_coll($a, $b) 
  {
     return strcmp($a["USERNAME"], $b["USERNAME"]);    
  }  

  $AUTH = $CONFIG['AUTH'];
 
  usort($AUTH, "sort_friends_coll");
 
  //print_r($AUTH);
?>

<div id="HCsplash" style="padding-top: 160px; text-align:center;color:#ffffff;display:none;">
   <div id="myh1"><H1>Homomm</H1></div><br>
   <img src="/res/HMMlogo2.png" style="width:310px;">
</div>

<?php
  if ($curPicture != PHP_STR) {
    
    $apic = glob($picPath . DIRECTORY_SEPARATOR . "*");
    
    $i=0;
    foreach($apic as &$path) {
      $fileName = basename($path);
      if (is_file($picPath . DIRECTORY_SEPARATOR . $fileName)) {
        $path=$fileName;
      } else {
        unset($apic[$i]); 
      } 
      $i++;  
    }
      
    $i=array_search(basename($curPicture), $apic);
    // if the only one
    if (count($apic)==1) {
      $prevPicture = basename($apic[0]);
      $nextPicture = basename($apic[0]);
    // if first  
    } else if ($i==0) {
      $prevPicture = basename($apic[count($apic)-1]);
      $nextPicture = basename($apic[1]);
    // if last        
    } else if ($i==(count($apic)-1)) {
      $prevPicture = basename($apic[$i-1]);
      $nextPicture = basename($apic[0]);      
    } else {
      $prevPicture = basename($apic[$i-1]);
      $nextPicture = basename($apic[$i+1]);      
    }    
    
    $hidePlayer = "0";
  } else {
    $hidePlayer = "1";    
  }    
?>
<div id="picPlayer" style="width:100%;height:1900px;vertical-align:middle;text-align:center;background:#000000;display:<?php echo(($hidePlayer==="1"? "none": "inline"));?>;">
   <div id="closePlayer" style="position: absolute; top:20px; left:20px; cursor:pointer;" onclick="closePlayer()"><img src="/res/parent.png" style="width:64px;"></div>
   <div id="myPicCont" style="width:100%;max-width:100%;clear:both;margin:auto;vertical-align:middle;background:#000000;"><img id="myPic" src="<?php echo($curPicture);?>" style="width:100%;vertical-align:middle;display:none;;background:#000000;"></div>
   <div id="navPlayer1" style="position:absolute;top:3000px;width:175px;cursor:pointer;overflow-x:hidden;border:0px solid red;" onclick="openPic('<?php echo($prevPicture);?>')"><img src="/res/picPrev.png" style="width:200px;position:relative;left:-125px;"></div>
   <div id="navPlayer2" style="position:absolute;top:3000px;width:175px;cursor:pointer;overflow-x:hidden;border:0px solid red;" onclick="openPic('<?php echo($nextPicture);?>')"><img src="/res/picNext.png" style="width:200px;position:relative;left:+100px;"></div>
</div>

<form id="frmHC" method="POST" action="/" target="_self" enctype="multipart/form-data" style="display:<?php echo((($hideHCSplash == "1") && ($hidePlayer == "1")? "inline": "none"));?>;">

<div class="header">
   <a id="burger-menu" href="#" style="display:none;"><img src="/res/burger-menu2.png" style="width:58px;"></a><a id="ahome" href="http://homomm.5mode-foss.eu" target="_blank" style="color:black; text-decoration: none;"><img id="logo-hmm" src="/res/HMMlogo2.png" style="width:48px;">&nbsp;Homomm</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="agithub" href="https://github.com/par7133/Homomm" style="color:#000000"><span style="color:#119fe2">on</span> github</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="afeedback" href="mailto:code@gaox.io" style="color:#000000"><span style="color:#119fe2">for</span> feedback</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="asupport" href="tel:+39-378-0812391" style="font-size:13px;background-color:#15c60b;border:2px solid #15c60b;color:black;height:27px;text-decoration:none;">&nbsp;&nbsp;get support&nbsp;&nbsp;</a><div id="pwd2" style="float:right;position:relative;top:+13px;display:none"><input type="password" id="Password2" name="Password2" placeholder="password" style="font-size:13px; background:#393939; color:#ffffff; width: 125px; border-radius:3px;" value="<?php echo($password);?>" autocomplete="off"></div>
</div>

<div style="clear:both;"></div>

<table class="friend-header" style="width:100%;border:3px solid #e4f5f7;display:none;">
<tr>
<td style="width:100%;background:#e4f5f7;">    
<?php if ($user!="MASTER"): ?>
    <div class="friend-header-ve" style="float:left;width:31%;font-size:14px;padding:4px;border:3px solid #e4f5f7;margin-top:2px;margin-right:2px;margin-bottom:2px;text-align:left;cursor:pointer;">&nbsp;&nbsp;<a href="https://github.com/par7133/Homomm" style="text-decoration:none;color:black;">on github</a></div>
    <div class="friend-header-ve" style="float:left;width:31%;font-size:14px;padding:4px;border:3px solid #e4f5f7;margin-top:2px;margin-right:2px;margin-bottom:2px;text-align:left;cursor:pointer;">&nbsp;&nbsp;<a href="mailto:code@gaox.io" style="text-decoration:none;color:black;">for feedback</a></div>
    <div class="friend-header-ve" style="float:left;width:31%;font-size:14px;padding:4px;border:3px solid #e4f5f7;margin-top:2px;margin-right:2px;margin-bottom:2px;text-align:left;cursor:pointer;">&nbsp;&nbsp;<a href="tel:+39-331-0812391" style="text-decoration:none;color:black;">get support</a></div>
<?php else: ?>
    <?php foreach($AUTH as $key => $val): 
            $myusername = $val['USERNAME'];
            $currentChatClass = PHP_STR;
            if ($myusername == $chatHint) {
              $currentChatClass = "friend-header-ve-selected";
            }  
            echo("<div class=\"friend-header-ve $currentChatClass\" onclick=\"changeChat('$myusername')\" style=\"float:left;width:31%;border:3px solid #e4f5f7;font-size:14px;padding:4px;margin-top:2px;margin-right:2px;margin-bottom:2px;text-align:left;cursor:pointer;\">&nbsp;&nbsp;$myusername</div>");
      endforeach; ?> 
<?php endif; ?>  
</td>
</tr>
</table>  

<div style="clear:both;"></div>
	
<div id="sidebar" style="clear:both; float:left; padding:8px; width:25%; max-width:250px; height:100%; text-align:center; border-right: 1px solid #2c2f34;">
    <?php if ($user!="MASTER"): ?>
    <br><br>
    <img src="/res/HMMgenius.png" alt="HMM Genius" title="HMM Genius" style="position:relative; left:+6px; width:90%; border: 1px dashed #EEEEEE;">
    <?php else: ?>
    <div style="text-align:left;">&nbsp;<?php echo(getResource("Friends", $curLocale));?></div><br>
    <div style="position:relative;top:-10px;left:+6px; width:90%; overflow-y:auto; height:244px; border: 1px dashed #EEEEEE;">
      <?php foreach($AUTH as $key => $val): 
              $myusername = $val['USERNAME'];
              $currentChatClass = PHP_STR;
              if ($myusername == $chatHint) {
                $currentChatClass = "friend-selected";
              }  
              echo("<div class=\"friend $currentChatClass\" onclick=\"changeChat('$myusername')\" style=\"padding:10px;text-align:left;font-size:14px;cursor:pointer;\">&nbsp;&nbsp;$myusername</div>");
            endforeach; ?> 
    </div>  
    <?php endif; ?>
    <div id="upload-cont"><input id="files" name="files[]" type="file" accept=".gif,.png,.jpg,.jpeg" style="visibility: hidden;" multiple></div>
    &nbsp;<br><br>
    <div style="text-align:left;white-space:nowrap;">
    &nbsp;&nbsp;<input type="password" id="Password" name="Password" placeholder="password" style="font-size:13px; background:#393939; color:#ffffff; width: 60%; border-radius:3px;" value="<?php echo($password);?>" autocomplete="off">&nbsp;<input type="submit" value="<?php echo(getResource("Go", $curLocale));?>" style="text-align:left;width:25%;"><br>
    &nbsp;&nbsp;<input type="text" id="Salt" placeholder="salt" style="position:relative; top:+5px; font-size:13px; background:#393939; color:#ffffff; width: 90%; border-radius:3px;" autocomplete="off"><br>
    <div style="text-align:center;">
    <a href="#" onclick="showEncodedPassword();" style="position:relative; left:-2px; top:+5px; color:#000000; font-size:12px;"><?php echo(getResource("Hash Me", $curLocale));?>!</a>     
    
    <br><br><br>
    
<audio id="mybeep" preload="auto">
  <source src="/media/R2D2-hey-you.mp3" type="audio/mpeg">
  Maybe doesn't support the audio..
</audio>  

<input type="button" id="myPlayButton" onclick="playmybeep()" value="<?php echo(getResource("Try the Beep", $curLocale));?>">

<script>
  function playmybeep() {
    document.getElementById("mybeep").volume = 1;
    document.getElementById("mybeep").play(); 
  }  
</script> 

    </div>
    </div>
</div>

<div id="messagebar" style="float:left; width:75%; max-width:950px; height:600px; padding:8px; border:0px solid red;">
	
	<?php if (APP_SPLASH): ?>
	<?php if ($hideSplash !== PHP_STR): ?>
	<div id="splash" style="border-radius:20px; position:relative; left:+3px; width:98%; background-color: #33aced; padding: 20px; margin-bottom:8px;">	
	
	   <button type="button" class="close" aria-label="Close" onclick="closeSplash();" style="position:relative; left:-10px;">
        <span aria-hidden="true">&times;</span>
     </button>
	
	   Hello and welcome to Homomm!<br><br>
	   
	   Homomm is a light and simple software on premise to exchange multimedia messages with friends.<br><br>
	   
	   Homomm is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.<br><br>
	   
     Homomm name comes from the two words: "homines" meaning our choise to give chance to the human beings to come first  
     and "mm" for "multimedia messaging".<br><br>
     
     Homomm doesn't want to be a replacement of Whats App, Telegram, Wechat, etc. but their alter ago.<br><br>
     
	   First step, use the left side panel password and salt fields to create the hash to insert in the config file for every user. Remember to manually set there also the salt value.<br><br>
	   
	   As you are going to run Homomm in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:<br>
	   <ol>
	   <li>Check the permissions of your "Repo" folder in your web app private path; and set its path in the config file.</li>
	   <li>In the Repo path create a "user" folder for each user and give to this folder the write permission. Set it appropriately in the config file.</li>
     <li>Check the permissions of your "hmm-img" folder in your web app public path; and set its path in the config file.</li>
     <li>In hmm-img path create a "user" folder for each user and give to this folder the write permission. Set it appropriately in the config file.</li>
	   <li>In the config file, set every "user" information appropriately like in the examples given.</li>
	   <li>Configure your <a href="http://twilio.com" style="color:#e6d236;">Twilio</a> account information appropriately to send out sms notification.</li>	      
     <li>Configure the server pushing interval to be notified on new chat messages.</li>
     <li>Configure the max history items as required (default: 50).</li>	      
	   </ol>
	   
	   <br>	
     
	   Hope you can enjoy it and let us know about any feedback: <a href="mailto:code@gaox.io" style="color:#e6d236;">code@gaox.io</a>
	   
	</div>	
	<?php endif; ?>
	<?php endif; ?>
	
	&nbsp;<?php echo(getResource("Message board", $curLocale));?>&nbsp;<a href="#" onclick="refresh();"><img src="/res/refresh.png" style="position:relative;top:+0px;"></a><br>
	<div id="Console" style="float:left; width:100%; height:288px; min-height:288px; overflow-y:auto; background:url('/res/console-bg.png'); background-size:cover; margin-top:10px; border:0px solid red;">
	<div id="Consolep" style="min-height:433px;margin-left:5px;padding:10px;border:0px solid green; color: #000000;">
<?php showHistory($msgHistory); ?>
  </div>	
  </div>
	<div id="Messagep" style="float:left; width:100%;min-height:105px;position:relative;top:-1px;margin-left:0px;padding:10px;padding-top:0px;border:0px solid red;background:url('/res/console-bg.png'); background-size:cover; color: #000000;">
<div id="MessageL" style="width:100%;position:relative;white-space:nowrap;top:-23px;border:0px solid black;"><div id="MessageK" style="float:left;width:93%;background:#FFFFFF;;white-space:nowrap;position:relative; top:+40px;border:0px solid red;"><textarea id="MessageLine" name="MessageLine" type="text" autocomplete="off" rows="3" placeholder="<?php echo(getResource("Message", $curLocale));?>" style="float:left;position:relative;top:+1px;width:75%;resize:none; background-color:white; color:black; border:0px; border-bottom: 1px dashed #EEEEEE;font-weight:900;"></textarea><div id="sendOptions" style="float:left;position:relative;top:+1px;left:+2px;background-color:#FFFFFF;width:105px;max-width:105px;height:59px;white-space:nowrap;padding:3px;font-weight:900;"><div id="pop-icons" style="float:left;text-align:center;margin:3px;margin-top:0px;width:30px;cursor:pointer;border:0px solid black;">&#128578;</div><div style="float:right;position:relative:top:-2px;border:0px solid blue;"><input type="checkbox" name="chkSMS" value="sms" style="font-size:10px;vertical-align:middle;">&nbsp;SMS&nbsp;</div><br><div onclick="upload();" style="float:right;position:relative;top:+5px;left:0px;cursor:pointer;border:0px solid red;"><img src="/res/upload.png" style="width:26px;"></div><div id="del-attach" onclick="clearUpload()" style="float:left; position:relative;top:-8px;left:-60px;display:none;cursor:pointer;"><img src="/res/del-attach.png" style="width:48px;"></div></div></div><div id="MessageS" style="float:left;width:7%;position:relative;top:+40px;cursor:pointer;border:0px solid green;" onclick="sendMessage()"><img src="/res/send.png" style="float:left;height:100%;width:63px;"></div></div>	
<div style="clear:both"></div>
<div id="emoticons" style="position:absolute; width: 130px; height:69px; background-color:#FFFFFF; border:1px solid black;display:none;">
  <?php foreach ($EMOTICONS as $key => $val): ?>
     <div style="float:left;width:30px;cursor:pointer;" onclick="insertEmotIcon('<?php echo($key);?>');"><?php echo($val);?></div>  
  <?php endforeach; ?>
</div>
<div style="clear:both"></div>
  </div>  
		
</div>

<input type="hidden" id="CommandLine" name="CommandLine">
<input type="hidden" id="chatHint" name="chatHint" value="<?php echo($chatHint); ?>">
<input type="hidden" name="hideSplash" value="<?php echo($hideSplash); ?>">
<input type="hidden" name="hideHCSplash" value="1">
<input type="hidden" name="msg-sign" value="<?php echo(mt_rand(1000000, 9999999)); ?>">     
<input type="hidden" id="last_message" value="<?php echo($lastMessage); ?>">

</form>

<div class="footer">
<div id="footerCont">&nbsp;</div>
<div id="footer"><span style="background:#FFFFFF;opacity:1.0;margin-right:10px;">&nbsp;&nbsp;A <a href="http://5mode.com">5 Mode</a> project <span class="no-sm">and <a href="http://demo.5mode.com">WYSIWYG</a> system</span>. Some rights reserved.</span></div>	
</div>

<script>
if (document.getElementsByClassName("friend-selected")[0]) {
  document.getElementsByClassName("friend-selected")[0].scrollIntoView();  
}  

function upload() {
 <?PHP if ($password!==PHP_STR): ?>
   $("input#files").click();
 <?PHP endif; ?>  
} 

function setPPlayer() {
  
  $("#picPlayer").css("height", parseInt(window.innerHeight)+"px");

  $("#myPicCont").css("height", parseInt(window.innerHeight)+"px");
  $("#myPicCont").css("max-width", parseInt(window.innerWidth)+"px");
  
  $("#closePlayer").css("left", "10px");
  $("#navPlayer1").css("top", parseInt((window.innerHeight-200)/2)+"px");
  $("#navPlayer2").css("top", parseInt((window.innerHeight-200)/2)+"px");
  $("#navPlayer2").css("left", parseInt(window.innerWidth-175)+"px");
  
  if (document.getElementById("myPic").src!="") {
    if ($("#myPic").width() > $("#myPic").height()) {
      f = $("#myPic").width() / $("#myPic").height();
      $("#myPic").css("padding-top", parseInt((window.innerHeight - $("#myPic").height()) / 2)+"px");
      $("#myPic").css("width", "100%"); //parseInt(window.innerWidth)+"px");
      $("#myPic").css("height", "");
      $("#myPic").css("max-height", parseInt(window.innerHeight)+"px");
    } else {
      $("#myPic").css("width", "");
      $("#myPic").css("max-width", parseInt(window.innerWidth)+"px");
      $("#myPic").css("height", "100%"); //parseInt(window.innerHeight)+"px");
      $("#myPicCont").css("max-width", parseInt(window.innerWidth)+"px");      
    }    
    $("#myPic").css("display", "inline");
  }  

  $(document.body).css("overflow-x","hidden");
}  

function hideTitle() {
  $("#myh1").hide("slow");
}

function startApp() {
  $("#HCsplash").hide("slow");
  $(document.body).css("background","#ffffff");
  $("#frmHC").show();
  
  <?php if (APP_SPLASH): ?>
  $(document.body).css("overflow-y","auto");    
  <?php endif; ?>
}			

<?php if($hideHCSplash!=="1"): ?>
window.addEventListener("load", function() {

  //$("#HCsplash").show();	  
  //setTimeout("startApp()", 5000);
  $(document.body).css("background","#000000");
  $("#HCsplash").show("slow");	  
  setTimeout("hideTitle()", 2000);
  setTimeout("startApp()", 4000);
  
}, true);
<?php else: ?>
window.addEventListener("load", function() {
    
  startApp();
  
});	
<?php endif; ?>

window.addEventListener("load", function() {
  <?php if ($hideHCSplash != "1" || $hidePlayer != "1"): ?>
  $(document.body).css("backgrond","#000000");
  <?php else: ?>
  $(document.body).css("backgrond","#FFFFFF");
  <?php endif; ?>
});

window.addEventListener("load", function() {		 
  <?php if($password===PHP_STR):?>
    $("#Password").addClass("emptyfield");
  <?php endif; ?>
  readyToType();
  document.getElementById("MessageLine").focus();
}, true);

window.addEventListener("load", function() {
  <?php if ($hidePlayer == "0"): ?>
  setPPlayer();
  <?php endif; ?>
  
  <?php if ($password != PHP_STR): ?>
  setInterval("checkServer()", <?php echo(APP_PUSH_INTERVAL);?>);
  <?PHP endif; ?>
}, true);

window.addEventListener("resize", function() {
  <?php if ($hidePlayer == "0"): ?>
  setPPlayer();
  <?php endif; ?>
}, true);

</script> 

</body>	 
</html>	 
