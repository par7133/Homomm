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
 * init.inc
 * 
 * Homomm initialization file.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2024, 5 Mode     
 */
 
error_reporting(E_ALL & ~ (E_WARNING | E_NOTICE | E_STRICT | E_DEPRECATED));  
ini_set('display_startup_errors',1);  
ini_set('display_errors',1);  
ini_set('log_errors',1);

require dirname(__DIR__) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.inc";

// Checking a little the configuration..
if (!file_exists(APP_REPO_PATH)) {
  die("Repository folder doesn't exist. You must create a repository folder in your web app private path and configure it properly inside the config file.");
}	
if (!file_exists(APP_PIC_PATH)) {
  die("Picture folder doesn't exist. You must create a pciture folder in your web app public path and configure it properly inside the config file.");
}

require dirname(__DIR__) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "const.inc";
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "functions" . DIRECTORY_SEPARATOR . "funct.inc";

// ERROR HANDLING AND LOGGING

if (DEBUG) {
  error_reporting(E_ALL | E_STRICT);  
  ini_set('display_startup_errors',1);  
  ini_set('display_errors',1);
  ini_set('log_errors',1); 
} else {
  error_reporting(E_ALL & ~ (E_WARNING | E_NOTICE | E_STRICT | E_DEPRECATED));  
  ini_set('display_startup_errors',0);  
  ini_set('display_errors',0);  
  ini_set('log_errors',1); 
}

// Set no cache headers
// for HTTP/1.0 clients
header("Expires: 0");
header("Pragma: no-cache");
// HTTP/1.1 support
header("Cache-Control: private,no-cache,max-age=0,s-maxage=0,must-revalidate");
