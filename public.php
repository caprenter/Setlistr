<?php
/*
 *      public.php
 *      Allows us to display lists that are set as 'publicly viewable.
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
 
//Check that the list is public.
if (isset($list_id)) {
  $public_list = is_list_public($list_id);
  //var_dump($public_list);

  //if it is then get the list data
  if (isset($list_id) && $public_list) {
    include("functions/connect.php");
    $query = ("SELECT * FROM `tz_todo` WHERE list_id = " . $list_id . " ORDER BY position");

    $result = mysql_query($query);
    if (mysql_num_rows($result) > 0) {
      while($row = mysql_fetch_assoc($result)){
        $songs[] = array("title" => $row['text'],
                       "in-out" => $row['in_out'],
                       "type" => $row['type']
                       );
      } 
    }
    mysql_close($link);

    $title = $public_list[0];
    $last_updated =  $public_list[1];
    $last_updated = date("jS M, Y H:i:s",strtotime($last_updated));
    $list_user_id = $public_list[2];
    
    //Create 2 arrays of songs. One in the set, one NOT in the set
    foreach ($songs as $song) {
      if ($song["in-out"] == 1) {
        $in_set[] = array("title" => $song["title"],"type" => $song["type"]);
      } else {
        $not_in_set [] = array("title" => $song["title"],"type" => $song["type"]);
      }
    }
  }
}

//Print stuff to the screen:
$page = "Public List"; //used for page title in header.php
include('theme/header.php');
?>
 <div class="workspace">
   <div class="visibility">Public List: Everyone can view this set list</div>
      
   <?php if (isset($title)) { ?>
      
      <div class="active-list public-list">
        <h4 id="208" class="public-list-title"><?php echo $title; ?></h4>updated: <?php echo $last_updated; ?>    
      </div>
      <!--<div class="visibility">
        <p><span class="label">Visability:</span> Everyone can view this set list</p>
      </div>-->
      <div class="column-left">
      <?php 
        if (isset($in_set)) {
            echo "<p class=\"list-header\">In the set</p>";
            theme_list_songs ($in_set,"sortable1");
        }
      ?>
      <?php
        if ($user->is_loaded()){
            $this_user_id = $user->get_property("userID");
            if ($this_user_id == $list_user_id) {
              print(' <form name="edit-public-list" action="' . $host . '" method="post" class="edit-list">
                      <input type="hidden" name="list" value="'.  $list_id .'"/>
                      <input type="submit" value="Edit List" />
                      </form>');
            }
        }
      ?>
      </div>

      <div class="column-right">
      <?php 
        if (isset($not_in_set)) {
          echo "<p class=\"list-header\">In reserve</p>";
          theme_list_songs ($not_in_set,"sortable2");
        }
      ?>
      </div>

  <?php } else { ?>
      <div class="not-public">Sorry this list is not available for public viewing.</div>

  <?php } ?>
  </div><!-- end workspace -->

<?php
//print_r($songs);
include('theme/footer.php'); 

/**
 * 
 * name: theme_list_songs
 * @param array $songs An array of song titles, and set breaks
 * @param string $css_id A string that sets a css attribute for the type of list
 * 
 * @return Just prints HTML to the screen
 */

function theme_list_songs ($songs,$css_id) {
    echo '<ul id="' . $css_id . '" class="todoList">';
    foreach ($songs as $song) {
      echo '<li class="todo ' . $song['type'] . '"><div class="text">' . $song['title'] . '</div></li>';
    }
    echo '</ul>';
}

/**
 * Tests to see if a list is public. If it is we return the Title and last updated time 
 * name: is_list_public
 * @param integer $list_id The unique id of a set lists
 * @return string Tilte of the set list
 */

function is_list_public ($list_id) {
  include("functions/connect.php");
  $query = ("SELECT * FROM lists WHERE list_id = " . $list_id);
  $result = mysql_query($query);
  if (mysql_num_rows($result) > 0) {
    while($row = mysql_fetch_assoc($result)){
      if ($row["public"] == TRUE) {
        //echo $row["name"];
        mysql_close($link);
        return array($row["name"],$row["last_updated"],$row["user_id"]);
      } else {
        mysql_close($link);
        return FALSE;
      }
    } 
  } else {
    //echo "no rows";
    return FALSE;
  }
}
?>
