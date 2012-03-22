<?php
/*
 *      footer.php
 *      Theme file to display the site footer
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
  </div><!--main-->
  
  <div id="footer-wrapper">
    <div id="footer">
      <div id="print-footer">
        <p>www.setlistr.co.uk - Free software to make, save and print set lists.</p>
      </div>
      <div class="column">
        <h3>About</h3>
        <p>Make, save and print set lists.<br/><br/></p>
        <p>Make and print without having to login<br/><br/></p>
        <p>To save set lists for future use/edits, <a href="<?php echo $host; ?>save.php<?php if(isset($list_id)) { echo "?list=" . $list_id; } ?>">create a free account</a>.</p>
        <p><br/>Free Software from <a href="https://twitter.com/caprenter">@caprenter</a><br/><a href="https://github.com/caprenter/Setlistr">https://github.com/caprenter/Setlistr</a></p>
      </div>
      <div class="column">
      <h3>Contact</h3>
        <p>Email: caprenter@gmail.com</p>
        <p>Twitter: <a href="https://twitter.com/caprenter">@caprenter</a></p>
      </div>
      <div class="column">
        <?php 
          echo '<h3>User tools</h3>';
          if ( isset($user) && $user->is_loaded() && isset($list_id) && $list_id !=0) {
            echo '<p><a href="' . $host . 'export.php?format=csv&amp;list=' . $list_id . '">Export this list as csv</a></p>';
            echo '<p><a href="' . $host . 'export.php?format=xml&amp;list=' . $list_id . '">Export this list as XML</a></p>';
          } else {
            echo "Logged in users can import and export set lists, clone and share lists, and keep an archive of old lists.";
          }
        ?>
      </div>
      <div class="column">
        <h3>Developers</h3>
        <p><a href="<?php echo $host; ?>api.php">Setlistr API</a></p>
      </div>
    </div>
  </div>

<div style="clear:both; min-height:20px;"></div>
</div><!--wrapper-->
<!-- Including our scripts -->
<?php 
  if (isset($include_javascript) && $include_javascript== TRUE) {
?>
<!--<script type="text/javascript" src="javascript/jquery.min.js"></script>
<script type="text/javascript" src="javascript/jquery-ui.min.js"></script>-->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $host; ?>javascript/jquery.jeditable.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo $host; ?>javascript/script.js"></script>
<?php 
  }
?>
<?php //Password strength tester
if (isset($password_page) && $password_page == TRUE) { ?>
  <!--<script type="text/javascript" src="javascript/jquery.min.js"></script>-->
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo $host; ?>javascript/password_match.js"></script>
  <script type="text/javascript" src="<?php echo $host; ?>javascript/jquery.pstrength-min.1.2.js"></script>
  <script type="text/javascript">
  $(function() {
  $('.password').pstrength();
  });
  </script>
<?php } ?>

<!--Google Analytics-->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-717518-11']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
