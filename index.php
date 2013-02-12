<?php
/*
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
if (file_exists('settings.php')) {
  require_once('settings.php');
} else {
  die("You haven't created a settings.php file");
}
//Initiate the user access script
require_once "phpUserClass/access.class.beta.php";
$user = new flexibleAccess();

//Logout routine
if ( isset($_GET['logout']) && $_GET['logout'] == 1 ) {
	$user->logout('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
}

include ("redirect.php");
//Each page relies heavily on the $list_id variable:- the id of the list we're viewing/editing etc.
//The following sets all that up for us and deals with the cases of being logged in/out, creating new lists etc
if ( $user->is_loaded() ) {
  //Fetch all their  set lists, and store the data in $lists
  $user_id = $user->get_property("userID");
  $query = sprintf("SELECT * FROM `lists` WHERE `user_id` ='%d' ORDER BY `last_updated` DESC",$user_id );
  $result = mysql_query($query);
  if (mysql_num_rows($result) > 0) {
    while($row = mysql_fetch_assoc($result)){
      $lists[$row["list_id"]] = array(  "name" => $row["name"],
                                        "last_updated" => $row["last_updated"],
                                        "is_public" => $row["public"]
                                      );
    }
    //Create a one dimensional array of list ids
    foreach ($lists as $key=>$value) {
      $my_list_ids[] = $key;
    }
    //selects the first value of the array. In this case it should be the most recently edited list. 
    $list_id = reset($my_list_ids); 
    //print_r($lists); die;
  } else {
    //user has no lists
    //$lists is not set
  }
} else {
  //user is not known so provide a new list
  //$lists is not set
}

//Check list requested belongs to user
//Not sure when this is used. Is it old code?
if (isset($_POST['list'])) {
    $posted_list_id = filter_var($_POST['list'], FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($posted_list_id, FILTER_VALIDATE_INT)) {
      unset($posted_list_id);
    }
  //Then we want to select a specific list
  if ( $user->is_loaded() && in_array($posted_list_id,$my_list_ids)) {
       $list_id = $posted_list_id;
    } else {
      //If not make a new one
      $_GET['list'] = "new";
    }
}

//New List
if (isset($_GET['list']) && $_GET['list']=="new" || (!isset($lists)) || $lists == NULL){
    //Find the number of the last made list
    $listResult = mysql_query("SELECT MAX(list_id)+1 FROM tz_todo");
    if(mysql_num_rows($listResult)) {
			list($list_id) = mysql_fetch_array($listResult); //list function takes array values and assigns them to variables
    }
    //If no lists then set to 1. This is only going to happen on a fresh install
		if(!$list_id) {
       $list_id = 1;
    }
    if (!$user->is_loaded()) {
      $user_id = 0;
    }
    //Create a new list for the user - set user = 0 if not logged in.
    mysql_query("INSERT INTO lists SET name='New List', user_id=".$user_id.", list_id = ". $list_id );
    $lists[$list_id] = array( "name" => "New List",
                            "last_updated" => date("Y-m-d H:i:s",time())
                          );
    //Give the list one default song.
    mysql_query("INSERT INTO tz_todo SET text='New Song. Doubleclick to Edit.', type='todo', list_id = '". $list_id ."', position =1" );
}

//By now we should have a list to work with so pull the ajax functions to start working with list items
require "functions/todo.class.php";
//$query = sprintf("SELECT * FROM `tz_todo` WHERE list_id ='%d' ORDER BY `position` ASC",$lists[0]["id"]);
//echo $query;
if (isset($list_id)) {
  //If we have  a list id then fetch the data about the list items
  $query = mysql_query("SELECT * FROM `tz_todo` WHERE list_id = " . $list_id . " ORDER BY `position` ASC");

  $todos = array();
  // Filling the $todos array with new ToDo objects:
  while($row = mysql_fetch_assoc($query)){
    if ($row["in_out"]) {
      //echo "in";
      $in_set[] = new ToDo($row);
    } else {
      //echo "out";
      $not_in_set [] = new ToDo($row);
    }
  }
} 
?>

<?php
  //Start output to the screen
  $page = "Home"; //used for page title in header.php
  include('theme/header.php'); 
 ?>
    <div class="workspace">
      <div class="list-buttons">
          <?php include("theme/nav_buttons.php"); ?>
      </div>
      
      <div class="active-list">
        <?php
          //if we have a number of lists to choose from:
          //Display the title of the selected one, or if not selected the most recent
          if (isset($lists)) {
            echo '<h4 id="list-' . $list_id .'" class="edit list-title">' . $lists[$list_id]['name'] . '</h4>';
            echo '<div id="edit-and-updated"><a class="edit-title" href="#">[edit title]</a>&nbsp;<span id="updated">updated: ' . date("D, j M Y H:i:s",strtotime($lists[$list_id]["last_updated"])) . '</span></div>';
            //Sat, 28 Jan 2012 00:10:53 GMT
            
            //include ('theme/visibility_form.php'); //Check box to make list public/private
          } else {
            echo '<h4 id="list-' . $list_id .'" class="list-title">[Edit] Title</h4>';
          }
        ?>
      </div>
      <div class="column-left">
        <p class="list-header">In the set</p>
        <ul id="sortable1" class="todoList">
          <?php
            // Looping and outputting the $todos array. The __toString() method
            // is used internally to convert the objects to strings:
            echo '<li class="notice">Drag songs here to put them in your set or create them below &#8659;  </li>';
              if (isset($in_set)) {
                foreach($in_set as $item){
                  echo $item;
                }
            } 
          ?>
        </ul>
        <div class="buttons">
          <ul class="inline">
            <li><a id="addButton" class="green-button" href="#">Add a Song</a></li>
            <li><a id="addBreak" class="orange-button" href="#">Add a Set Break</a></li>
          </ul>
        </div>
      </div><!--end column left-->
      <div class="column-right">
        <p class="list-header">In reserve</p>
        <ul id="sortable2" class="todoList">
          <?php
            // Looping and outputting the $todos array. The __toString() method
            // is used internally to convert the objects to strings:
            echo '<li class="notice">Drag songs here to keep them in reserve &#8659;</li>';
            if (isset($not_in_set)) {
              foreach($not_in_set as $item){
                echo $item;
              }
            } 
          ?>
        </ul>
      </div>
    </div><!--end workspace-->
    <!-- This div is used as the base for the confirmation jQuery UI POPUP. Hidden by CSS. -->
    <div id="dialog-confirm" title="Delete item?">Are you sure you want to delete this item?</div>

    <!--<p class="note">The todos are flushed every hour. You can add only one in 5 seconds.</p>-->
    <div id="homepage-text">
      <div class="homepage-text-wrapper">
        <div class="homepage-left">
          <h3>Setlistr is a free service designed for musicians who need to organise their material.</h3>
          <p>For example it can be used to:</p>
          <ul>
            <li>Create a set list for your band</li>
            <li>Decide the running order of tracks on your latest recording</li>
            <li>Share your ideas with others</li>
            <li>Print your set list from your browser</li>
          </ul>
          <h3>Why should I create an account?</h3>
          <p>Maybe you shouldn't. If you just want to use the site without logging in, then you can.<br/>Lists are deleted after 2 hours if not saved, to help us keep our servers clean.</p>
          <p><br/>However, with an account, you can do more...</p>
          <ul>
            <li>Keep an archive of old sets</li>
            <li>Copy a set list, edit and re-use it</li>
            <li>Import set lists</li>
            <li>Export set lists</li>
            <li>Make lists public for others to see</li>
          </ul>
          <p>Accounts are free and we don't ask for credit-card details or anything like that.<br/> You can easily delete your account, and take your data away with you at anytime.</p>
          <p><br/></p>
          <h3>Improve Setlistr</h3>
          <p>Feedback, suggestions, feature requests, and offers of help are all welcome.</p>
        </div>
      </div><!--end homepage-text-wrapper-->
    </div><!--end homepage-text-->
<?php 
  $include_javascript = TRUE; //Because on some pages we don't want to include it!
  include('theme/footer.php'); 
?>
