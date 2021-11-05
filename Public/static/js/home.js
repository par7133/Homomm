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

var bBurgerMenuVisible = false;
var bIconsVisible = false;

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

 $("#Password2").on("keydown",function(e){
   key = e.which;
   //alert(key);
   $("#userHint").val("");
   if (key===13) {
   e.preventDefault();
   $("#Password").val("");
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

$("#burger-menu").on("click",function(){
  if (!bBurgerMenuVisible) {
    $(".friend-header").css("display", "table");
  } else {
    $(".friend-header").css("display", "none");
  }    
  bBurgerMenuVisible=!bBurgerMenuVisible;  
});

function hideBurgerMenu() {
  $(".friend-header").css("display", "none");
  bBurgerMenuVisible=false;  
}

function changeChat(user) {
 $("#userHint").val(user);
 frmHC.submit();
}

$("#pop-icons").on("click", function() {
  if (!bIconsVisible) {
    tollerance = 450 / window.scrollY;
    msgLineRect = document.getElementById("MessageLine").getBoundingClientRect(); 
    //mytop = parseInt(msgLineRect.top - window.scrollY - 350 - tollerance) + "px";
    left = parseInt(msgLineRect.left + msgLineRect.width - 260) + "px";
    if (window.innerWidth < 650) {
      left = parseInt(msgLineRect.left + msgLineRect.width - 0) + "px";
    }  
    //$("#emoticons").css("top", mytop);
    $("#emoticons").css("left", left);
    $("#emoticons").css("display", "inline");
    $("#emoticons").css("z-index", "99999");
  } else {
    $("#emoticons").css("display", "none");
  }
  bIconsVisible = !bIconsVisible;
});

function hideIcons() {
  $("#emoticons").css("display", "none");
  IconsVisible = false;
}  

function insertEmotIcon(icon) {
  $("#MessageLine").val($("#MessageLine").val() + icon);  
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

function closePlayer() {
  refresh();
}

function sendMessage() {
 //if ($("#MessageLine").val()==="") {
 //  alert("First, write your message!");
 //  return; 
 //}
  $("#CommandLine").val("sendmsg");
  frmHC.submit();
}

function deletePic(pic) {
  $("#CommandLine").val("delpic " + pic);
  frmHC.submit();  
}  

function deleteMsg(msg) {
  $("#CommandLine").val("delmsg " + msg);
  frmHC.submit();  
}  

function openPic(pic) {
  $("#CommandLine").val("openpic " + pic)
  frmHC.submit();
}

function setContentPos() {
  if (window.innerWidth<650) {
    $("#ahome").attr("href","/");
    $("#agithub").css("display","none");
    $("#afeedback").css("display","none");
    $("#asupport").css("display","none");
    $("#pwd2").css("display","inline");    
    $("#sidebar").css("display","none");
    $("#burger-menu").css("display","inline");
    $("#messagebar").css("width","100%");
    $("#logo-hmm").css("display","none");
  } else {  
    $("#ahome").attr("href","http://homomm.org");
    $("#agithub").css("display","inline");
    $("#afeedback").css("display","inline");
    $("#asupport").css("display","inline");  
    $("#pwd2").css("display","none");
    $("#sidebar").css("display","inline");
    $("#burger-menu").css("display","none");
    $("#messagebar").css("width","75%");
    $("#logo-hmm").css("display","inline");
  }
  hideBurgerMenu();
  hideIcons();
  if (window.innerWidth<900) {
    $("#MessageL").css("width","97%");
    $("#MessageK").css("width","89%");
    //$("#del-attach").css("top","-42px");  
  } else {
    $("#MessageL").css("width","100%");
    $("#MessageK").css("width","93%");  
    //$("#del-attach").css("top","-34px");
  }    
  if (window.innerWidth<650) {
    $("#MessageL").css("width", parseInt(window.innerWidth-65) + "px");
  } else {
    consoleRect=document.getElementById("Console").getBoundingClientRect();
    $("#MessageL").css("width", parseInt(consoleRect.width-33) + "px");   
  }    
  newConsoleHeight = parseInt(window.innerHeight-250);
  if (newConsoleHeight>288) {
    $("#Console").css("height", newConsoleHeight + "px");
  }  
  //$("#Messagep").css("top", (newConsoleHeight - 433) + "px");
  msgKrect=document.getElementById("MessageK").getBoundingClientRect();
  msgLineRect=document.getElementById("MessageLine").getBoundingClientRect();
  $("#MessageS").css("height",parseInt(msgLineRect.height));
  $("#MessageS").css("max-height",parseInt(msgLineRect.height));
  $("#MessageLine").css("width", parseInt(msgKrect.width - 115) + "px");
  window.scroll(0, 0);
  $(document.body).css("overflow-y", "hidden");
}  

function setFooterPos() {
  //if (document.getElementById("footerCont")) {
	//if ($("#Password").val() === "") {  
  //  tollerance = 48;
  //} else {
	  tollerance = 15;
	//}  	  
  
    if (window.innerWidth<450) {
      $(".no-sm").css("display", "none");
    } else {
      $(".no-sm").css("display", "inline");
    }
    
    $("#footerCont").css("top", parseInt( window.innerHeight - $("#footerCont").height() - tollerance ) + "px");
    $("#footer").css("top", parseInt( window.innerHeight - $("#footer").height() - tollerance ) + "px");
  //}
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
  $("#upload-cont").html("<input id='files' name='files[]' type='file' accept='.gif,.png,.jpg,.jpeg' style='visibility: hidden;' multiple>"); 
  $("#del-attach").css("display", "none");
}  

$("#Password").on("keydown", function(e){
	$("#Password").removeClass("emptyfield");
});	

$("#Salt").on("keydown", function(e){
	$("#Salt").removeClass("emptyfield");
});	

window.addEventListener("load", function() {
  
  if ($("#frmHC").css("display")==="none") {
    setTimeout("setContentPos()", 5200);  
    setTimeout("setFooterPos()", 5300);
  } else {
    setTimeout("setContentPos()", 1000);
    setTimeout("setFooterPos()", 3000);
  }      
  
}, true);

window.addEventListener("resize", function() {

  if ($("#frmHC").css("display")==="none") {
    setTimeout("setContentPos()", 5200);
    setTimeout("setFooterPos()", 5300);  
  } else {
    setTimeout("setContentPos()", 1000);
    setTimeout("setFooterPos()", 3000);
  }      

}, true);


