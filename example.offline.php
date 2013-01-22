<?php
/*
 *      offline.php
 *      This is a custom page to deal with site problems. 
 *      Currently it is called from phpUSerClass/access.class.beta.php when the site fails
 *      to connect to the database.
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

$page = "Off Line"; //used for page title in header.php
include('theme/header.php'); 
?>
<div class="workspace">
  <div class="active-list">
        <h2>Sorry</h2>
  </div>
  <div class="offline-message">Setlistr is currently down for a bit of tweaking.<br/> We'll be back up again shortly.</div>
</div>
<?php include('theme/footer.php'); ?>
