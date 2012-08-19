<?php
/*
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
require_once('../settings.php');
//Initiate the user access script
require_once "../phpUserClass/access.class.beta.php";
$user = new flexibleAccess();


  //Start output to the screen
  $page = "Widget"; //used for page title in header.php
  include('../theme/header.php'); 
 ?>

    <!--<div class="list-buttons">
      <ul class="inline">
        <li><a id="newList1" href="<?php echo $host; ?>index.php?list=new">New</a></li>
      </ul>
    </div>-->
    <div class="workspace">
      <div class="active-list">
        <h2 style="clear:both">Setlistr Widget</h2>
      </div>

      <!--<p class="note">The todos are flushed every hour. You can add only one in 5 seconds.</p>-->
      <div id="widget-text">
        <div class="column-left">
          <p>You can create a widget for your own website to show any public set list.
          Use the <a href="<?php echo $host; ?>api.php">Setlistr API</a> to customise your widget to give you data you want.</p>
          <p><br/>Using the code below, produces the widget on the right. You must supply a list of parameters as shown:</p>
       
        </div><!--end column left-->
        <div class="column-right">
          <script type="text/javascript">
            var parameters="list=24&username=caprenter&songs=in&limit=2";
          </script>
          <script src="http://www.setlistr.co.uk/widget/script.js"  type="text/javascript"></script>
          <div id="setlistr-widget-container"></div>
        </div>
      </div>
      <div class="code-example">
      <p>Copy and paste the code below into your webpage.</p>
       <pre>
          <?php 
            $code = '
<script type="text/javascript">
  var parameters="list=24&username=caprenter&songs=in&limit=2";
</script>
<script src="http://www.setlistr.co.uk/widget/script.js"  type="text/javascript"></script>

<div id="setlistr-widget-container"></div>';
            echo htmlspecialchars($code);
          ?>
         </pre>
      </div>
    </div><!--end workspace-->
<?php 
  $include_javascript = FALSE; //Because on some pages we don't want to include it!
  include('../theme/footer.php'); 
?>
