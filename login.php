<?php
/*
 *      login.php
 *      This is the routine that logs a user into the site. 
 *      If they have been working on a list, then it will save that list to the user.
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
Login with php user class
http://phpUserClass.com
*/
//echo $_GET['list'];

//If user has been working on a list while logged out...Pass the list id via the URL
if (isset($_GET['list'])) {
  //this is passed from the main set list page. We need to post it back
  $list_id = filter_var($_GET['list'], FILTER_SANITIZE_NUMBER_INT);
  if (!filter_var($list_id, FILTER_VALIDATE_INT)) {
    unset($list_id);
  }
  //echo $list_id;
}
 
//Initiate the user access script
require_once 'phpUserClass/access.class.beta.php';
$user = new flexibleAccess();

if ( isset($_GET['logout']) && $_GET['logout'] == 1 ) {
	$user->logout('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
}

//echo $_POST['list'];

if ( !$user->is_loaded() ) {
	//Login stuff:
	if ( isset($_POST['uname']) && isset($_POST['pwd'])) {
        //Mention that we don't have to use addslashes as the class do the job
        //but I guess this won't hurt!
        $_POST['uname'] = filter_var($_POST['uname'], FILTER_SANITIZE_STRING);
        $_POST['pwd'] = filter_var($_POST['pwd'], FILTER_SANITIZE_STRING);
        if (isset($_POST['remember']) && $_POST['remember'] != 1 || !isset($_POST['remember'])) {
          $_POST['remember'] = FALSE;
        }
        
        if ( !$user->login($_POST['uname'],$_POST['pwd'],$_POST['remember'] )) {
          $errors =  'Woah!<br/>You won\'t get in with that username and password';
          if (!empty($_POST['list'])) {
            $list_id = filter_var($_POST['list'], FILTER_SANITIZE_NUMBER_INT);
            if (!filter_var($list_id, FILTER_VALIDATE_INT)) {
              unset($list_id);
            }   
          }           
        } else {
          //user is now loaded
          //header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
          
          $user_id = $user->get_property("userID");

          //Assign the list to the user.
          if (!empty($_POST['list'])) {
            $list_id = filter_var($_POST['list'], FILTER_SANITIZE_NUMBER_INT);
            if (filter_var($list_id, FILTER_VALIDATE_INT)) {
               //echo $list_id;
               //die;
              //mysql_query("INSERT INTO `lists` (user_id) VALUES (" .$userID. ") WHERE list_id = " . $list_id);
              $result = mysql_query("UPDATE `lists` SET user_id=" . $user_id . "  WHERE list_id = " . $list_id. " AND user_id = 0");
            }
          }
          //print_r($result);
       //die;
        //We're looged in
        //Old list has been saved
        //Go back to index.php
        header('Location: ' . $host);
	  }
	}
  
 $page = "Login"; //used for page title in header.php
 include('theme/header.php'); 
	print('<div class="workspace">
        <div class="active-list">
          <h2>Login</h2>
        </div>');
  if (isset($errors)) {
    echo '<div class="errors">' . $errors . '</div>';
  }
	print('<form class="login" method="post" action="'.$_SERVER['PHP_SELF'].'">
          <div class="field-container">
            <label for="uname">Username</label><br/>
            <input type="text" name="uname" id="uname"/>
          </div>
          <div class="field-container">
            <label for="password">Password</label><br/>
            <input type="password" name="pwd" id="password"/>
          </div>
          <label for="remember">Remember me?</label> <input type="checkbox" name="remember" id ="remember" value="1" /><br /><br />'
        );
  if (isset($list_id)) {
    echo '<input type="hidden" name="list" value="'.  $list_id .'"/>';
  }
  print('<input type="submit" value="Login" />
        </form><br /><br />
        <p class="notice">Forgotten password? <a href="' . $host . 'new_pass.php">Send me a new password</a></p><br/>
        <p class="notice">Need to register? <a href="' . $host . 'save.php">Create a new account</a></p><br/><br/>
        </div>'
        ); 
      
} else {
  //User is loaded. Redirect to home page
  header('Location: ' . $host);
}
?>

<?php include('theme/footer.php'); ?>
