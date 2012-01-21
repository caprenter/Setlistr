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
 * Current options create a new lits and adds song or break to 'in the set' list
 * 
 * Could also...
 * Add songs to an existing list (at top/bottom)
 * Allow import of Setlistr format CSV (would set songs in set and in reserve) NB Export format also needs a tweek to allow this
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

  //If a list has been submitted deal with it and add it to the database
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
    //print_r($clean_songs);
    
    //Now check if a title has been submitted
    if (isset($_POST['title'])) {
      $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
      $title = filter_var($title, FILTER_SANITIZE_SPECIAL_CHARS);
    } else {
      $title = "List created from import";
    }
      
    
    if (count($clean_songs)>0) { //check we have at least one song!
      //Add them to the database
      require 'functions/connect.php'; //is this needed?
      require_once 'functions/todo.class.php';
      
      //create new list: We need the first song to do this 
      //NOTE: If first song is a break:, we don't deal with that case! It'll go in as a song
      $first_song = array_shift($clean_songs);
      //ToDO::createNewList(song title,user id,list title,TRUE if from import);
      //make the list with the first song in it
      $list_id = ToDO::createNewList($first_song,$user_id,$title,TRUE);
      //echo $list_id; die;
      //print_r($clean_songs);
      
      //Now add the songs and breaks
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
      //mysql_free_result($result);
      header("Location: " . $host);      
    }
  }
}

?>
<?php
$page = "Import";
include('theme/header.php');
  echo '<h1 class="title"><a href="' . $host . '">Setlistr</a></h1>';
?>
<h2 class="user-action">Import setlist</h2>
<p class="notice">Copy and paste (or type) a list of songs here to import them into Setlistr.</p>
<ul class="import-help">
  <li>One song per line.</li>
  <li>Use "break: Your break text here" (no qoutes) to insert a set break.</li>
</ul>

<form class="import-form" name="import-form" action="<?php echo $host; ?>import.php" method="post">
  <div class="import-box">
    <label for="title">Title</label> 
    <input type="text" size="40" value="" name="title" />
    <br/><br/>
    <label for="setlist">Songs</label> 
    <textarea cols="40" rows="15" name="setlist"></textarea>
    <br/>
    <input type="submit" value="import" />
    <input type="hidden" value="<?php echo $list_id; ?>" />
  </div>
</form>

<?php
include('theme/footer.php'); 
?>
