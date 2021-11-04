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
 * funct.inc
 * 
 * Homomm function file.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2024, 5 Mode     
 */


if (!function_exists("enableEmails")) { 
/**
 * Enable the links of the given text
 * 
 * @param string $text the text being parsed for links
 * @return the text with enabled links
 */  
function enableEmails(string $text, bool $masked = false): string 
{
  $callable_masked = function($aResults) {
    $result = implode(PHP_STR, $aResults); 
    return "<a href='mailto:$result'>" . mb_strrichr($result, "@", true) . "@.." . mb_strrichr($result, ".", false) . "</a>";
  };

  $callable_unmasked = function($aResults) {
    $result = implode(PHP_STR, $aResults); 
    return "<a href='mailto:$result'>$result</a>";
  };

  $regexPattern = "/(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}/i";
  
  if ($masked) {
    $callable = $callable_masked;
  } else {
    $callable = $callable_unmasked;
  } 
  
  return preg_replace_callback($regexPattern, $callable, $text);
}  
}

if (!function_exists("enableLinks")) { 
/**
 * Enable the links of the given text
 * 
 * @param string $text the text being parsed for links
 * @return the text with enabled links
 */
function enableLinks(string $text): string
{
  //return preg_replace("/(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,8})(\/?.+)?/", "<a href='\\0' target=\"_blank\">\\0</a>", $text);
  return preg_replace("/(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,8})(\/?[\da-zA-Z\-\?\/\&\#\=]+)?/i", "<a href='\\0' target=\"_blank\">\\0</a>", $text);
}  
}

if (!function_exists("date_add1")) {
 /**
  * Add the specified interval to the given date
  *
  * @param string $intervalType the type of interval to add to the string
  *               it can be one of the followin values: 
  *
  *               Y - Year
  *               m - Month
  *               W - Weeks 
  *               d - Day
  *               H - Hour
  *               i - Minute
  *               s - Second
  *
  * @param int $interval the interval to add (or subtract if negtive)
  * @param mixed $date the date to add the interval to
  * 
  * @return the resulting date otherwise false in case of error
  *
  * @see https://github.com/par7133/PHP-Classic-ASP-polyfills
  *
  */
  function date_add1(string $intervalType, int $interval, $date = null)
  {

     if (!isset($intervalType) || !isset($interval)) {
       return false;
     }
     
     // Parsing $date
     if (isset($date) && ($date instanceof DateTime)) {
       $d = $date;
     } else if (isset($date) && (is_string($date) && is_date($date))) { 
       $d = new DateTime($date);
     } else if (!isset($date)) {
       $d = new DateTime();
     } else {
       // any other case than..
       return false;      
     }   

     if ($interval === 0) {
       if (isset($d)) {
         return $d;
       } else {
         return false;
       }  
     }
     
     if ($interval > 0) {
     
       switch($intervalType)
       {
         case "Y":
           date_add($d, date_interval_create_from_date_string($interval . ' years'));
           break;
         case "m":
           date_add($d, date_interval_create_from_date_string($interval . ' months'));
           break;
         case "W":
           $days = $interval * 7;
           date_add($d, date_interval_create_from_date_string($days . ' days'));
           break;
         case "d":
           date_add($d, date_interval_create_from_date_string($interval . ' days'));
           break;
         case "H":
           date_add($d, date_interval_create_from_date_string($interval . ' hours'));
           break;
         case "i":
           date_add($d, date_interval_create_from_date_string($interval . ' minutes'));
           break;
         case "s":
           date_add($d, date_interval_create_from_date_string($interval . ' seconds'));
           break;
         default:
           return false;  
       }

     } else { 

       switch($intervalType)
       {
         case "Y":
           date_sub($d, date_interval_create_from_date_string(abs($interval) . ' years'));
           break;
         case "m":
           date_sub($d, date_interval_create_from_date_string(abs($interval) . ' months'));
           break;
         case "W":
           $days = $interval * 7;
           date_sub($d, date_interval_create_from_date_string(abs($days) . ' days'));
           break;
         case "d":
           date_sub($d, date_interval_create_from_date_string(abs($interval) . ' days'));
           break;
         case "H":
           date_sub($d, date_interval_create_from_date_string(abs($interval) . ' hours'));
           break;
         case "i":
           date_sub($d, date_interval_create_from_date_string(abs($interval) . ' minutes'));
           break;
         case "s":
           date_sub($d, date_interval_create_from_date_string(abs($interval) . ' seconds'));
           break;
         default:
           return false;  
       }

     }
     
     return $d;
  }
}

if (!function_exists("fixMultipleFileUpload")) { 
/**
 * Fix multiple file uploaded array ($_FILE)
 * 
 * @param array $f the $_FILE array
 * @return array the array fixed
 */
function fixMultipleFileUpload(&$f): array
{
    $files = array();
    $count = count($f['name']);
    $keys = array_keys($f);
   
    for($i=0;$i<$count;$i++)
    {
        foreach($keys as $key)
        {
            $files[$i][$key] = $f[$key][$i];
        }
    }
    return $files;
}
}
 
 
if (!function_exists("HTMLencode")) { 
/**
 * Encode any HTML of a given string
 * 
 * @param string $s the string to encode
 * @param bool $withBR keep the BR tag, true/false
 * @return string the string encoded
 */
 
$replacement_for_links1 = []; 
$i_replacement1 = -1;

$replacement_for_links2 = []; 
$i_replacement2 = -1;


function next_link_replacement1($matches)
{
  global $replacement_for_links1;
  global $i_replacement1;
  
  $i_replacement1++;
  
  //echo "*".$replacement_for_links1[$i_replacement1]."*";
    
  // as usual: $matches[0] is the complete match
  // $matches[1] the match for the first subpattern
  // enclosed in '(...)' and so on
  return $replacement_for_links1[$i_replacement1]??"";
}

function next_link_replacement2($matches)
{
  global $replacement_for_links2;
  global $i_replacement2;
  
  $i_replacement2++;
  
  //echo "@".$replacement_for_links2[$i_replacement2]."@";
    
  // as usual: $matches[0] is the complete match
  // $matches[1] the match for the first subpattern
  // enclosed in '(...)' and so on
  return $replacement_for_links2[$i_replacement2]??"";
}

 
function HTMLencode(?string $s, bool $withBR = false): string 
{
  global $replacement_for_links1;
  global $i_replacement1;

  global $replacement_for_links2;
  global $i_replacement2;
  
  $safe_for_links = [];
  
  if (!isset($s)) {
    return PHP_STR;
  }
    
  $s = str_ireplace("&#39;", "'", $s); 
  $s = str_ireplace("&#34;", "\"", $s);
  $s = str_ireplace("\\n", "", $s);
  
  preg_match_all("/(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,8})(\/?[\da-zA-Z\-\?\/\&\#\=]+)?/i", $s, $safe_for_links, PREG_SET_ORDER);
  
  //print_r($safe_for_links);
  
  $i=0;
  foreach($safe_for_links as $val) {
    $replacement_for_links1[] = "|LINK #".$i."|";
    $i++;  
  }  
  
  if ($safe_for_links) {
    $i_replacement1 = -1;
      
    //$s = preg_replace("/(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,8})(\/?[\da-zA-Z\-\?\/\&\#\=]+)?/i", "<a href='\\0' target=\"_blank\">\\0</a>", $text);
    $s = preg_replace_callback("/(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,8})(\/?[\da-zA-Z\-\?\/\&\#\=]+)?/i",
                               "next_link_replacement1",
                               $s); 

    $s = htmlspecialchars($s, ENT_QUOTES |ENT_IGNORE | ENT_HTML5, "UTF-8");

    $replacement_for_links2 = []; 

    $i=0;
    foreach($safe_for_links as $val) {
      $replacement_for_links2[] = $val[0];
      $i++;  
    }  

    $i_replacement2 = -1;

    $s = preg_replace_callback("/\|LINK \#\d+|\|/",
                               "next_link_replacement2",
                               $s); 
                               
  } else {
  
    $s = htmlspecialchars($s, ENT_QUOTES |ENT_IGNORE | ENT_HTML5, "UTF-8");
  
  }
  
  //$s = str_ireplace(";", PHP_STR, $s);
  //if (mb_stripos($s, ";") === false) {
  //  $s = str_ireplace("&", "|COMMERCIAL E|", $s);
  //}
  
  //if (mb_stripos($s, ";") === false) {
  //  $s = str_ireplace("|COMMERCIAL E|", "&", $s);
  //}
  
  if ($withBR) {
    $s = str_ireplace(chr(10), PHP_BR, $s);
  }  
    
  return $s;
} 
}

if (!function_exists("is_word")) {
/**
 * Determine if the given string is a word 
 * 
 * @param string $string the string being checked
 * @return bool true/false if it a word o not    
 */
 function is_word(string $string) {
	 
   if (($string === ".") || ($string === "..")) {
	  return false;
   }	  	 
	 
   return preg_match("/^[\w\-\.]+?$/", $string);	 
 }	 

}

if (!function_exists("left")) { 
/**
 * Left cut the given substring for the specified length 
 * 
 * @param string $string the string being cut on the left
 * @param int $length the length of the substring to return
 * @return string the resulting substring    
 */
function left(?string $string, int $length): string 
{
  if (!isset($string) || $string === PHP_STR) {
    return PHP_STR;
  }
  return mb_substr($string, 0, $length);
}
}

if (!function_exists("ltrim1")) { 
/**
 * Left trim the given chars for a string 
 * 
 * @param string $string the string being trimmed
 * @param string $chars the chars to trim off
 * @return string the resulting string    
 */
function ltrim1(string $string, string $chars): string
{
  if (!isset($string) || $string === PHP_STR) {
    return PHP_STR;
  }
  if (!isset($chars) || $chars === PHP_STR) {
    return $string;
  }
  $len = strlen($chars); 
  $retval = $string;
  for ($y=1;$y<=$len;$y++) {
    for ($z=0;$z<=$len-1;$z++) {
      $retval = ltrim($retval,substr($chars,$z,1));
    }   
  }
  return $retval;
}
}

if (!function_exists("rtrim1")) {
/**
 * Right trim the given chars for a string 
 * 
 * @param string $string the string being trimmed
 * @param string $chars the chars to trim off
 * @return string the resulting string    
 */
function rtrim1(string $string, string $chars): string
{
  if (!isset($string) || $string === PHP_STR) {
    return PHP_STR;
  }
  if (!isset($chars) || $chars === PHP_STR) {
    return $string;
  }
  $len = strlen($chars); 
  $retval = $string;
  for ($y=1;$y<=$len;$y++) {
    for ($z=0;$z<=$len-1;$z++) {
      $retval = rtrim($retval,substr($chars,$z,1));
    }   
  }
  return $retval;
}
}

if (!function_exists("right")) {
/**
 * Right cut the given string for the specified length 
 * 
 * @param string $string the string being cut on the right
 * @param int $length the length of the substring to return
 * @return string the resulting substring    
 */
function right(?string $string, int $length): string 
{
  if (!isset($string) || $string === PHP_STR) {
    return PHP_STR;
  }  
  return mb_substr($string, mb_strlen($string) - $length);
}
}

if (!function_exists("sendSMS")) {
/**
 * Send out an SMS using Twilio service (you need to be registred on Twilio) 
 * 
 * @param array $post_body the message to send out in the following format:
 *
 *      $message = array(
 *        'To'=>"+393312345678", 
 *        'MessagingServiceSid'=>"You find the MessagingServiceSid on Twilio", 
 *        'Body'=>"Your Message"
 *      );
 *
 * @param string $url the Twilio api $url
 * @param string $username your Twilio account SID
 * @param string $pasword your Twilio auth token
 * @return array the server generated response    
 */
function sendSMS($post_body, string $url, string $username, string $password) {
  $ch = curl_init( );
  //$headers = array(
  //  'Content-Type:multipart/form-data'
  //  'Authorization:Basic '. base64_encode("$username:$password")
  //);
  //curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
  curl_setopt ( $ch, CURLOPT_URL, $url );
  curl_setopt ( $ch, CURLOPT_POST, 1 );
  curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_body );
  curl_setopt ( $ch, CURLOPT_USERPWD, "$username:$password" );
  // Allow cUrl functions 20 seconds to execute
  curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
  // Wait 10 seconds while trying to connect
  curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
  $output = array();
  $output['server_response'] = curl_exec( $ch );
  $curl_info = curl_getinfo( $ch );
  $output['http_status'] = $curl_info[ 'http_code' ];
  $output['error'] = curl_error($ch);
  curl_close( $ch );
  return $output;
}
}
