<?php
/*
 *      is_list_public.php
 *      Function to check if a setlist is set to public display
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
 
 /**
 * Tests to see if a list is public. If it is we return the Title and last updated time 
 * name: is_list_public
 * @param integer $list_id The unique id of a set lists
 * @return string Tilte of the set list
 */

function is_list_public ($list_id) {
  //include("connect.php");
  $query = ("SELECT lists.public, lists.user_id, lists.name, lists.last_updated, users.username FROM lists JOIN users ON lists.user_id = users.userID WHERE lists.list_id = " . $list_id);
  $result = mysql_query($query);
  if (mysql_num_rows($result) > 0) {
    while($row = mysql_fetch_assoc($result)){
      if ($row["public"] == TRUE) {
        //echo $row["name"];
       // mysql_close($link);
        return array($row["name"],$row["last_updated"],$row["username"],$row["user_id"]);
      } else {
        //mysql_close($link);
        return FALSE;
      }
    } 
  } else {
    //echo "no rows";
    return FALSE;
  }
}
?>
