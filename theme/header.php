<?php
/*
 *      header.php
 *      Theme file to display the site header     
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Setlistr | Manage your setlists | <?php echo $page; ?></title>

<!--GoogleFonts-->
<!--<link href='http://fonts.googleapis.com/css?family=Cabin&v1' rel='stylesheet' type='text/css'>-->
<!--<link href='http://fonts.googleapis.com/css?family=Chango' rel='stylesheet' type='text/css'>-->

<!-- Including the jQuery UI Human Theme -->
<link rel="stylesheet" href="<?php echo $host; ?>css/jquery-ui.css" type="text/css" media="all" />

<!-- Our own stylesheet -->
<link rel="stylesheet" type="text/css" href="<?php echo $host; ?>css/styles.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $host; ?>css/print.css" media="print" />

<script type="text/javascript">
function show_confirm()
{
var r=confirm("Press a button!");
if (r==true)
  {
  alert("You pressed OK!");
  }
else
  {
  alert("You pressed Cancel!");
  }
}
</script>
</head>

<body>
<div id="header">
    <div id="banner">
      
      
      
      <div id="logo">
        <h1 class="title">
          <a href="<?php echo $host; ?>">Setlistr</a>
        </h1>
      </div><!--logo-->
      
      <div id="login-area">
        <?php 
          if ($page == "API Example" || $page == "Widget") {
            include('../theme/login_html.php'); 
          } else {
           include('theme/login_html.php'); 
          }
        ?>
      </div><!--login-->
      
      <div id="nav">
        <ul class="inline">
          <li><a href="<?php echo $host; ?>">Home</a></li>
          <?php 
            if (isset($user) && $user->is_loaded() ) { 
          ?><li>
            <a href="<?php echo $host ?><?php echo $user->get_property("username");; ?>">My Lists</a>
          </li>
          <?php } ?>
          <!--<li><a href="<?php echo $host; ?>about.php">About</a></li>
          <li><a href="<?php echo $host; ?>contact.php">Contact</a></li>-->
        </ul>
      </div><!--nav-->
     
    </div><!--banner-->
</div><!--header-->
<div id="wrapper">
  <div id="main">
