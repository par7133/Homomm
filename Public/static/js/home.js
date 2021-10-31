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
 * home.js
 * 
 * JS file for Home page
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2024, the Open Gallery's contributors     
 */

$(document).ready(function() {
 $("#Password").on("keydown",function(e){
   key = e.which;
   //alert(key);
   $("#userHint").val("");
   if (key===13) {
   e.preventDefault();
   frmHC.submit();
   } else { 
   //e.preventDefault();
   }
 });

 $("#MessageLine").on("keydown",function(e){
   key = e.which;
   //alert(key);
   if (key===13) {
     //e.preventDefault();
     //sendMessage()
   } else { 
     //e.preventDefault();
   }
 });
});

function changeChat(user) {
 $("#userHint").val(user);
 frmHC.submit();
}

function closeSplash() {
  $("#hideSplash").val("1");
  $("#splash").hide();	
}

/**
 * Encrypt the given string
 * 
 * @param {string} string - The string to encrypt
 * @returns {string} the encrypted string
 */
function encryptSha2(string) {
  var jsSHAo = new jsSHA("SHA-256", "TEXT", 1);
  jsSHAo.update(string);
  return jsSHAo.getHash("HEX");
}

function refresh() {
 $("#CommandLine").val("refreshbrd");
 frmHC.submit();
}

function sendMessage() {
 //if ($("#MessageLine").val()==="") {
 //  alert("First, write your message!");
 //  return; 
 //}
 $("#CommandLine").val("sendmsg");
 frmHC.submit();
}

function setContentPos() {
  if (window.innerWidth<900) {
    $("#MessageL").css("width","97%");
    $("#MessageK").css("width","89%");  
  } else {
    $("#MessageL").css("width","100%");
    $("#MessageK").css("width","93%");  
  }    
}  

function setFooterPos() {
  if (document.getElementById("footerCont")) {
	if ($("#Password").val() === "") {  
      tollerance = 48;
    } else {
	  tollerance = 15;
	}  	  
    $("#footerCont").css("top", parseInt( window.innerHeight - $("#footerCont").height() - tollerance ) + "px");
    $("#footer").css("top", parseInt( window.innerHeight - $("#footer").height() - tollerance ) + "px");
  }
}

function showEncodedPassword() {
   if ($("#Password").val() === "") {
	 $("#Password").addClass("emptyfield");
	 return;  
   }
   if ($("#Salt").val() === "") {
	 $("#Salt").addClass("emptyfield");
	 return;  
   }	   	
   passw = encryptSha2( $("#Password").val() + $("#Salt").val());
   msg = "Please set your hash in the config file with this value:";
   alert(msg + "\n\n" + passw);	
}

$("input#files").on("change", function(e) {
  
  if (!document.getElementById("files").files) {
    $("#del-attach").css("display", "none");
  } else {  
    $("#del-attach").css("display", "inline");
  }  
  //frmHC.submit();
});

function clearUpload() {
  $("#upload-cont").html("<input id='files' name='files[]' type='file' accept='.gif,.png,.jpg,.jpeg' style='visibility: hidden;'>"); 
  $("#del-attach").css("display", "none");
}  

$("#Password").on("keydown", function(e){
	$("#Password").removeClass("emptyfield");
});	

$("#Salt").on("keydown", function(e){
	$("#Salt").removeClass("emptyfield");
});	

window.addEventListener("load", function() {
  
  setTimeout("setContentPos()", 1000);  
  setTimeout("setFooterPos()", 3000);
  
}, true);

window.addEventListener("resize", function() {

  setTimeout("setContentPos()", 1000);  
  setTimeout("setFooterPos()", 3000);

}, true);


