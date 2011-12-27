<?php
/*
 *      visibility.php
 *      Change the visibility of a list between public/private    
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

//Check user is logged in
if ( $user->is_loaded() ) {
  
  //Process form. Check and sanitise variables
  //if (!empty($_POST['show']) && !empty($_POST['list'])){
  if (!empty($_POST['list'])) {
    //echo $_POST['list']; 
    if (!isset($_POST['show'])) {
      $show = 0;
    } else {
      $show = filter_var($_POST['show'], FILTER_SANITIZE_NUMBER_INT);
    }
    $list_id = filter_var($_POST['list'], FILTER_SANITIZE_NUMBER_INT);
    //echo $list_id;
    //echo $show;
    //var_dump(filter_var($show, FILTER_VALIDATE_INT));
    //die;
    if (filter_var($list_id, FILTER_VALIDATE_INT) && ($show == 0 || $show ==1)) {
        $query = "UPDATE lists SET public = " . $show . "  WHERE list_id = " . $list_id;
        //echo $query;
        //die;
        mysql_query("UPDATE lists SET public = " . $show . "  WHERE list_id = " . $list_id );
    }
  }
  //Redirect to home page, logged in, and with the same list.
  header('Location: index.php');
}

//Redirect to home page, logged in, and with the same list.  
header('Location: index.php');

