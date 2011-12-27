<?php
/*
 *      clone.php
 *      Routine to clone an existing list (logged in users only)
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

/*
* Clone a list
*/

//Pass the list id via the URL
if (isset($_GET['list'])) {
  //this is passed from the main set list page. We need to post it back
  $list_id = filter_var($_GET['list'], FILTER_SANITIZE_NUMBER_INT);
  if (!filter_var($list_id, FILTER_VALIDATE_INT)) {
    unset($list_id);
  }
  //echo $list_id;
}

//Clone it
if (isset($list_id)){
  //Is user logged in?
  require_once 'phpUserClass/access.class.beta.php';
  $user = new flexibleAccess();
  if ( $user->is_loaded() ) {
      //Find the id of the 'next' list
      $listResult = mysql_query("SELECT MAX(list_id)+1 FROM tz_todo");
      if(mysql_num_rows($listResult)) {
        list($cloned_list_id) = mysql_fetch_array($listResult);
      }
      //if(!$cloned_list_id) {
      //  $cloned_list_id = 1;
      //}
      
      //Get all the info about the existing list (from lists table)
      $query = ("SELECT name,user_id FROM lists WHERE list_id =" . $list_id);
      $result = mysql_query($query);
      if(mysql_num_rows($result)) {
        list($name,$user_id) = mysql_fetch_array($result);
      }
      //echo $name;
      //echo $user_id;
      //die;
      
      //Belt and braces check!!
      if ($user_id == $user->get_property("userID")) {
        //Fetch exisitng list items (from tz_todo)
        $query = ("SELECT * FROM `tz_todo` WHERE list_id =" . $list_id);
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
          //Loop through and insert new table rows as we go.
          while($row = mysql_fetch_assoc($result)){
            $query = ("INSERT INTO `tz_todo` SET list_id = " .$cloned_list_id . ",
                        type = '" . $row['type'] . "',
                        position =" . $row['position'] .",
                        in_out = " . $row['in_out'] . ",	text = '" . $row['text'] . "'");
            mysql_query($query);
            //echo $query;  
            //die;         
          } 
          //Now insert the new record in the lists table
          $query = ("INSERT INTO `lists` SET list_id = " .$cloned_list_id . ",
                        user_id = " . $user_id . ",
                        name = 'Clone of " . $name ."'" );
          //echo $query;
          mysql_query($query);
        }
      }
      //mysql_close($link);
  } //end if user is loaded
}

//Redirect to home page, hopefully still logged in, and with the cloned list.
header('Location: index.php');

