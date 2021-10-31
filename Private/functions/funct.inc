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

  $regexPattern = "/(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}/";
  
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
  return preg_replace("/(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,8})(\/?.+)?$/", "<a href='\\0' target=\"_blank\">\\0</a>", $text);
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
function HTMLencode(?string $s, bool $withBR = false): string 
{
  if (!isset($s)) {
    return PHP_STR;
  }
    
  $s = str_ireplace("&#39;", "'", $s); 
  $s = str_ireplace("&#34;", "\"", $s);
  $s = str_ireplace("\\n", "", $s);
  $s = htmlspecialchars($s, ENT_QUOTES |ENT_IGNORE | ENT_HTML5, "UTF-8");
    
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
