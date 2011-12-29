<?php 
/*
 *      user.php
 *      A routine to view a user account and update personal details     
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

//Initiate the user access script
require_once "phpUserClass/access.class.beta.php";
$user = new flexibleAccess();

$message ="";
//echo $_POST['username'];
//echo $_POST['email'];

//Process form if submitted to update a users details
if (!empty($_POST['username']) && !empty($_POST['email']) ){
  
  //Sanitize the user inputed data
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  //$email = filter_var($email, FILTER_VALIDATE_EMAIL);
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
      //$pwd = filter_var($_POST['pwd'], FILTER_SANITIZE_STRING);
      if ($user->updateProperty(array('username' => $username)) == 1) {
        $message .= "Username updated<br/>";
      } else {
        $message .= "Failed to update username <br/>";
      }
      if ($user->updateProperty(array('email' => $email)) == 1) {
        $message .= "Email updated<br/>";
      } else {
        $message .= "Failed to update email <br/>";
      }
      
      if (!empty($_POST['pwd']) && !empty($_POST['confirm'])) {
        $pwd = filter_var($_POST['pwd'], FILTER_SANITIZE_STRING);
        $confirm = filter_var($_POST['confirm'], FILTER_SANITIZE_STRING);
        //$pass_update = $user->updateProperty(array('password' => $pwd));
        //print_r($pass_update);
        //echo strlen($pwd );
        if ($pwd == $confirm && strlen($pwd) >= 6) {
          if ($user->updateProperty(array('password' => $pwd)) == 1) {
            $message .= "Password updated<br/>";
          } else {
            $message .= "Failed to update password <br/>";
          }
        } else {
          if (strlen($pwd) < 6) {
            $message .= "Password NOT updated because password is too short.<br/><br/>";
          } else {
            $message .= "Password NOT updated because they do not match.<br/>";
          }
        }
      }
  } else {
    $message = "New email address is not valid.<br/><br/>";
  }
}

$page = "My Account"; //used for page title in header.php
include('theme/header.php'); 
$user = new flexibleAccess();
?>


    <div id="nav">
      <div class="login">
        <ul class="inline">
          <?php 
            if ( $user->is_loaded() ){
              echo "<li class='username'>" . $user->get_property("username") . "</li>";
              //echo '<li class="logout"><a href="'.$_SERVER['PHP_SELF'].'?logout=1">logout</a></li>';
              echo '<li class="logout"><a href="index.php?logout=1">logout</a></li>';
            } else {
              //User is loaded
              echo "<li><a href='login.php'>Login</a></li>";
            }
          ?>
        </ul>
      </div><!--login-->
      <h1 class="title"><a href="index.php">Setlistr</a></h1>
      <div class="list-buttons">
        <ul class="inline">
          <li><a href="index.php">Back</a></li>
        </ul>
      </div>
    </div><!--nav-->
    
    
    <div class="active-list">
      <h3>Account Settings</h3>
    </div>
    <div class="user-column-left">
      
    <?php
    
              //gravatar
          $url = 'http://www.gravatar.com/avatar/';
          $email = $user->get_property("email");
          $username = $user->get_property("username");
          $default = 'monsterid';
          $size = 120;
           
          $grav_url = $url.'?gravatar_id='.md5( strtolower($email) ).
          '&default='.urlencode($default).'&size='.$size; 
          echo '<img class="avatar" src="'. $grav_url .'" />';
          //echo $email;
          //echo $username;
    ?>
          
          


    </div>
    <div class="user-column-right">
    <?php
    if ($message != NULL) {
       echo '<div class="message">' . $message . '</div>';
    }
    ?>
    <form action="user.php" method="post" id="account">
 
    <div class="field-container">
      <label for="edit-name">Username: <span class="form-required" title="This field is required.">*</span></label>
      <input name="username" id="edit-name" value="<?php echo $username; ?>" class="form-text required" type="text">
      <div class="description">Spaces are allowed; punctuation is not allowed except for periods, hyphens, and underscores.</div>
    </div>
    <div class="field-container">
      <label for="edit-mail">E-mail address: <span class="form-required" title="This field is required.">*</span></label>
      <input name="email" id="edit-mail" value="<?php echo $email; ?>" class="form-text required" type="text">
      <div class="description">A valid e-mail address. All e-mails from the system will be sent to this address. The e-mail address is not made public and will only be used if you wish to receive a new password or wish to receive certain news or notifications by e-mail.</div>
    </div>
    <div class="field-container">
      <div class="field-container">
        <label for="pwd">Password: </label>
        <input name="pwd" id="pwd" class="password" type="password">
      </div>
      <div class="field-container">
         <label for="confirm">Confirm Password</label>
         <input type="password" name="confirm" id="confirm" class="confirm" /> <div class="error-msg"></div>
      </div>
      <!--
      <div class="form-item confirm-parent" id="edit-pass-pass2-wrapper">
       <label for="edit-pass-pass2">Confirm password: </label>
       <input name="pass[pass2]" id="edit-pass-pass2" maxlength="128" size="25" class="form-text password-confirm" type="password"><span class="password-confirm">Passwords match: <span></span></span>
      </div>
      <div style="display: none;" class="password-description"></div>

       <div class="description">To change the current user password, enter the new password in both fields.</div>
       -->
    </div>

    <input type="submit" class="form-submit" value="Update" id="edit-submit" name="op">

      </form>
    </div>
      
<?php 
$password_page = TRUE; //used to initiate the password strength javascript
include('theme/footer.php'); 
?>
