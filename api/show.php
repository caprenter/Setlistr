<?php
/*
 *      api/show.php
 *      Demonstrates use of the api
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
//Initiate the user access script
require_once "../phpUserClass/access.class.beta.php";
$user = new flexibleAccess();


  //Start output to the screen
  $page = "API Example"; //used for page title in header.php
  $list_id=0; //dummy variable to keep averything happy
  include('../theme/header.php'); 
?>


   <!--   <div class="list-buttons">
        <ul class="inline">
          <li><a id="newList1" href="<?php echo $host; ?>index.php?list=new">New List</a></li>
        </ul>
      </div>-->
          
    <div class="active-list">
      <h3>API Examples</h3>
    </div>

<div class="column-left" style="clear:both;margin-left:13px">
<h4><?php echo $host; ?>api/?list=all</h4><br/>
<?php
$url = $host . "/api/?list=all";
$data = json_decode(file_get_contents($url));
print('<table class="api_demo"><thead><th>id</th><th>Title</th><th>Last Updated</th></thead><tbody>');
foreach ($data as $list) {
  print(' <tr>
              <td>' . $list->list_id . '</td>
              <td>' . $list->name . '</td>
              <td>' . $list->last_updated . '</td>
          </tr>');
}
print('</tbody></table>');

//print_r($data);
?>
</div>

<div class="column-right">
<h4><?php echo $host; ?>api/?list=<?php echo $api_default_example_list_id; //$api_default_example_list_id from settings.php ?></h4><br/>
<?php
$url = $host . "/api/?list=" . $api_default_example_list_id; //$api_default_example_list_id from settings.php
$json =  file_get_contents($url);
//echo $json;
$data = json_decode($json);
//print_r($data);
print('<h3>' . $data[0]->title . '</h3>');
print('<p>Last Updated: ' . $data[0]->last_updated  . '</p>');
if(isset($data[0]->in_set)) {
  echo '<h4 class="api_demo">In set</h4>';
  print('<ul class="api_demo">');
    foreach ($data[0]->in_set as $song) {
      if ($song->type =='break') {
        $text = $song->title . ' (break)';
      } else {
        $text = $song->title;
      }
      print('<li>' . $text.'</li>');
    }
  print('</ul>');
}

if(isset($data[0]->not_in_set)) {
  echo '<h4 class="api_demo">Not in set</h4>';
  print('<ul class="api_demo">');
    foreach ($data[0]->not_in_set as $song) {
      if ($song->type =='break') {
        $text = $song->title . ' (break)';
      } else {
        $text = $song->title;
      }
      print('<li>' . $text.'</li>');
    }
  print('</ul>');
}


?>
</div>


<?php 
  $include_javascript = FALSE; //Because on some pages we don't want to include it!
  include('../theme/footer.php'); 
?>
