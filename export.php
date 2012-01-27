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
require_once('settings.php');

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
      $username = $user->get_property("username"); //used in xml 
      //echo $username;die;
      $query = ("SELECT user_id, name from `lists` WHERE  list_id = " .  $list_id . " LIMIT 1");
      $result = mysql_query($query);
      $row = mysql_fetch_assoc($result);
      //print_r($row); die;
      if ($user_id == $row['user_id']) {
          $title = $row['name']; //used in xml
          //echo $title;die;
          $query = ("SELECT * from `tz_todo` WHERE list_id = " .  $list_id . " ORDER BY  in_out DESC,position");
          $result = mysql_query($query);
          if (mysql_num_rows($result) !=0) {
            while ($row = mysql_fetch_assoc($result)) {
                if ($row['in_out'] == 1 ) {
                  $in_out = "In the set";
                  $in_out = "in";
                } else {
                  $in_out = "In reserve";
                  $in_out = "out";
                }
                //echo $row['text'];
                switch ($row['type']) {
                  case 'todo':
                    $type = 'song';
                    break;
                  case 'break':
                    $type = 'break';
                    break;
                }
                $data[] = array($row['text'],$in_out,$type);
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

//OR as XML file

if (isset($data)) {
  
  if (isset($_GET['format'])) {
    $acceptable_formats = array('csv','xml');
    $format = filter_var($_GET['format'],FILTER_SANITIZE_STRING);
    if (!in_array($format,$acceptable_formats)) {
      header("Location: " . $host);
    } else {
      if ($format == 'csv') {
        //Create CSV file in memory
        $filename = "Setlistr_" . convert_to_filename($title) . ".csv";
        //echo $filename;die;
      } elseif ($format == 'xml') {
        //Create XML file in memory
        $filename = "Setlistr_" . convert_to_filename($title) . ".xml";
      } else {
        header("Location: " . $host);
      }

      //Send file for download
      header("Cache-Control: public");
      header("Content-Description: File Transfer;");
      header("Content-Disposition: attachment; filename=" . $filename . ";");
      header("Content-Type: application/octet-stream; "); 
      header("Content-Transfer-Encoding: binary");

      if ($format == 'csv') {
        exportCSV($data,array("Song","In/Out","Type"));
      } elseif ($format == 'xml') {
        exportXML($data, $username, $title);
      } else {
        header("Location: " . $host);
      }    
    }
}

} else {
  header("Location: " . $host);
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


function exportXML($data, $username, $list_title, $return_string = false) {
    
    $stream = ($return_string) ? fopen ('php://temp/maxmemory', 'w+') : fopen ('php://output', 'w');

    $doc = new DomDocument('1.0','UTF-8');
    
    //<playlist version="1" xmlns="http://xspf.org/ns/0/">
    $root = $doc->createElement('playlist');
    $root = $doc->appendChild($root);
      $root->setAttribute('version', '1');
      $root->setAttribute('xmlns', 'http://xspf.org/ns/0/');
      
    //<title>80s Music</title>
    $title = $doc->createElement('title');
    $title = $root->appendChild($title);
    $value = $doc->createTextNode($list_title);
    $value = $title->appendChild($value);
    
    //<creator>Jane Doe</creator>
    $creator = $doc->createElement('creator');
    $creator = $root->appendChild($creator);
    $value = $doc->createTextNode($username);
    $value = $creator->appendChild($value);

    //<trackList>
    $trackList = $doc->createElement('trackList');
    $trackList = $root->appendChild($trackList);

    foreach ($data as $record) {
      //<track>
      $track = $doc->createElement('track');
      $track = $trackList->appendChild($track);
      
      //<title>
      $title = $doc->createElement('title');
      $title = $track->appendChild($title);
      $value = $doc->createTextNode($record[0]);
      $value = $title->appendChild($value);
      
      /*if ($record[1]) {
        //<annotation>I love this song</annotation>
        $annotation = $doc->createElement('annotation');
        $annotation = $track->appendChild($annotation);
        $value = $doc->createTextNode($record[1]);
        $value = $annotation->appendChild($value);
      }*/
      
      if ($record[2] == 'break') {
        //<annotation>I love this song</annotation>
        $annotation = $doc->createElement('annotation');
        $annotation = $track->appendChild($annotation);
        $value = $doc->createTextNode('break');
        $value = $annotation->appendChild($value);
      }
      
      
    }

  $doc->formatOutput = true;
  echo $doc->saveXML(); 
}

/**
 * Thanks: http://www.johnrockefeller.net/php-tricks-eliminate-any-unwanted-characters-from-a-string/
 * Converts a string to a valid UNIX filename.
 * @param $string The filename to be converted
 * @return $string The filename converted
 */
function convert_to_filename ($string) {

  // Replace spaces with underscores and makes the string lowercase
  $string = str_replace (" ", "_", $string);

  $string = str_replace ("..", ".", $string);
  $string = strtolower ($string);

  // Match any character that is not in our whitelist
  preg_match_all ("/[^0-9^a-z^_^.]/", $string, $matches);

  // Loop through the matches with foreach
  foreach ($matches[0] as $value) {
    $string = str_replace($value, "", $string);
  }
  return $string;
}
?>
