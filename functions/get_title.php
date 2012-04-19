<?php
/*
 *      get_title.php
 *      Function to get the title of a particular list
 *      
 *      Copyright 2012 caprenter <caprenter@gmail.com>
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
 
 /**
 * Given a user_id and a list_id we fetch the title.
 * BE CAREFUL HOW YOU USE THIS. IS THE USER LOGGED IN??? IS THIS THE USERS OWN LIST??? 
 * name: get_title
 * @param integer $list_id The unique id of a set lists
 * @param integer $user_id The unique id of a user
 * @return string Tilte of the set list or empty
 */


//BEFORE YOU ALTER THIS REMEMBER
//get_title can also be used to check if a list belongs to a user. Given a list id and a user id
//it will return false if it fails to find a list with that id for that user.!!
function get_title ($list_id,$user_id) {
  //include("connect.php");
  $query = ("SELECT * FROM lists WHERE list_id = " . $list_id . " AND user_id = " . $user_id);
  $result = mysql_query($query);
  if (mysql_num_rows($result) > 0) {
    while($row = mysql_fetch_assoc($result)){
        return $row["name"];
    }
  } else {
    //echo "no rows";
    return FALSE;
  }
}
?>
