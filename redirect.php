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
 
/* If the URL contains a username, then we serve up a page of their lists
 * If logged in they get all their lists
 * If not then they get only their public lists
 * If logged in and looking at someone else's lists, they should only see public ones.
 * 
 * This is all made off the api JSON
*/

//Thanks to: http://www.phpaddiction.com/tags/axial/url-routing-with-php-part-one/
//echo $_SERVER['SERVER_NAME'] .PHP_EOL;
//echo $_SERVER['REQUEST_URI'];
$request = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
//echo $request;
//$scriptName = filter_var($_SERVER['SCRIPT_NAME'],FILTER_SANITIZE_URL);
//echo $scriptName;
$url_componants = explode("/",$request);
$no_componants = count($url_componants);
$username = $url_componants[($no_componants-1)];
$username = preg_replace("/%20/"," ",$username);
//echo $username;
//Does user exist?
$query = sprintf("SELECT userID FROM `users` WHERE `username` ='%s'",$username );
$result = mysql_query($query);
//echo mysql_num_rows($result);
if (mysql_num_rows($result) > 0) {
  while($row = mysql_fetch_assoc($result)){
      $user_id = $row["userID"];
  }

    //Start output to the screen
    $page = "User: " . $username; //used for page title in header.php
    $list_id=0; //dummy variable to keep averything happy
    include('theme/header.php'); 
    ?>

    <div class="workspace">
      <div class="active-list">
        <h2><?php echo $username; ?></h2>
      </div>

      <div class="column-left" style="clear:both;margin-left:13px">
      
      <?php
      //Get all public lists via the api for this user
      $url = $host . "/api/?list=all&username=" . $username;
      //If the viewer is logged in then check to see if they are looking at their own page
      //if they are also send their api-key in the api call and then we can get private lists as well
      if ( $user->is_loaded() ) { 
        $logged_in_user = $user->get_property("username");
        if ($logged_in_user == $username) {
          $user_api = $user->get_property("apikey");
          $url .= "&key=" . $user_api;
          $list_title = "Lists";
        } else {
          $list_title = "Public Lists";
        }
      }
      echo "<h4>" . $list_title . "</h4><br/>";
      
      $data = json_decode(file_get_contents($url));
      //print_r($data);
      if (isset($data[0]->data)) {
        echo "<p>This user doesn't have any public lists</p>";
      } else {
        foreach ($data as $list) {
          $this_list = array( "date" =>strtotime($list->last_updated), 
                            "id"=> $list->list_id, 
                            "title" => $list->title);
          if (isset($user_api)) { //then return data about public/private lists
            //echo $list->privacy;
            $this_list["privacy"] = $list->privacy;
          }
          $lists[] = $this_list;
        }
        usort($lists, "sort_by_date");
        //print_r($lists);
        print('<table class="api_demo"><thead><tr><th>id</th><th>Title</th><!--<th>Last Updated</th>--></tr></thead><tbody>');
        foreach ($lists as $list) {
          //print_r($list);echo $list["privacy"];
          if (isset($list["privacy"]) && $list["privacy"] == 1) {
            $privacy = "public";
            $at_least_one_public_list = true; //set a flag to check that we have a public list - if we do we print a 'key' later on
          } else {
            $privacy ="private"; //this is odd because when looking at another users list when logged in the 'private' class will be applied
          }
          print(' <tr class="' . $privacy . '">
                    <td><a class="setlist" id="list_' . $list["id"] . '" href="' . $host .'list/' . $list["id"] . '">' . $list["id"] . '</a></td>
                    <td>' . $list["title"] . '</td>
                    <!--<td>' . date("Y-m-d H:i",$list["date"]) . '</td>-->
                  </tr>');
        }
        
        /*foreach ($data as $list) {
          print(' <tr>
                    <td><a class="setlist" id="' . $list->list_id . '" href="' . $host .'list/' . $list->list_id . '">' . $list->list_id . '</a></td>
                    <td>' . $list->name . '</td>
                    <td>' . date("Y-m-d H:i",strtotime($list->last_updated)) . '</td>
                  </tr>');
        }*/
        print('</tbody></table>');
        if (isset($at_least_one_public_list)) { //print a 'key' to show what the * created with css by a public list means
          echo "<br/><br/>* public list";
        }
      }
      //print_r($data);
      ?>
      </div>

      <div class="column-right">
        <div id="list-container"></div>
      </div>

    </div><!--end workspace-->

    <?php 
      $user_page_javascript = TRUE; //Because on some pages we don't want to include it!
      include('theme/footer.php'); 
      die;
}


function sort_by_Date( $a, $b ) {
    return $b["date"] - $a["date"];
}
?>
