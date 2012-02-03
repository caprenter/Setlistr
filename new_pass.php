<?php
/*
 *      new_pass.php
 *      Allows a user to recover if they have forgotten their password.
 *      Sends a one-time, time limited, login url, if a supplied email address matches one in the 
 *      database. 
 *      
 *      Also authenticates requests for new passwords, and requires a user to set a new password
 *      if an authenticated url is received. 
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

//I know this is not great. But a bit of html we will need to re-use...
$submit_email_form ='<form class="login" method="post" action="'.$host.'new_pass.php">
                          <label for="email">Email</label> <input type="text" name="email" id="email"/><br /><br />
                          <input type="submit" value="Submit" />
                      </form><br /><br />
                      <p class="notice">&#8656; <a href="' . $host . '">Home</a></p>'; 



require_once 'phpUserClass/access.class.beta.php';
$user = new flexibleAccess();


//If user is already logged in redirect them to the home page
if ($user->is_loaded()) {
  //Go back to index.php
  header('Location: ' . $host);
} 

//User is not logged in below here...

//**Case: We have got here from a specially crafted URL sent to a user.
//This URL has been verified. A new password form has been submitted. 
//If a new password has been supplied then update the users password field.
//We need to be really careful that this form is not spoofed so we check again the $key is assigned to the user id
if (isset($_POST['pwd']) && isset($_POST['confirm']) && isset($_POST['user']) && isset($_POST['key'])) {
  if (!empty($_POST['pwd']) && !empty($_POST['confirm'])) {
    $pwd = filter_var($_POST['pwd'], FILTER_SANITIZE_STRING);
    $confirm = filter_var($_POST['confirm'], FILTER_SANITIZE_STRING);
    $user_id = filter_var($_POST['user'], FILTER_SANITIZE_NUMBER_INT);
    $key = filter_var($_POST['key'], FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
    $key = filter_var($key, FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_LOW);

    if ($pwd == $confirm && strlen($pwd) >= 6 && strlen($key) == 50) {
      //Check the recovery password table to see if we can update this users password.
      include("functions/connect.php");
      $query = sprintf("SELECT * FROM recover_password WHERE password = '%s'",$key);
      //echo $query; die;
      $result = mysql_query($query);
      if (mysql_num_rows($result) !=0) {
        //echo "result!";
        $row = mysql_fetch_assoc($result);
        if ($user_id == $row['user_id'] ) { //IMPORTANT this helps us check that we're not just updating the correct user and not just any old record
          $recovery_id = $row['recovery_id'];
          //Remove the record from the table as this is a one time login only!
          //$record_id = 1; //debug
          // echo $user_id;
          //echo $pwd;
          // die;
          //DEBUG INFO: This line deletes the one time access URL from the database.
          //It may help to comment it out if you are trying to debug this part of the appication
          //as you can keep trying the same URL
          $query = sprintf("DELETE FROM recover_password WHERE recovery_id = %d",$recovery_id);
          //echo $query;
          mysql_query($query);
         
          //Now update the users password
          if ($user->update_password($user_id,$pwd)) {
            $page = "Password updated";
            $html = '<h2 class="user-action">Your password has been sucessfully updated</h2>';
            $html .= '<p class="notice">You can now <a href="' . $host . 'login.php">login</a></p><br/>';

          } else {
            $page = "Password updated failed"; //internal error
            $html = '<h2 class="user-action">Sorry</h2>';
            $html .= '<p class="notice">Failed to update password </p><br/>';
            $html .= '<p class="notice">As site security is important to us and your one-time login attempt has now been used, you will need to re-supply an email address.</p><br/>';
            $html .= '<h2 class="user-action">Try again?</h2>';
            $html .= $submit_email_form;

          }
        }
      } 
    } else {
      if (strlen($pwd) < 6) {
        $page = "Password updated failed"; //too short
        $html = '<h2 class="user-action">Sorry</h2>';
        $html .= '<p class="notice">Password NOT updated because password is too short.</p><br/>';
        $html .= '<p class="notice">As site security is important to us and your one-time login attempt has now been used, you will need to re-supply an email address.</p><br/>';
        $html .= '<h2 class="user-action">Try again?</h2>';
        $html .= $submit_email_form;
      } else {
        $page = "Password updated failed"; //don't match
        $html = '<h2 class="user-action">Sorry</h2>';
        $html .= '<p class="notice">Password NOT updated because they do not match.</p><br/>';
        $html .= '<p class="notice">As site security is important to us and your one-time login attempt has now been used, you will need to re-supply an email address.</p><br/>';
        $html .= '<h2 class="user-action">Try again?</h2>';
        $html .= $submit_email_form;
      }
    }
  } else {
    //either password or confirm fields were empty
    $page = "Password updated failed"; //empty fields
    $html = '<h2 class="user-action">Sorry</h2>';
    $html .= '<p class="notice">Password NOT updated because you sent empty data.</p><br/>';
    $html .= '<p class="notice">As site security is important to us and your one-time login attempt has now been used, you will need to re-supply an email address.</p><br/>';
    $html .= '<h2 class="user-action">Try again?</h2>';
    $html .= $submit_email_form;
  }


//**Case: A user has got here after clicking the link in their email and now we give them a from to reset their password
} elseif (isset($_GET['key'])) {
  //Check and sanitize the key
  $key = filter_var($_GET['key'], FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
  $key = filter_var($key, FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_LOW);
  //echo $key; die;
  if (strlen($key) == 50 ) {
    //Go see if we have it in our recover_password table and return the user_id
    include("functions/connect.php");
    $query = sprintf("SELECT * FROM recover_password WHERE password = '%s'",$key);
    //echo $query; die;
    $result = mysql_query($query);
    if (mysql_num_rows($result) !=0) {
      //echo "result!";
      $row = mysql_fetch_assoc($result);
      $issued_time = $row['issued'];
      $recovery_id = $row['recovery_id'];
      $user_id = $row['user_id'];
      mysql_close($link);
      //Now check the link has not expired
      if (strtotime($issued_time) > time() - 60*60*24) {
        //The link is less than a day old - proceed
        //Present a form to allow the user to reset their password:
        $page = "Set new password"; 
        $html = '<h2 class="user-action">Hello. Please enter a new password below</h2>';
        $html .=' <form class="login new_pass" method="post" action="'.$host.'new_pass.php" />
                  <div class="field-container">
                      <label for="pwd">New Password</label>
                      <input type="password" class="password" name="pwd" id="pwd" />
                  </div>
                  <div class="field-container">
                      <label for="confirm">Confirm Password</label>
                      <input type="password" name="confirm" id="confirm" class="confirm" /> <div class="error-msg"></div>
                  </div>
                  <input class="submit register" type="submit" value="Submit" />
                  <input name="user" type="hidden" value="' . $user_id . '" />
                  <input name="key" type="hidden" value="' . $key . '" />
                </form>';
        
        $password_page = TRUE; //used to initiate the password strength javascript
        //echo $user_id; echo $issued_time; echo $recovery_id; die;
      } else {
        $page = "One-time login expired"; 
        $html = '<h2 class="user-action">Sorry</h2>';
        $html .= '<p class="notice">The one-time login link has expired.</p><br/>';
        $html .= '<h2 class="user-action">Try again?</h2>';
        $html .= $submit_email_form;
      }
    } else {
      //The key is not in the table
      $page = "One-time login error";
      $html = '<h2 class="user-action">Sorry</h2>';
      $html .= '<p class="notice">There is an error with your one-time login link.</p><br/>';
      $html .= '<h2 class="user-action">Try again?</h2>';
      $html .= $submit_email_form;
    }
      
  } else {
    //Problem with the URL of the one-time login (not 50 chars)
    //Simply show the recovery form
    $page = "Request new password"; //used for page title in header.php
     
    //$html = '<h1 class="title"><a href="' . $host . '">Setlistr</a></h1>';
    $html = '<h2 class="user-action">Forgotten your password?</h2>';
    $html .= '<p class="notice">Tell us your email address and we\'ll send you instructions about how to reset your password.</p><br/>';
    $html .= $submit_email_form;
  }  
 
//**Case: Someone has supplied an email address - hopefully because they are real and need a new password!  
} elseif (isset($_POST['email'])) {
  //Sanitise user input 
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $page = "Request new password - invalid email address"; 
    $html = '<h2 class="user-action">Sorry</h2>';
    $html .= '<p class="notice">The email address you supplied doesn\'t seem to be a valid email address.</p>';
    $html .= '<p class="notice">Please email us at <a href="mailto:' . $site_email .'">' . $site_email . '</a> if we\'re wrong.</p><br/>';
    $html .= '<h2 class="user-action">Try again?</h2>';
    $html .= $submit_email_form;
  } else {
    //Check to see if we have the email in our system
    //See if this valid email is assigned to a user in the system and get their id
    //echo "getting user id";
    $user_id = $user->get_user_by_email($email); //returns a user id or FALSE 
    if ($user_id) {
     //Generate one time, time limited login url
     $password = $user->randomPass(50,"1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"); //use the access class password generator
     //make a connection independently cos userClass restricts the tables we can get at
     include("functions/connect.php");
     //Save a password and time-stamp against this user
     $query = sprintf("INSERT INTO recover_password (user_id,password) VALUES (%d,'%s')",$user_id,$password);
     //echo $query;
     mysql_query($query);
     //Send the user an email, with link in it notifying them of the reset password.
     $link = $host . "new_pass.php?key=" . $password;
     //echo $link; //die;
     //Print a message to the screen
     //Email address is user input but should have been suffciently sanitized by now to send to the screen
     $page = "Request new password - valid email address"; 
     $html = '<h2 class="user-action">Email found</h2>';
     //$html .='<p class="notice">An email has been sent to: ' . $email . '</p>';
     //$html .='<p class="notice">Follow the instructions we have sent you to get access to your account.</p>';
     //send email
     $to = $email;
     $subject = "Setlistr.co.uk  - Password Reset Request";
     $body = "Hello\n\nSomone has requested a new password for this email address at Setlistr.co.uk.\n\nIf you have not done so, then just ignore this message.";
     $body .= "\n\nTo set a new password follow the link below.\n\n";
     $body .= "This is a one-use link to reset your password. It will expire in 24hours.\n\n";
     $body .= $link;
     $body .= "\n\nAny problems/feedback/concerns please let us know: caprenter@gmail.com";
     $headers = "From: " . $site_email;
     if (mail($to, $subject, wordwrap($body,70), $headers)) {
        $html .='<p class="notice">An email has been sent to: ' . $email . '</p>';
        $html .='<p class="notice">Follow the instructions we have sent you to get access to your account.</p>';
     } else {
        $html .= '<h2 class="user-action">Sorry</h2>';
        $html .='<p class="notice">We were unable to send you an email. Please contact us at <a href="mailto:' . $site_email . '">' . $site_email . '</a></p>';
     }
     //$html .= $body;
    } else { 
      //email does not exist in the system
      $page = "Request new password - email not found"; 
      $html = '<h2 class="user-action">Sorry</h2>';
      $html .= '<p class="notice">That email address is not in the system.</p><br/>';
      $html .= '<h2 class="user-action">Try again?</h2>';
      $html .= $submit_email_form;
    }
      
    //echo $user_id; die;
  }
  
//**Case: Visiting the page with no extra data. Probably clicked the link to get a new password
} else {
  //show form to request new password
  $page = "Request new password"; //used for page title in header.php
   
	//$html = '<h1 class="title"><a href="' . $host . '">Setlistr</a></h1>';
  $html = '<h2 class="user-action">Forgotten your password?</h2>';
  $html .= '<p class="notice">Tell us your email address and we\'ll send you instructions about how to reset your password.</p><br/>';
  $html .= $submit_email_form;

}

?>
<?php
include('theme/header.php');
  echo '<h1 class="title"><a href="' . $host . '">Setlistr</a></h1>';
  if (isset($html)) {
    echo $html;
  }
include('theme/footer.php'); ?>
