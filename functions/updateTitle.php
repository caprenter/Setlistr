<?php
/*
 *      updateTitle.php
 *      Ajax function to update a page title
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

include('connect.php');
require_once '../phpUserClass/access.class.beta.php';
$user = new flexibleAccess();
require "todo.class.php";

//Sanitize the id variable
//$list_id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
$list_id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);

//Check the id is of the form list-xxx
if  (startsWith($list_id, 'list-')) {
 $list_id = substr($list_id,5);
 //echo $list_id;
} else {
  $list_id = "bad id!"; //return a string which will fail at the next test below
}


if (filter_var($list_id, FILTER_VALIDATE_INT)) { //check it is an integer
  $new_name = filter_var($_POST['value'], FILTER_SANITIZE_STRING);

  //Only update a list title if we're logged in and it's our list.
  if ( $user->is_loaded() ){
    $user_id = $user->get_property("userID");
  } else { //or we're logged out and the list owner is '0'
    $user_id = 0;
  }
    mysql_query("UPDATE `lists` SET name='" . $new_name . "' WHERE list_id = " . $list_id . " AND user_id = " . $user_id);
    //if(mysql_error($GLOBALS['link'])) {
     //   throw new Exception($link . "Error updating title!");
   // }
  //$query = mysql_query("SELECT * FROM `tz_todo` WHERE list_id = " . $list_id . " ORDER BY `position` ASC");
  echo  $new_name;
  //mysql_fetch_assoc($query);
}


//Thanks: http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}
?>
