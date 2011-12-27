<?php
/*
 *      clean_up_routine.php
 *      Used to purge the database of old lists. 
 *      When run, lists older than a specific time/date will be removed
 *      where they are not assigned to a logged in user.
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

include("functions/connect.php");
$date_time = date("Y-m-d H:i:s",(time() - 60*60*2));
//$date_time = "2011-12-20";
echo $date_time . PHP_EOL ;
$query = mysql_query("SELECT list_id FROM lists WHERE user_id = 0 and last_updated <='" . $date_time . "'");
if(mysql_num_rows($query)) {
 while($row = mysql_fetch_assoc($query)){
        $list_ids[] =  $row['list_id'];
        echo $row['list_id'] . PHP_EOL ;
    }
  //print_r($list_ids); die;
 //$list_ids = array("0","14","15");
 //print_r($list_ids);
 mysql_query("DELETE FROM  tz_todo WHERE list_id IN ( ".implode(",",$list_ids).")"); 
 mysql_query("DELETE FROM  lists WHERE list_id IN ( ".implode(",",$list_ids).")");  
 
}  
?>
