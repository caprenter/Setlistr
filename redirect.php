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
      <h4>Public Lists</h4><br/>
      <?php
      $url = $host . "/api/?list=all&username=" . $username;
      $data = json_decode(file_get_contents($url));
      //print_r($data);
      if (isset($data[0]->data)) {
        echo "<p>This user doesn't have any public lists</p>";
      } else {
        foreach ($data as $list) {
          $lists[] = array( "date" =>strtotime($list->last_updated), 
                            "id"=> $list->list_id, 
                            "name" => $list->name);
        }
        usort($lists, "sort_by_date");
        //print_r($lists);
        print('<table class="api_demo"><thead><tr><th>id</th><th>Title</th><!--<th>Last Updated</th>--></tr></thead><tbody>');
        foreach ($lists as $list) {
          print(' <tr>
                    <td><a class="setlist" id="list-' . $list["id"] . '" href="' . $host .'list/' . $list["id"] . '">' . $list["id"] . '</a></td>
                    <td>' . $list["name"] . '</td>
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
