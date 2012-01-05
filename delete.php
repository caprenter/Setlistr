<?php
/*
 *      delete.php
 *      Routine to delete a list (logged in users only)
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
require_once('settings.php');

//Initiate the user access script
require_once "phpUserClass/access.class.beta.php";
$user = new flexibleAccess();

//Validate the list id
if (isset($_GET['list'])) {
  //this is passed from the main set list page. We need to post it back
  $list_id = filter_var($_GET['list'], FILTER_SANITIZE_NUMBER_INT);
  if (!filter_var($list_id, FILTER_VALIDATE_INT)) {
    unset($list_id);
  }
  //echo $list_id;
}

if ($user->is_loaded()) {
  $user_id = $user->get_property("userID");
  if (isset($list_id)) {
    //echo $user_id;
    //Remove lists assigned to user 0 older than 2 hours
    $query = mysql_query("DELETE FROM lists WHERE list_id = " . $list_id . " AND user_id = " . $user_id);
    header('Location: ' . $host);
  }
} else {
   header('Location: ' . $host);
}
?>
