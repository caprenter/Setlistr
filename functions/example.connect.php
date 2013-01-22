<?php
/*
 *      connect.php
 *      Connect to the database
 *      This is basically the original script by
 *      Martin Angelov
 *      http://tutorialzine.com/2010/03/ajax-todo-list-jquery-php-mysql-css/
 * 
 *      Copyright 2011 caprenter <caprenter@gmail.com>
 *      
 *      This file is part of Setlistr.
 *      
 *      Setlistr is free software: you can redistribute it and/or modify
 *      it under the terms of the GNU Affero General Public License as published by
 *      the Free Software Foundation, either version 3 of the License, or
 *      (at your option) any later version.
 *      
 *      Setlistr is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU Affero General Public License for more details.
 *      
 *      You should have received a copy of the GNU Affero General Public License
 *      along with Setlistr.  If not, see <http://www.gnu.org/licenses/>.
 *      
 *      Setlistr relies on other free software products. See the README.txt file 
 *      for more details.
 */

/* Database config */

//now set in settings.php
//nasty hack that fetches settings.php depending on the depth of the page within the site
//This currently allows for one level e.g. /widget
//Also probably not good in terms of private/public variables, 
//BUT this is also a fix to using only one settings file across the site!
if (is_file("settings.php")) {
  include ("settings.php"); 
} elseif (is_file("../settings.php")) {
  include ("../settings.php"); 
} elseif (is_file("../../settings.php")) {
  include ("../../settings.php"); 
}

/* End config */
global $db_database;
global $db_host;
global $db_user;
global $db_pass;

$link = @mysql_connect($db_host,$db_user,$db_pass) or die('Unable to establish a DB connection');

mysql_set_charset('utf8');
mysql_select_db($db_database,$link);

?>
