<?php
/*
 *      visibility_form.php
 *      Theme file to display the form permission toggle
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

if ( $user->is_loaded() ) { ?>

  <?php 
  if (isset($lists)) { 
    if (isset($lists[$list_id]['is_public'])) { //this may not be set for someone who has created an account and bypassed saving a list.
      $is_public = $lists[$list_id]['is_public']; //0 = private, 1=public
      // echo $is_public;
    } else {
      $is_public = 0;
    }
  }
  ?>


  <form name="public-private" action="visibility.php" method="post" <?php if ($is_public == 0) { echo 'class="private"'; } ?>>
      <input type="checkbox" name="show" class="make-public" value="1" <?php if ($is_public == 1) { echo 'checked="checked"'; } ?>  onchange="this.form.submit();" /> 
      <?php if ($is_public == 0) { echo "Make public"; } else { echo "Public"; } ?> 
  <!--<input type="radio" name="show" value="0" <?php if ($is_public == 0) { echo "checked"; } ?>  onchange="this.form.submit();" /> Private
      <input type="radio" name="show" value="1" <?php if ($is_public == 1) { echo "checked"; } ?>  onchange="this.form.submit();" /> Public
   --> 
      <?php 
        if ($is_public == 1) {
          //echo ' - link: <a href="' . $host . 'public.php?list=' . $list_id . '">' . $host . 'public.php?list=' . $list_id . '</a>';
          echo ' - link: <a href="' . $host . 'list/' . $list_id . '">' . $host . 'list/' . $list_id . '</a>';
        }
      ?>
      <br />
      <input type="hidden" name="list" value ="<?php echo $list_id; ?>" />
  </form>

<?php } ?>
