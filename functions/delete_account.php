<?php
//Only delete if user is logged in!
//this is belt and braces, we shouldn't even get this far if the user is not loaded
if ( $user->is_loaded() ){ 
  $logged_in_user_id = $user->get_property("userID");
  
  //$user_id taken from the post form on user.php
  //Check it's the same as the logged in user
  if ($user_id == $logged_in_user_id) {
    
    //IF a confirmation has not been received
    //Show an 'Are you sure' Message and a form to submit confirm=yes
    if (!isset($_POST['confirm'])) {
      $page = "My Account"; //used for page title in header.php
      include('theme/header.php'); 
      //$user = new flexibleAccess();
      $user = new flexibleAccess();
      ?>

      <div id="nav">
        <div class="login">
          <ul class="inline">
            <?php 
              if ( $user->is_loaded() ){
                $user_id = $user->get_property("userID");
                echo "<li class='username'>" . $user->get_property("username") . "</li>";
                //echo '<li class="logout"><a href="'.$_SERVER['PHP_SELF'].'?logout=1">logout</a></li>';
                echo '<li class="logout"><a href="' . $host . '?logout=1">logout</a></li>';
              } else {
                //User is loaded
                echo "<li><a href='" . $host . "login.php'>Login</a></li>";
              }
            ?>
          </ul>
        </div><!--login-->
        <h1 class="title"><a href="<?php echo $host; ?>">Setlistr</a></h1>
        <div class="list-buttons">
          <ul class="inline">
            <li><a href="<?php echo $host; ?>user.php">Back</a></li>
          </ul>
        </div>
      </div><!--nav-->
      
      
      <div class="active-list">
        <h3>Are you sure you want to delete this account?</h3>
        <p>This will destroy all data and can not be undone.</p>
      <?php
      print('<form action="user.php" method="post" id="delete-account">
        <input type="hidden" name="user" value="' . $user_id . '"/>
        <input type="hidden" name="confirm" value="yes"/>
        <input type="submit" class="form-submit" value="Yes - Delete Account" id="delete-user-account" name="delete-user-account">
      </form></div>');
      include('theme/footer.php'); 
      die; //this can be used here to stop the execution of the rest of the user.php page
    } 
    //WE HAVE CONFIRMATION so go ahed and delete...
    else if (isset($_POST['confirm']) && $_POST['confirm'] == "yes") {
      //Find all the users lists:
      $query = sprintf("SELECT * FROM `lists` WHERE `user_id` ='%d' ORDER BY `last_updated` DESC",$user_id );
      $result = mysql_query($query);
      if (mysql_num_rows($result) > 0) {
        while($row = mysql_fetch_assoc($result)){
          $lists[] = $row["list_id"];
        }
      }
      //print_r ($lists); die;
      //Delete all the songs on each list
      mysql_query("DELETE FROM tz_todo WHERE list_id IN ( ".implode(",",$lists).")");
      //Delete all the lists
      mysql_query("DELETE FROM lists WHERE list_id IN ( ".implode(",",$lists).")");
      //Delete the user
      mysql_query("DELETE FROM users WHERE userID = " . $user_id );
      include('theme/header.php'); 
      ?>
      <div id="nav">
        <div class="login">
          <ul class="inline">
            <li><a href="<?php echo $host; ?>login.php">Login</a></li>
          </ul>
        </div><!--login-->
        <h1 class="title"><a href="<?php echo $host; ?>">Setlistr</a></h1>
        <div class="list-buttons">
          <ul class="inline">
            <li><a href="<?php echo $host; ?>">New List</a></li>
          </ul>
        </div>
      </div><!--nav-->
      
      
      <div class="active-list">
      <h3>Your account has been deleted</h3>
      <?php
      include('theme/footer.php'); 
      die; //this can be used here to stop the execution of the rest of the user.php page
    } //end if confirm =yes
  }   //end if user to delete matches logged in user
}     //end if user is logged in
?>
