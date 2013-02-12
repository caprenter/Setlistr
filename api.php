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
require_once('settings.php');
//Initiate the user access script
require_once "phpUserClass/access.class.beta.php";
$user = new flexibleAccess();


  //Start output to the screen
  $page = "Api"; //used for page title in header.php
  include('theme/header.php'); 
 ?>

    <!--<div class="list-buttons">
      <ul class="inline">
        <li><a id="newList1" href="<?php echo $host; ?>index.php?list=new">New</a></li>
      </ul>
    </div>-->
    <div class="workspace">
      <div class="active-list">
        <h2 style="clear:both">Setlistr API</h2>
      </div>

      <!--<p class="note">The todos are flushed every hour. You can add only one in 5 seconds.</p>-->
      <div id="api-text">
        <div class="column-left">
          <p>The Setlistr API gives you access to setlist data. <br/><br/>
          All public lists are available via the API. <br/>
          With an API key you can also retrieve your own non-public' setlists.</p>
          <p><br/>You can call</p>
          <ul>
            <li>an individual public setlist, or</li>
            <li>list of all public setlists</li>
            <li>all of your personal lists (public and private with an API key)</li>
          </ul>
          
          <p>Data will be return in either XML or JSON format</p>
          <p><br/>An example file that calls the API to show data can be found at:<br/> <a href="<?php echo $host . "api/show.php"; ?>"><?php echo $host . "api/show.php"; ?></a></p>
          <br/>
          <h3 class="api-option">Setlistr Widget</h3>
          <p>A javscript widget that can be embeded into webpages is also available.<br/>
          See: <a href="<?php echo $host; ?>widget/">Setlistr Widget</a></p>
        </div><!--end column left-->
        <div class="column-right">
          <h3 class="api-option">The list parameter</h3>
          <p>All calls to the API must include a 'list' parameter.<br/>
          Values are either 'all' or an integer (corresponding to a list id). See below.</p>
          <br/>
          <h3 class="api-option">All public lists</h3>
          <p>http://www.setlistr.co.uk/api/?list=all</p>
          <h4 class="api-option">Further parameters</h4>  
          <p>&amp;username=&lt;a_valid_setlistr_username&gt;<br/>
          (Returns all public lists by that user. Can ONLY be used with list=all option)</p>
          <br/>
          <h3 class="api-option">To get data on a single public list:</h3>
          <p>http://www.setlistr.co.uk/api/?list=(integer)<br/>e.g. http://www.setlistr.co.uk/api/?list=55<br/></p>
          <h4 class="api-option">Further parameters</h4> 
          <p>&amp;songs=in/out/all (i.e. songs 'in' the set, not in ('out') or, (default) all)</p>
          <p>&amp;breaks=yes/no (include set breaks in the output - default is yes)</p>
          <br/>
          
          <h3 class="api-option">Using your API key</h3>
          <p>With an API key you can retrieve your 'non-public' setlists as well.</p>
          <p>In the returned data the 'privacy' field is set to:<br/>
           0 for private lists and<br/>
           1 for public lists.</p>
          <p>When logged in, you will find your API key on your <a href="<?php echo $host; ?>user.php">user page</a>.</p><br/>
          <p>&amp;username=&lt;your_setlistr_username&gt;&amp;key=&lt;your_key&gt;</p>
          <br/>
          <h3 class="api-option">Format parameters</h3>
           <p>&amp;format=xml/json (default is json)</p>
           <br/>
        </div>
      </div>
      <div id="changelog">
      <h3>Changelog</h3>
      <h4>Commit <a href="https://github.com/caprenter/Setlistr/commit/659ac7fb8b989158093820e4d8390db17b3897ab">659ac7</a> 8th Jan. 2013</h4>
      <ul>
        <li>Changes 'name' to 'title' in all returned list data. Breaks backwards compatability</li>
        <li>Adds 'user'name' and 'list_id' to all returned list data</li>
      </ul>
      <h4>Commit <a href="https://github.com/caprenter/Setlistr/commit/07aff16fdbb9c83aaa781e2d384a4ad59ec1a097">c1a097</a> 12th Jan. 2013</h4>
      <ul>
        <li>Adds public/private information about lists to calls that send a username and api key</li>
      </ul>
      </div>
    </div><!--end workspace-->
<?php 
  $include_javascript = FALSE; //Because on some pages we don't want to include it!
  include('theme/footer.php'); 
?>
