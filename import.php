<?php
/*
 *      import.php
 *      Enables users to import a set list
 *      Currently only logged in users can import
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
/* import list typed or pasted into textarea
 * Current options:
 * Create a new list and adds song or break to 'in the set' and 'not in set' list
 * Add songs to exisiting list
 * 
 * Could also...
 * Add songs to an existing list (at top)
 * Allow import of Setlistr format CSV (would set songs in set and in reserve) NB Export format also needs a tweek to allow this
 * Allow import of Setlistr format XML
 * Allow import of Setlistr format JSON
 * Allow logged out users to import a list
 * 
*/



//Initiate the user access script
require_once 'phpUserClass/access.class.beta.php';
$user = new flexibleAccess();

//For logged in users only!
if (!$user->is_loaded()) {
  header("Location: " . $host);
} 


if ($user->is_loaded()) {
  $user_id = $user->get_property("userID");
  
  //Pass the list id via the URL
  if (isset($_GET['list'])) {
    //this is passed from the main set list page. We need to post it back, and to allow a user to ammend an existing list
    $list_id = filter_var($_GET['list'], FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($list_id, FILTER_VALIDATE_INT)) {
      unset($list_id);
    }
    //echo $list_id;
  }
   
  include ("functions/get_title.php");

  //If data has been submitted deal with it and add it to the database
  //Songs
  if (isset($_POST['setlist'])) {
    $setlist = filter_var($_POST['setlist'], FILTER_SANITIZE_STRING);
    //$setlist = filter_var($setlist, FILTER_SANITIZE_SPECIAL_CHARS);
    //echo $setlist;
    //echo $_POST['setlist'];
    //$setlist=$_POST['setlist'];
    $songs = preg_split("/[\r\n]+/", $setlist, -1, PREG_SPLIT_NO_EMPTY);
    //var_dump($songs);
    foreach ($songs as $song) {
      $clean_songs[] = filter_var($song, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    
    $not_in_setlist = filter_var($_POST['setlist_out'], FILTER_SANITIZE_STRING);
    //$setlist = filter_var($setlist, FILTER_SANITIZE_SPECIAL_CHARS);
    //echo $setlist;
    //echo $_POST['setlist'];
    //$setlist=$_POST['setlist'];
    $not_in_songs = preg_split("/[\r\n]+/", $not_in_setlist, -1, PREG_SPLIT_NO_EMPTY);
    //var_dump($songs);
    foreach ($not_in_songs as $song) {
      $clean_not_in_songs[] = filter_var($song, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    //print_r($clean_not_in_songs);
    
    //New list or add to existing list?
    //List can be either 'new' or 'current'
    if (isset($_POST['list'])) {
      $list = filter_var($_POST['list'], FILTER_SANITIZE_STRING);
      if (!in_array($list,array("new","current"))) {
        //bad variable passed - default to new list.
        $list = "new";
      }
    }
    
    //If we are asked to add songs to the current list
    //Check the list. If checks fail, list_id is unset.
    if ($list == "current") {
      if (isset($_POST['list_id'])) {
        $list_id = filter_var($_POST['list_id'], FILTER_SANITIZE_NUMBER_INT);
        //Now check this list belongs to the user!!
        //We can use get_title to do this. It will return false if 
        if (get_title($list_id,$user_id) == FALSE) {
          unset($list_id);
        }
      }
    }
    
    
    //Now check if a title has been submitted
    if (isset($_POST['title'])) {
      $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
      $title = filter_var($title, FILTER_SANITIZE_SPECIAL_CHARS);
      if ($title == NULL) {
        $title = "List created from import";
      }
    } else {
      $title = "List created from import";
    }
      
    //Data has been collected and sanitized...
    if (count($clean_songs) > 0 || count($clean_not_in_songs) > 0) { //check we have at least one song!
      //Add them to the database
      //Obviously we have case where not-in-set could be empty, as could the in-set list
      require 'functions/connect.php'; //is this needed?
      require_once 'functions/todo.class.php';
      
      //Either create a new list or add to existing list
      //The function that creates a new list requires a list entry
      if (!isset($list_id)) {
        //create new list: We need the first song to do this 
        if (count($clean_songs) > 0) {
          //NOTE: If first song is a break:, we don't deal with that case! It'll go in as a song
          $first_song = array_shift($clean_songs);
          //make the list with the first song in it
          $list_id = ToDO::createNewList($first_song,$user_id,$title,TRUE);
        } else {
          //We don't have any songs in the 'in' set, so start a new list with songs not in set
          $first_song = array_shift($clean_not_in_songs);
          //make the list with the first song in not in set column
          $list_id = ToDO::createNewList($first_song,$user_id,$title,TRUE,TRUE);
        }        
      } 
      //Now add the songs and breaks to the 'in set' list
      if (count($clean_songs) > 0) {
        foreach ($clean_songs as $song) {
          //check to see if it is a song or a break, then add it to the database
          //make sure it goes to a list assigned to the user.
          //echo $song;
          if (strstr($song,'break:')) {
            //echo 'found';
            $break = preg_replace('/break:/',' ',$song);
            $break = trim($break);
            //echo $break; die;
            ToDo::createBreak($break,$list_id,TRUE);
          } else {          
            ToDo::createNew($song,$list_id,TRUE);
          }
        }
      }
      //Now add the songs and breaks to the 'not in set' list
      //Difference is the final TRUE parameter in the ToDO command
      if (count($clean_not_in_songs) > 0) {
        foreach ($clean_not_in_songs as $song) {
          //check to see if it is a song or a break, then add it to the database
          //make sure it goes to a list assigned to the user.
          //echo $song;
          if (strstr($song,'break:')) {
            //echo 'found';
            $break = preg_replace('/break:/',' ',$song);
            $break = trim($break);
            //echo $break; die;
            ToDo::createBreak($break,$list_id,TRUE,TRUE);
          } else {          
            ToDo::createNew($song,$list_id,TRUE,TRUE);
          }
        }
      }
      //mysql_free_result($result);
      header("Location: " . $host);      
    }
  }
}

?>
<?php
$page = "Import";
include('theme/header.php');
if ($list_id) {
  //echo $list_id;
  $title = get_title($list_id,$user_id);
  //echo $title;
}
?>
<div class="workspace">
  <div class="active-list">
        <h2>Import Setlist</h2>
  </div>
  <p class="notice">Copy and paste (or type) a list of songs here to import them into Setlistr.</p>
  <ul class="import-help">
    <li>One song per line.</li>
    <li>Use "break: Your break text here" (no qoutes) to insert a set break.</li>
  </ul>

  <form class="import-form" name="import-form" action="<?php echo $host; ?>import.php" method="post">
    <div class="import-box">
      <label for="list" class="full-width">Import as new list or add to the current list?</label> <br/>
      <input class="list-radio" type="radio" name="list" value="new" checked="checked" onclick="noTitle();">New list<br/>
      <input class="list-radio" type="radio" name="list" value="current" onclick="showTitle();">Add to current list<br/>
      <br/>
      <label for="title">Title</label> 
      <input id="title" type="text" size="40" value="" name="title" />
      <br/><br/>
      <label for="setlist">Songs<br/>(in set)</label> 
      <textarea cols="40" rows="15" name="setlist"></textarea>
      <br/>
      <label for="setlist">Songs<br/>(not in set)</label> 
      <textarea cols="40" rows="15" name="setlist_out"></textarea>
      <br/>
      <input type="submit" value="Import" />
      <input type="hidden" name="list_id" value="<?php echo $list_id; ?>" />
    </div>
  </form>
  <br/><br/>
</div><!-- end workspace-->

<!--Thanks: http://www.hotscripts.com/forums/javascript/44260-change-text-when-clicking-radio-button.html-->
<!--Thanks: http://www.codingforums.com/showthread.php?t=226289-->
<script type="text/javascript">
    function showTitle() {
        var inputbox = document.getElementById("title");
        inputbox.value = "<?php echo $title; ?>";
    }
    function noTitle() {
        var inputbox = document.getElementById("title");
        inputbox.value = "";
    }
</script>
<?php
include('theme/footer.php'); 
?>
