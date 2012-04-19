<?php
require_once('../settings.php');
require_once "../phpUserClass/access.class.beta.php";
include("../functions/get_title.php");
$user = new flexibleAccess();
//echo $_GET['list'];
if (isset($_GET['list'])) {
    $posted_list_id = filter_var($_GET['list'], FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($posted_list_id, FILTER_VALIDATE_INT)) {
      unset($posted_list_id);
    }
}
?>

    <?php
    $url = $host . "/api/?list=" . $posted_list_id; //$api_default_example_list_id from settings.php
    $json =  file_get_contents($url);
    //echo $json;
    $data = json_decode($json);
    //print_r($data);
    print('<h3>' . $data[0]->title . '</h3>');
    print('<p>Last Updated: ' . date("F j, Y, g:ia",strtotime($data[0]->last_updated))  . '</p>');
    print('<p>Link: <a href="' . $host  . '/list/' . $posted_list_id . '">' . $host  . '/list/' . $posted_list_id . '</a>');
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
    
    if ($user->is_loaded()){
        $user_id = $user->get_property("userID");
        //Check to see if it is this users list:
        if (get_title($posted_list_id,$user_id) != FALSE) {
          print(' <form name="edit-public-list" action="' . $host . '" method="post" class="edit-list">
                  <input type="hidden" name="list" value="'.  $posted_list_id .'"/>
                  <input type="submit" value="Edit List" />
                  </form>');
        }
    }


?>
