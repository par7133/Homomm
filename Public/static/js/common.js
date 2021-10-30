/**
 * Copyright 2021, 2024 5 Mode
 *
 * This file is part of Http Console.
 *
 * Http Console is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Http Console is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.  
 * 
 * You should have received a copy of the GNU General Public License
 * along with Http Console. If not, see <https://www.gnu.org/licenses/>.
 *
 * commons.js
 * 
 * Common constants and functions
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2024, the Open Gallery's contributors     
 */


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
