<?php
/*
 *      404.php
 *      This is a custom page to deal with pages that are not found. 
 *      The image used:
 *      Creator: Kristianus Kurnia / noskill1343
 *      www.noskill1343.com 
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

$page = "Page not found"; //used for page title in header.php
include('theme/header.php'); 
//echo '<h1 class="title"><a href="' . $host . '">Setlistr</a></h1>';
?>
<div id="nav">
  <h1 class="title"><a href="index.php">Setlistr</a></h1>
  <div class="list-buttons">
    <ul class="inline">
      <li><a id="newList1" href="' . $host . '">Home</a></li>
    </ul>
  </div>
</div><!--nav-->
<div class="not-found-message">
  <h2 class="user-action">Sorry</h2>
  <p>The page you are looking for could not be found.</p> 
  <p>If you think it's something we should fix please mail <a href="mailto:caprenter@gmail.com">caprenter@gmail.com</a></p>
  <div class="credit">
    <p>Image by: Kristianus Kurnia - <a href="http://www.noskill1343.com">www.noskill1343.com</a></p>
  </div>
</div>

<?php include('theme/footer.php'); ?>
