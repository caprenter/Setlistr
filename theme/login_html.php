<?php
/*
 *      login_html.php
 *      Theme file to display the login/logout html on pages     
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
?>
        <ul class="inline">
          <?php 
            //Drop down select list of all users set lists. For logged in users only.
            if (isset($lists)) {
                if (count($lists)>1) { //only show if more than one list available
                  echo'<li><div id="all-lists">';
                  echo '<form method="post" action="index.php">';
                  echo '<select name="list" onchange="this.form.submit();">';
                  foreach ($lists as $id=>$value) {
                    if ($value['name'] == "New List (click to edit title)") {
                      $value['name'] = "New List";
                    }
                    echo '<option value="' . $id . '"' . ($id == $list_id ? "selected='selected'":"") . '>' . $value["name"] .'</option>';
                  }
                  echo '</select>';
                  echo '<input type="submit" value="Select List" style="display:none">';
                  echo '</form></div></li>';
              }
            }
          ?>

          
          <?php 
          //Display user logged in stuff or 'login'
          if ( $user->is_loaded() ){
            //gravatar
            $url = 'http://www.gravatar.com/avatar/';
            $email = $user->get_property("email");
            $default = 'monsterid';
            $size = 40;
             
            $grav_url = $url.'?gravatar_id='.md5( strtolower($email) ).
            '&default='.urlencode($default).'&size='.$size; 
         
            echo "<li class='username'><a href='user.php'>" . $user->get_property("username") . "</a></li>";
            echo '<li class="logout"><a href="'.$_SERVER['PHP_SELF'].'?logout=1">logout</a></li>';
            echo '<li><a href="user.php"><img class="avatar" src="'. $grav_url .'" /></a></li>';          
          } else {
            //User is not loaded
            echo "<li><a href='login.php'>Login</a></li>";
          }
          ?>

        </ul><!--ul.inline-->
