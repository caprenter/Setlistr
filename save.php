<?php
/*
 *      save.php
 *      When a user saves a list, then  we register them and log them in.
 *      Otherwise lists are saved as changes are made
 * 
 *      This is heavily based on the example script from http://phpUserClass.com   
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
/*
* When a user saves a list, then  we register them and log them in.
* Otherwise lists are saved as changes are made
*/

//Pass the list id via the URL
if (isset($_GET['list'])) {
  //this is passed from the main set list page. We need to post it back
  $list_id = filter_var($_GET['list'], FILTER_SANITIZE_NUMBER_INT);
  if (!filter_var($list_id, FILTER_VALIDATE_INT)) {
    unset($list_id);
  }
  //echo $list_id;
}

if (isset($_POST['list'])) {
  //this is passed from the main set list page. We need to post it back
  $list_id = filter_var($_POST['list'], FILTER_SANITIZE_NUMBER_INT);
  //$list_id = filter_var($list_id, FILTER_VALIDATE_INT); 
  //echo $list_id;
}

//Process form once submitted 
if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['pwd']) && !empty($_POST['confirm'])){
  //Register user:
  
  //Sanitize the user inputed data
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $email = filter_var($email, FILTER_VALIDATE_EMAIL);
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
      $pwd = filter_var($_POST['pwd'], FILTER_SANITIZE_STRING);
      $confirm = filter_var($_POST['confirm'], FILTER_SANITIZE_STRING);
      //$pwd = $_POST['pwd'];
      if ($pwd == $confirm && strlen($pwd) >=6 ) {
          require_once 'phpUserClass/access.class.beta.php';
          $user = new flexibleAccess();
          //The logic is simple. We need to provide an associative array, where keys are the field names and values are the values :)
          $data = array(
            'username' => $username,
            'email' => $email,
            'password' => $pwd,
            'active' => 1
          );
          $userID = $user->insertUser($data);//The method returns the userID of the new user or 0 if the user is not added
          if ($userID==0) {
            $errors = 'Sorry. We could not create an account with those details.<br/>Please try a different username and/or email address.';//user is allready registered or something like that
            if (!empty($_POST['list'])) {
                $list_id = filter_var($_POST['list'], FILTER_SANITIZE_NUMBER_INT);
                if (!filter_var($list_id, FILTER_VALIDATE_INT)) {
                  unset($list_id);
                }   
            } 
          } else {
            //echo 'User registered with user id '.$userID;
          
            //Assigne the list to the user.
            if (isset($_POST['list'])) {
              $list_id = filter_var($_POST['list'], FILTER_SANITIZE_NUMBER_INT);
              if (filter_var($list_id, FILTER_VALIDATE_INT)) {
               // echo $list_id;
                //When we assign list to user make sure exisiting owner of list has user_id = 0!!
                mysql_query("UPDATE `lists` SET user_id=" . $userID . "  WHERE list_id = " . $list_id . " AND user_id = 0");
              }
            }
            //Redirect to home page, logged in, and with the same list.
             header('Location: index.php');
          }
      } else {
        if (strlen($pwd) < 6 ) {
          $errors = "Password is too short.<br/><br/>";
        } else {
          $errors = "Passwords do not match.<br/><br/>";
        }
      }
  } else {
    $errors = "Email address is not valid.<br/><br/>";
  }
} else {
    if($_SERVER['REQUEST_METHOD'] == "POST") {
      $errors = "You must fill in all the fields.<br/><br/>";
    }
}

$page = "Save List"; //used for page title in header.php
include('theme/header.php'); 
echo '<h1 class="title"><a href="' . $host . '">Setlistr</a></h1><h2 class="user-action">Register to save your lists.</h2>';




if (isset($errors)) {
    echo '<div class="errors">' . $errors . '</div>';
  }
	echo'<form class="login" method="post" action="'.$_SERVER['PHP_SELF'].'">
  <div class="field-container">
      <label for="username">Username</label><br/>
      <input type="text" name="username" id="username"/>
      <div class="description">Spaces are allowed; punctuation is not allowed except for periods, hyphens, and underscores.</div>
  </div>
	<div class="field-container">
     <label for="email">Email</label><br/>
     <input class="email" type="text" name="email" id="email"/>
           <div class="description">A valid e-mail address. All e-mails from the system will be sent to this address. The e-mail address is not made public and will only be used if you wish to receive a new password or wish to receive certain news or notifications by e-mail.</div>
  </div>    
  <div class="field-container">
      <label for="pwd">Password</label><br/>
      <input type="password" class="password" name="pwd" id="pwd" />
  </div>
  <div class="field-container">
      <label for="confirm">Confirm Password</label><br/>
      <input type="password" name="confirm" id="confirm" class="confirm" /> <div class="error-msg"></div>
  </div>';

   
   
    if (isset($list_id)) {
      echo '<input type="hidden" name="list" value="'.  $list_id .'"/>';
    }

echo '<input class="submit register" type="submit" value="Register" />
	</form>';
 echo '<br/><br/><p class="notice">Already got an account? ';
 if (isset($list_id)) {
      echo '<a href="' . $host . 'login.php?list=' . $list_id . '">';
    } else {
      echo '<a href="' . $host . 'login.php">';
    }
  echo'Login</a> (We\'ll save your list if you\'ve been working on one)</p><br/>';
  echo'<p class="notice">&#8656; <a href="' . $host . '">Back</a></p>'
?>
<?php 
$password_page = TRUE; //used to initiate the password strength javascript
include('theme/footer.php'); 
?>
