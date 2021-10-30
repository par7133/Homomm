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
 $userHint = PHP_STR;
 $userHintResolved = PHP_STR;
 $picPath = PHP_STR;
 

 function showHistory() {
   global $msgHistory;
   global $user;
   global $curPath;
   global $picPath;
   
   $i = 1;	 
   //echo "curPath=$curPath<br>"; 
   $oldDate = "";
   foreach($msgHistory as $val) {
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
     // grab the date
     $date = left($val, 8);
     $date = date("l j F", mktime(0,0,0,substr($date,4,2),right($date,2),left($date,4))); 
     if ($date!=$oldDate) {
       echo("<div style='text-align:center;'><span style='background-color:gray;color:#FFFFFF'>$date</span></div><br>");  
       $oldDate = $date;
     }  
     // grab the time
     preg_match('/^.+-(\d{4})-/i', $val, $matches);
     $time = $matches[1];
     $time = ltrim(left($time,2),"0") . ":" . right($time,2);
     // parsing for file ext
     $fileext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
     if ($fileext === "png" || $fileext === "jpg" || $fileext === "jpeg" || $fileext === "gif") {
       // display the img
       $img = substr($picPath, strlen(APP_PATH)) . DIRECTORY_SEPARATOR . $val; 
       echo("<div style='background-color:$bgcolor;float:$float;padding:5px;max-width:300px;min-width:260px;border-radius:2px;'><img src='$img' style='width:100%;'><div style='float:right;font-size:9px;'>$time</div></div><br><br><br>");
     } else {  
       // display the msg
       $msg = HTMLencode(file_get_contents($curPath . DIRECTORY_SEPARATOR . "msgs" . DIRECTORY_SEPARATOR . $val));
       echo("<div style='background-color:$bgcolor;float:$float;padding:5px;max-width:300px;min-width:260px;border-radius:2px;white-space:normal;'>".str_replace("\n", "<br>", $msg)."<div style='float:right;font-size:9px;'>$time</div></div><br><br><br>");
     }	   
     echo("<div style='clear:both;'></div>");
     echo("</div>");

	   $i++;   
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
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param1 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param1 = $str;
	 return;
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

   //if (trim($message,"\n")!==PHP_STR) {
   //  myExecSendMessage();  
   //}  

   //if (!empty($_FILES['files'])) {
   if (!empty($_FILES['files']['tmp_name'][0])) {
	   
     $uploads = (array)fixMultipleFileUpload($_FILES['files']);
     
     //no file uploaded
     if ($uploads[0]['error'] === PHP_UPLOAD_ERR_NO_FILE) {
       //updateHistoryWithErr("No file uploaded.", false);
       return;
     } 
 
     foreach($uploads as &$upload) {
		
       switch ($upload['error']) {
       case PHP_UPLOAD_ERR_OK:
         break;
       case PHP_UPLOAD_ERR_NO_FILE:
         //updateHistoryWithErr("One or more uploaded files are missing.", false);
         return;
       case PHP_UPLOAD_ERR_INI_SIZE:
         //updateHistoryWithErr("File exceeded INI size limit.", false);
         return;
       case PHP_UPLOAD_ERR_FORM_SIZE:
         //updateHistoryWithErr("File exceeded form size limit.", false);
         return;
       case PHP_UPLOAD_ERR_PARTIAL:
         //updateHistoryWithErr("File only partially uploaded.", false);
         return;
       case PHP_UPLOAD_ERR_NO_TMP_DIR:
         //updateHistoryWithErr("TMP dir doesn't exist.", false);
         return;
       case PHP_UPLOAD_ERR_CANT_WRITE:
         //updateHistoryWithErr("Failed to write to the disk.", false);
         return;
       case PHP_UPLOAD_ERR_EXTENSION:
         //updateHistoryWithErr("A PHP extension stopped the file upload.", false);
         return;
       default:
         //updateHistoryWithErr("Unexpected error happened.", false);
         return;
       }
      
       if (!is_uploaded_file($upload['tmp_name'])) {
         //updateHistoryWithErr("One or more file have not been uploaded.", false);
         return;
       }
      
       // name	 
       $name = (string)substr((string)filter_var($upload['name']), 0, 255);
       if ($name == PHP_STR) {
         //updateHistoryWithErr("Invalid file name: " . $name, false);
         return;
       } 
       $upload['name'] = $name;
       
       // fileType
       $fileType = substr((string)filter_var($upload['type']), 0, 30);
       $upload['type'] = $fileType;	 
       
       // tmp_name
       $tmp_name = substr((string)filter_var($upload['tmp_name']), 0, 300);
       if ($tmp_name == PHP_STR || !file_exists($tmp_name)) {
         //updateHistoryWithErr("Invalid file temp path: " . $tmp_name, false);
         return;
       } 
       $upload['tmp_name'] = $tmp_name;
       
       //size
       $size = substr((string)filter_var($upload['size'], FILTER_SANITIZE_NUMBER_INT), 0, 12);
       if ($size == "") {
         //updateHistoryWithErr("Invalid file size.", false);
         return;
       } 
       $upload["size"] = $size;

       $tmpFullPath = $upload["tmp_name"];
       
       $originalFilename = pathinfo($name, PATHINFO_FILENAME);
       $originalFileExt = pathinfo($name, PATHINFO_EXTENSION);
       $fileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));
       
       if ($originalFileExt!==PHP_STR) {
         //$destFileName = $originalFilename . "." . $fileExt;
         if ($user === "master") {
           $destFileName = date("Ymd-Hm") . "-" . mt_rand(100000, 999999) . "-master.$fileExt";
         } else {
           $destFileName = date("Ymd-Hm") . "-" . mt_rand(100000, 999999) . "-$userName.$fileExt";
         }  
       } else {
         return; 
       }	   
       $destFullPath = $picPath . DIRECTORY_SEPARATOR . $destFileName;
       
       if (file_exists($destFullPath)) {
         //updateHistoryWithErr("destination already exists", false);
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
    global $userHintResolved; 
     
    if (!empty($message)) {
    
      if ($user == "MASTER") {
        $fileName = date("Ymd-Hm") . "-" . mt_rand(100000, 999999) . "-master.msg";
      } else {
        $fileName = date("Ymd-Hm") . "-" . mt_rand(100000, 999999) . "-$userName.msg";
      }  
 
      $msg = HTMLencode($message);
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
        $smsUser = $userHintResolved; 
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
  
  
 $password = filter_input(INPUT_POST, "Password");
 $command = filter_input(INPUT_POST, "CommandLine");
 $message = filter_input(INPUT_POST, "MessageLine");
 $sendSMS1 = filter_input(INPUT_POST, "chkSMS");
 if ($sendSMS1!="") {
   $sendSMS = true;
 } else {
   $sendSMS = false;
 }    
 $pwd = PHP_STR;
 
 $userHint = filter_input(INPUT_POST, "userHint"); 
 
 $userHintResolved = PHP_STR;
 if ($userHint!=PHP_STR) {
   $found=false;
   foreach ($CONFIG['AUTH'] as $key => $val) {
     if ($userHint==$val['USERNAME']) {
       $userHintResolved = $key;
       $found=true;
       break;
     }      
   }
   if (!$found) {
     die("Invalid chat!"); 
   }  
 }
  
 $hideSplash = filter_input(INPUT_POST, "hideSplash");
 $hideHCSplash = filter_input(INPUT_POST, "hideHCSplash");

 //echo "password=*$password*<br>";
 if ($password != PHP_STR) {	
	$hash = hash("sha256", $password . APP_SALT, false);
  
  $found=false;
  foreach ($CONFIG['AUTH'] as $key => $val) {
    //echo ("username=".$val['USERNAME']."<br>");
    if ($hash==$val['HASH']) {
      $user = $key;
      if ($userHintResolved==PHP_STR) {
        $userHintResolved = $key;
      }  
      $found=true;
      break;
    }    
  }  
  if (!$found) {
    $password=PHP_STR; 
  }  
  
  if ($password != PHP_STR) {
    $userName = $CONFIG['AUTH'][$user]['USERNAME'];
    $pwd = APP_REPO_PATH . DIRECTORY_SEPARATOR . $CONFIG['AUTH'][$userHintResolved]['REPO_FOLDER'];
    $picPath =  APP_PIC_PATH . DIRECTORY_SEPARATOR . $CONFIG['AUTH'][$userHintResolved]['PIC_FOLDER'];
  }   
 } 
 
 $curPath = APP_REPO_PATH;
 if ($pwd!==PHP_STR) {
   if (left($pwd, strlen(APP_REPO_PATH)) === APP_REPO_PATH) {
     $curPath = $pwd;
     chdir($curPath);
     
     if (!file_exists($curPath . DIRECTORY_SEPARATOR . ".HMM_history")) {
       $output = [];
       file_put_contents($curPath . DIRECTORY_SEPARATOR . ".HMM_history", $output);
     }

     if (!file_exists($curPath . DIRECTORY_SEPARATOR . "msgs")) {
       mkdir("msgs", 0777);
     }
   }	    
 }	 
 $ipos = strripos($curPath, PHP_SLASH);
 $curDir = substr($curPath, $ipos);
 
 
 if ($password !== PHP_STR) {
   
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
     }
  
   } else if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $cmd . "|")) {
       
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
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
<head>
	
  <meta charset="UTF-8"/>
  <meta name="style" content="day1"/>
  
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
	
  <link rel="shortcut icon" href="./res/favicon.ico?v=<?php echo(time()); ?>" />
    
  <meta name="description" content="Welcome to <?php echo(APP_NAME); ?>"/>
  <meta name="author" content="5 Mode"/> 
  <meta name="robots" content="noindex"/>
  
  <script src="./js/jquery-3.1.0.min.js" type="text/javascript"></script>
  <script src="./js/common.js" type="text/javascript"></script>
  <script src="./js/bootstrap.min.js" type="text/javascript"></script>
  <script src="./js/sha.js" type="text/javascript"></script>
  
  <script src="./js/home.js" type="text/javascript" defer></script>
  
  <link href="./css/bootstrap.min.css" type="text/css" rel="stylesheet">
  <link href="./css/style.css?v=<?php echo(time()); ?>" type="text/css" rel="stylesheet">
     
  <script>
	
   function upload() {
   <?PHP if ($password!==PHP_STR): ?>
     $("input#files").click();
   <?PHP endif; ?>  
   } 
    
   window.addEventListener("load", function() {		 
		 <?php if($password===PHP_STR):?>
		    $("#Password").addClass("emptyfield");
		 <?php endif; ?>
     maxY = document.getElementById("Console").scrollHeight;
     //alert(maxY);
     document.getElementById("MessageLine").focus();
     document.getElementById("Console").scrollTop=maxY;
	 }, true);

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

  </script>    
    
</head>
<body>

<div id="HCsplash" style="padding-top: 160px; text-align:center;color:#ffffff;display:none;">
   <div id="myh1"><H1>Homomm</H1></div><br>
   <img src="./Public/static/res/HMMlogo2.png" style="width:310px;">
</div>

<form id="frmHC" method="POST" action="/" target="_self" enctype="multipart/form-data" style="display:<?php echo(($hideHCSplash==="1"?"inline":"none"));?>;">

<div class="header">
   <a href="http://homomm.org" target="_blank" style="color:black; text-decoration: none;"><img src="/res/HMMlogo2.png" style="width:48px;">&nbsp;Homomm</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://github.com/par7133/Homomm" style="color:#000000"><span style="color:#119fe2">on</span> github</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="mailto:info@homomm.com" style="color:#000000"><span style="color:#119fe2">for</span> feedback</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="tel:+39-331-4029415" style="font-size:13px;background-color:#15c60b;border:2px solid #15c60b;color:black;height:27px;text-decoration:none;">&nbsp;&nbsp;get support&nbsp;&nbsp;</a>
</div>
	
<div style="clear:both; float:left; padding:8px; width:25%; max-width:250px; height:100%; text-align:center;">
    <?php if ($user!="MASTER"): ?>
    <br><br>
    <img src="/res/HMMgenius.png" alt="HC Genius" title="HC Genius" style="position:relative; left:+6px; width:90%; border: 1px dashed #EEEEEE;">
    <?php else: ?>
    <div style="text-align:left;">&nbsp;Friends</div><br>
    <div style="position:relative;top:-10px;left:+6px; width:90%; overflow-y:auto; height:244px; border: 1px dashed #EEEEEE;">
      <?php foreach($CONFIG['AUTH'] as $key => $val): 
              $myusername = $val['USERNAME'];
              echo("<div class=\"friend\" onclick=\"changeChat('$myusername')\" style=\"text-align:left;cursor:pointer;\">&nbsp;&nbsp;$myusername</div>");
            endforeach; ?> 
    </div>  
    <?php endif; ?>
    <div id="upload-cont"><input id="files" name="files[]" type="file" accept=".gif,.png,.jpg,.jpeg" style="visibility: hidden;"></div>
    &nbsp;<br><br>
    <div style="text-align:left;">
    &nbsp;&nbsp;<input type="text" id="Password" name="Password" placeholder="password" style="font-size:10px; background:#393939; color:#ffffff; width: 70%; border-radius:3px;" value="<?php echo($password);?>" autocomplete="off">&nbsp;<input type="submit" value="Go" style="width:45px;"><br>
    &nbsp;&nbsp;<input type="text" id="Salt" placeholder="salt" style="position:relative; top:+5px; font-size:10px; background:#393939; color:#ffffff; width: 70%; border-radius:3px;" autocomplete="off"><br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="showEncodedPassword();" style="position:relative; left:-2px; top:+5px; color:#000000; font-size:12px;">Hash Me!</a>     
    </div>
</div>

<div style="float:left; width:75%; max-width:950px; height:600px; padding:8px; border-left: 1px solid #2c2f34;">
	
	<?php if (APP_SPLASH): ?>
	<?php if ($hideSplash !== PHP_STR): ?>
	<div id="splash" style="border-radius:20px; position:relative; left:+3px; width:98%; background-color: #33aced; padding: 20px; margin-bottom:8px;">	
	
	   <button type="button" class="close" aria-label="Close" onclick="closeSplash();" style="position:relative; left:-10px;">
        <span aria-hidden="true">&times;</span>
     </button>
	
	   Hello and welcome to Homomm!<br><br>
	   
	   Homomm is a light and simple software on premise to exchange multimedia messages with friends.<br><br>
	   
	   Homomm is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.<br><br>
	   
     Homomm name comes from the two words, "homines" meaning our choise to give chance to the human beings to come first, 
     and "mm" for "multimedia messaging".<br><br>
     
     Homomm doesn't want to be a replacement of Whats App, Telegram, Wechat, etc. but simply wants to be their alter ago.<br><br>
     
	   First step, use the left side panel password and salt fields to create the hash to insert in the config file for every user. Remember to manually set there also the salt value.<br><br>
	   
	   As you are going to run Homomm in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:<br>
	   <ol>
	   <li>Check the permissions of your "Repo" folder in your web app private path; and set its path in the config file.</li>
	   <li>In the Repo path create a "user" folder for each user and give to this folder the write permission. Set it appropriately in the config file.</li>
     <li>Check the permissions of your "hmm-img" folder in your web app public path; and set its path in the config file.</li>
     <li>In hmm-img path create a "user" folder for each user and give to this folder the write permission. Set it appropriately in the config file.</li>
	   <li>In the config file, set every "user" information appropriately like in the examples given.</li>
	   <li>Configure your <a href="http://twilio.com" style="color:#e6d236;">Twilio</a> account information appropriately to send out sms notification.</li>	      
     <li>Configure the max history items as required (default: 50).</li>	      
	   </ol>
	   
	   <br>	
     
	   Hope you can enjoy it and let us know about any feedback: <a href="mailto:info@homomm.org" style="color:#e6d236;">info@homomm.org</a>
	   
	</div>	
	<?php endif; ?>
	<?php endif; ?>
	
	&nbsp;Message board<br>
	<div id="Console" style="height:433px; overflow-y:auto; margin-top:10px;">
  <!--<div id="Console" style="height:493px; margin-top:10px;">-->
	<pre id="Consolep" style="min-height:433px;margin-left:5px;padding:10px;border:0px;background:url('/res/console-bg.png'); background-size:cover; color: #000000;">
<?php showHistory($msgHistory); ?>
<div style="clear:both"></div>
	</pre>	
  </div>
	<pre id="Messagep" style="position:relative;top:-10px;margin-left:5px;padding:10px;padding-top:0px;border:0px;background:url('/res/console-bg.png'); background-size:cover; color: #000000;">
<div id="MessageL" style="width:100%;position:relative;white-space:nowrap;top:-23px;border:0px solid black;"><div id="MessageK" style="float:left;width:93%;background:url('/res/send-opts-bg.png');white-space:nowrap;position:relative; top:+40px;border:0px solid black;"><textarea id="MessageLine" name="MessageLine" type="text" autocomplete="off" rows="3" placeholder="Message" style="float:left;position:relative;top:+1px;width:80%;resize:none; background-color:white; color:black; border:0px; border-bottom: 1px dashed #EEEEEE;font-weight:900;"></textarea><div id="sendOptions" style="float:left;position:relative;top:-1px;width:16%;min-width:50px;height:59px;white-space:nowrap;padding:3px;font-weight:900;"><input type="checkbox" name="chkSMS" value="sms">&nbsp;SMS&nbsp;<br><div onclick="upload();" style="position:relative;top:+5px;left:+5px;cursor:pointer;"><img src="/res/upload.png" style="width:32px;"></div><div id="del-attach" onclick="clearUpload()" style="position:relative;top:-48px;left:-60px;display:none;cursor:pointer;"><img src="/res/del-attach.png" style="width:64px;"></div></div></div><div style="float:left;width:7%;position:relative;top:+40px;cursor:pointer;" onclick="sendMessage()"><img src="/res/send.png" style="float:left;width:63px;"></div></div>	
<div style="clear:both"></div>
  </pre>  
		
</div>

<div class="footer">
<div id="footerCont">&nbsp;</div>
<div id="footer"><span style="background:#FFFFFF;opacity:1.0;margin-right:10px;">&nbsp;&nbsp;A <a href="http://5mode.com">5 Mode</a> project and <a href="http://wysiwyg.systems">WYSIWYG</a> system. Some rights reserved.</span></div>	
</div>

<input type="hidden" id="CommandLine" name="CommandLine">
<input type="hidden" name="pwd" value="<?php echo($curPath); ?>" style="color:black">
<input type="hidden" id="userHint" name="userHint" value="<?php echo($userHint); ?>">
<input type="hidden" name="hideSplash" value="<?php echo($hideSplash); ?>">
<input type="hidden" name="hideHCSplash" value="1">

</form>

</body>	 
</html>	 
