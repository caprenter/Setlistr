<?php
/*
 *      export.php
 *      Enables users to export a set list
 *      To do: Allow multiple export of many lists     
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

/* export list as CSV file*/
include("functions/connect.php"); //is this needed?
//Initiate the user access script
require_once 'phpUserClass/access.class.beta.php';
$user = new flexibleAccess();

//Only allow if user is logged in AND user owns the list
//Create an array of songs/breaks in the set list (or in reserve) that can easily be written to a csv file
if ( $user->is_loaded() ){
  //Pass the list id via the URL
  if (isset($_GET['list'])) {
    //this is passed from the main set list page. Sanitize it
    $list_id = filter_var($_GET['list'], FILTER_SANITIZE_NUMBER_INT);
    if (filter_var($list_id, FILTER_VALIDATE_INT)) {
      $user_id = $user->get_property("userID");
      $query = ("SELECT user_id from `lists` WHERE  list_id = " .  $list_id . " LIMIT 1");
      $result = mysql_query($query);
      $row = mysql_fetch_assoc($result);
      //print_r($row); die;
      if ($user_id == $row['user_id']) {
          $query = ("SELECT * from `tz_todo` WHERE list_id = " .  $list_id . " ORDER BY  in_out DESC,position");
          $result = mysql_query($query);
          if (mysql_num_rows($result) !=0) {
            while ($row = mysql_fetch_assoc($result)) {
                if ($row['in_out'] == 1 ) {
                  $in_out = "In the set";
                } else {
                  $in_out = "In reserve";
                }
                //echo $row['text'];
                $data[] = array($row['text'],$in_out);
            }
          } else {
            //no results returned
          }
      }
      mysql_free_result($result);
    }
  }
}

//Write the data to a downbloadable CSV file
//http://www.toosweettobesour.com/2008/10/10/outputting-csv-as-a-downloadable-file-in-php/
//Posted by Daniel Cousineau
//citing Timothy Boronczyk from the #phpc IRC channel on freenode
if (isset($data)) {
//Create CSV file in memory
$filename = "myset_list.csv";


//Send file for download
header("Cache-Control: public");
header("Content-Description: File Transfer;");
header("Content-Disposition: attachment; filename=" . $filename . ";");
header("Content-Type: application/octet-stream; "); 
header("Content-Transfer-Encoding: binary");

exportCSV($data,array("Song","In/Out"));

} else {
  echo "Sorry you can't export that list";
}



/*
 * 
 * name: exportCSV
 * @param array $data An array of set list data
 * @param array $col_headers An array of column headers for the csv
 * @param bool $return_string Set to true if you want the result returned as a string
 * @return
 */
function exportCSV($data, $col_headers = array(), $return_string = false)
{
    $stream = ($return_string) ? fopen ('php://temp/maxmemory', 'w+') : fopen ('php://output', 'w');

    if (!empty($col_headers))
    {
        fputcsv($stream, $col_headers);
    }

    foreach ($data as $record)
    {
        fputcsv($stream, $record,',','"');
    }

    if ($return_string)
    {
        rewind($stream);
        $retVal = stream_get_contents($stream);
        fclose($stream);
        return $retVal;
    }
    else
    {
        fclose($stream);
    }
}

?>
