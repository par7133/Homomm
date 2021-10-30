/**
 * Copyright (c) 2016, 2024, the Open Gallery's contributors
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither Open Gallery nor the names of its contributors 
 *       may be used to endorse or promote products derived from this software 
 *       without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * home.js
 * 
 * JS file for Home page
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2016, 2024, the Open Gallery's contributors     
 * @license https://opensource.org/licenses/BSD-3-Clause 
 */

var burgerMenuVisible=false;
var onAppMenu=false;

function popupMenu() {
  if (!burgerMenuVisible) {
    $(".appMenu").show();
    $(".appMenu").css("z-index", "99999");
  } else {
    $(".appMenu").hide();
    $(".appMenu").css("z-index", "99992");
  }
  burgerMenuVisible=!burgerMenuVisible;
} 

function hideMenu() {
  $(".appMenu").hide();
  burgerMenuVisible=false;
} 

$("#burgerMenuIco").on("mouseover", function() {
    onAppMenu = true;
});

$("#burgerMenuIco").on("mouseout", function() {
    onAppMenu = false;
});

$(".appMenu").on("mouseover", function() {
    onAppMenu = true;
});

$(".appMenu").on("mouseout", function() {
    onAppMenu = false;
});

$("body").on("click", function() {
  if (!onAppMenu) {
    hideMenu();
  }
});

window.addEventListener("load", function() {
  $("div.appMenu").load("https://appmenu.5mode.com/?v="+ rnd(50000, 99999));
}, true);

window.addEventListener("resize", function() {
  hideMenu();
}, true);


