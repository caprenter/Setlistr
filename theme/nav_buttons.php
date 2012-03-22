<div class="column-left">
<ul class="inline">
  <li><a id="newList1" href="index.php?list=new">New</a></li>
  
  <?php if ( !$user->is_loaded() ) { ?>
    <li><a id="saveList" href="save.php?list=<?php echo $list_id; ?>">Save</a></li>
  <?php } else { ?>
  <li><a href="clone.php?list=<?php echo $list_id; ?>">Clone</a></li>
  <li><a href="import.php">Import</a></li>
  <li><a onclick="return confirm('Are you sure you want to delete this list?');" id="deleteList" href="delete.php?list=<?php echo $list_id; ?>">Delete</a></li>
  <?php } ?>

</ul>
</div>
<div class="column-right">
<ul class="inline">
          <?php 
            //Drop down select list of all users set lists. For logged in users only.
            if (isset($lists)) {
                if (count($lists)>1) { //only show if more than one list available
                  echo'<li><div class="your_lists">Select list:</div><div id="all-lists">';
                  echo '<form method="post" action="index.php" id="list-of-lists">';
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
          </ul>
</div>
