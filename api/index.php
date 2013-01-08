<?php
/*
 *      api/index.php
 *      Enables users to get data remotely
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
require_once('../settings.php');

include("../functions/connect.php"); //is this needed?
//include("../functions/is_list_public");
//Initiate the user access script
//require_once 'phpUserClass/access.class.beta.php';
//$user = new flexibleAccess();

/* API parameters
 * setlistr/api/?list=all
 * Returns all public list ids
 * 
 * setlistr/api/?list=(integer)
 * Returns all data about that list:
 * Title
 * Last updated
 * Songs in the set
 * Songs not in the set
 * 
 * additional parameters:
 * songs=in/out/all - defaults to all [in=all songs in the set, out=all songs not in the set, all=both]
 * breaks=yes/no - defaults to 'yes' [include 'set break' items in the list or not]
 * format=xml,json
 * 
 * Filter lists to a particular user (only for registered users with api key)
 * user=username&api_key=(string) (not written yet)
 * 
*/

//Set up some variables
$acceptable_formats = array('xml','json');
$default_format = 'json';

//Filter API parameters
if (isset($_GET['list'])) {
    //can be 'all' or an integer
    if ($_GET['list'] === 'all') {
      $list_id = 'all';
    } else {
      $list_id = filter_var($_GET['list'], FILTER_SANITIZE_NUMBER_INT);
    }
}

if (isset($_GET['songs'])) {
    //can be 'in/out/all' 
    $allowed_songs_options = array("in","out","all");
    $song_opts = filter_var($_GET['songs'],FILTER_SANITIZE_STRING);
     if (!in_array($song_opts, $allowed_songs_options)) {
       $song_opts = 'all';
     }
}

if (isset($_GET['breaks'])) {
    //can be 'yes/no' 
    $allowed_break_options = array("yes","no");
    $breaks = filter_var($_GET['breaks'],FILTER_SANITIZE_STRING);
    $breaks = strtolower($breaks);
     if (!in_array($breaks, $allowed_break_options)) {
       $breaks = 'yes';
     }

}

if (isset($_GET['username'])) {
    //can be 'yes/no' 
    $username = filter_var($_GET['username'],FILTER_SANITIZE_STRING);
    
}
if (isset($_GET['key'])) {
    //can be 'yes/no' 
    $key = filter_var($_GET['key'],FILTER_SANITIZE_STRING);
    if (strlen($key) != 30) {
      unset($key);
    }
    if (isset($username) && isset($key)) {
      //Check API key belongs to user
      $query = "SELECT username, apikey FROM `users` WHERE username = \"" . $username . "\" AND apikey = \"" . $key . "\"";
      //echo $query;
      //echo mysql_query($query);
      $result = mysql_query($query);
      if (mysql_num_rows($result) != 1) {
        unset($key);
      }
    }
}

//If we don't have a valid integer or the string 'all' supplied afor the list parameter we can go no further
//We need to send an error report
//NB if $_GET['list'] contains integers, FILTER_VALIDATE_INT will validate as FILTER_SANITIZE_NUMBER_INT
//will return an integer
if (!(filter_var($list_id, FILTER_VALIDATE_INT) || $list_id == 'all' )) {
  //rset a message that says you must supply a list value
  $error[] = array("list-id" => "Invalid list value. You must supply a the list id of a public list (integer) or the string 'all'"); 
}


if ($list_id == 'all') {
  //Get all public lists from the database;
 //$query = "SELECT list_id,user_id,name,last_updated FROM `lists` WHERE public = 1";
  $query = "SELECT lists.list_id, lists.user_id, lists.name, lists.last_updated,  users.username FROM lists JOIN users ON lists.user_id = users.userID WHERE public = 1";
  if (isset($username)) {
    //echo $username;
    $query = "SELECT lists.list_id, lists.name, lists.last_updated FROM lists JOIN users ON lists.user_id = users.userID WHERE users.username =\"" . $username ."\"";
    if (!isset($key)) {
      $query .= " AND public = 1";
    }
  }
  $query .= " ORDER BY  list_id";
  //echo $query;
      $result = mysql_query($query);
      if (mysql_num_rows($result) !=0) {
        $data = array();
        while ($row = mysql_fetch_assoc($result)) {
            //print_r($row);
            $data[] = array('list_id' => $row['list_id'],
                            'title' => $row['name'], //changed from 'name' => for consitency. Thanks @kaerast.
                            'username' => $row['username'], //suggested by @kaerast.
                            'last_updated' => $row['last_updated']
                            );
        }
      }
  }

if (filter_var($list_id, FILTER_VALIDATE_INT)) {
  //echo $list_id;
  //Try to get the data for the list with this id
  include("../functions/is_list_public.php"); //if true, will also return list metadata (name, last_updated, user_id)
  $public_list = is_list_public($list_id);
  //var_dump($public_list);

  //if it is then get the list data
  if (isset($list_id) && $public_list || isset($list_id) && isset($username) && isset($key)) {
    //either the list is public
    $proceed = TRUE;
    //OR
    //username and key match, so need to check the list belongs to the user
    if (isset($key)) {
      //Check list belongs to user then if it does fetch the list otherwise send error msg
      $query = ("SELECT userID, name, last_updated FROM users JOIN lists ON users.userID = lists.user_id WHERE users.username = \"" . $username ."\" AND list_id = " . $list_id);
      echo $query;
      $result = mysql_query($query);
      if (mysql_num_rows($result) == 1) {
        while($row = mysql_fetch_assoc($result)){
          $public_list[0] = $row["name"];
          $public_list[1] = $row["last_updated"];
        }
      } else {
        //list does not belong to the user
        //fail...
        $proceed = FALSE;
      }
    }
    
    if ($proceed) {
      $query = ("SELECT * FROM `tz_todo` WHERE list_id = " . $list_id . " ORDER BY position");
      
      $result = mysql_query($query);
      if (mysql_num_rows($result) > 0) {
        while($row = mysql_fetch_assoc($result)){
          //print_r($row);
          if ($row['type'] == 'todo') {
            $row['type'] = 'song';
          }
          //filter options
          //We do this by skipping the routine that stores our 'songs' in an array if the filters match or not
          //breaks
          if (isset($breaks) && $breaks == "no" && $row['type'] == 'break') { //only return songs
            continue 1;
          }
          //Songs
          if (isset($song_opts) && $song_opts == 'out' && $row['in_out'] == 1) { //NB 1=in. Returns songs/items NOT in the set
            continue 1;
          }
          if (isset($song_opts) && $song_opts == 'in' && $row['in_out'] == 0) { //NB 0=out. Returns songs/items in the set
            continue 1;
          }
          //Array to store the data we want to return
          $songs[] = array("title" => $row['text'],
                         "in-out" => $row['in_out'],
                         "type" => $row['type']
                         );
        }
      }
      mysql_close($link);
      //print_r($public_list);
      $title = $public_list[0];
      $last_updated =  $public_list[1];
      $username = $public_list[2];
      //$last_updated = date("jS M, Y H:i:s",strtotime($last_updated));
      $last_updated  = date("Y-m-d",strtotime($last_updated))."T".date("H:i:sP",strtotime($last_updated));
      //$list_user_id = $public_list[2];
      
      //Create 2 arrays of songs. One in the set, one NOT in the set
      if (isset($songs)){
        foreach ($songs as $song) {
          if ($song["in-out"] == 1) {
            $in_set[] = array("title" => $song["title"],"type" => $song["type"]);
          } else {
            $not_in_set [] = array("title" => $song["title"],"type" => $song["type"]);
          }
        }
      }
      if(!isset($not_in_set)) {
          $not_in_set = NULL;
       }
      if(!isset($in_set)) {
          $in_set = NULL;
      }

      $data[] = array('title' => $title,
                      'list_id' => $list_id, //Added from suggestion by kaerast
                      'username' => $username,
                      'last_updated' => $last_updated,
                      'in_set' => $in_set,
                      'not_in_set' => $not_in_set
                      );
                      //print_r($data);
    } //if $proceed
  }
} //if list_id is int


//Output the results
//Check to see if we have any data!
if (!isset($data) || $data == NULL) {
  $error[] = array("data" => "Nothing to return. Check your request. You must supply a the list id of a public list (integer) or the string 'all'");
  //die;
}


//Check to see if we have a requested format
if (isset($_GET['format'])) {
  //Sanitize input
  $format = filter_var($_GET['format'], FILTER_SANITIZE_STRING);
  //Check to see if the string is equal to an acceptable format. If not set it to the default
  if (!in_array($format,$acceptable_formats)) {
    $format = $default_format;
  }
}

if (!isset($format)) {
  $format = $default_format;
}
//echo $format;

//If there are errors return the errors in the requested format and die
if (isset($error)) {
  //return error messages (in requested format?)
  if ($format == 'json') {
      output_json($error);
      die;
  } elseif ($format=="xml") {
       $doc = new DomDocument('1.0','UTF-8');
            
        //<setlistr version="1">
        $root = $doc->createElement('setlistr');
        $root = $doc->appendChild($root);
          $root->setAttribute('version', '1');
          
        //<generated>timestamp</generated>
        $generated = $doc->createElement('generated');
        $generated = $root->appendChild($generated);
        $value = $doc->createTextNode(date("Y-m-d")."T".date("H:i:sP"));
        $value = $generated->appendChild($value);
        
        
        foreach ($error as $err) {
          foreach ($err as $key=>$text) {
            //<error>
            $error_element = $doc->createElement('error');
            $error_element = $root->appendChild($error_element);
              $error_element->setAttribute('type',$key); 
              $value = $doc->createTextNode($text);
              $value = $error_element->appendChild($value);      
          }
        }
        $doc->formatOutput = true;
        echo $doc->saveXML(); 
        die;
  }
  
} else { //We have some good data so we output it in the required format

  //Send file for download
  $filename = "setlistr-api-request." . $format;
  header("Cache-Control: public");
  header("Content-Description: File Transfer;");
  //header("Content-Disposition: attachment; filename=" . $filename . ";");
  header("Content-Type: application/octet-stream; "); 
  header("Content-Transfer-Encoding: binary");
  
  //return the data in requested format
  switch($format) {
    case 'json':
      output_json($data);
      break;
    case 'xml':
      output_xml($data);
      break;
    default:
      //what goes here?
      break;
  }
} 

/*
 * Echos a json string of data converted from a php array
 * 
 * name: output_json
 * @param array $data 
 */
function output_json($data) {
  header('content-type: application/json; charset=utf-8');
  $data = json_encode($data);
  echo $data;
  
  //JSONP - see http://www.carolinamantis.com/wordpress/?p=29
  //header("Content-type: application/json");
  //echo $_GET['callback'] . ' (' . $data . ');';
}

/*
 * Echos an XML string of data converted from a php array
 * Needs to deal with different cases: List of setlists, or an actual setlist
 * 
 * name: output_json
 * @param array $data 
 */
function output_xml($data, $return_string = false) {
    $stream = ($return_string) ? fopen ('php://temp/maxmemory', 'w+') : fopen ('php://output', 'w');
    $doc = new DomDocument('1.0','UTF-8');
  
    //We have 2 cases
    //Either it's s list of setlist metadata about lists, or it's an actual set list
    //'song' is unique to set lists and not in the metadata
    //print_r($data);
    if (array_key_exists('in_set',$data[0])) { //we have a set list
      //Need to alter the format of the song lists to work with our export function
      //Function accepts song data like: $data[] = array($row['text'],$in_out,$type);
      if (isset($data[0]['in_set']) && $data[0]['in_set'] !=NULL) {
        foreach ($data[0]['in_set'] as $song) {
          $song_data[] = array($song['title'],"in set",$song['type']);
        }
      }
      if (isset($data[0]['not_in_set']) && $data[0]['not_in_set'] !=NULL) {
        foreach ($data[0]['not_in_set'] as $song) {
          $song_data[] = array($song['title'],"not in set",$song['type']);
        }
      }
      //print_r($song_data);
      include("../functions/exportXML.php");
      exportXML($song_data, 'setlistr', $data[0]['title'], $return_string = false);
    
    } else {
      //we have a list of lists
        
    //<setlistr version="1">
    $root = $doc->createElement('setlistr');
    $root = $doc->appendChild($root);
      $root->setAttribute('version', '1');
      
    //<generated>timestamp</generated>
    $generated = $doc->createElement('generated');
    $generated = $root->appendChild($generated);
    $value = $doc->createTextNode(date("Y-m-d")."T".date("H:i:sP"));
    $value = $generated->appendChild($value);
    
    
    foreach ($data as $record) {
      //<list>
      $list = $doc->createElement('list');
      $list = $root->appendChild($list);

    
      //<id>
      $id= $doc->createElement('id');
      $id = $list->appendChild($id);
      $value = $doc->createTextNode($record['list_id']);
      $value = $id->appendChild($value);
      
      //<title>
      $title = $doc->createElement('title');
      $title = $list->appendChild($title);
      $value = $doc->createTextNode($record['name']);
      $value = $title->appendChild($value);
      
      //<last-updated>
      $last_updated = $doc->createElement('lastUpdated');
      $last_updated = $list->appendChild($last_updated);
      $value = $doc->createTextNode($record['last_updated']);
      $value = $last_updated->appendChild($value);
      
    }

  $doc->formatOutput = true;
  echo $doc->saveXML(); 
}
}
?>
