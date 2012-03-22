<?php
/*
 *      todo.class.php
 *      This is the Ajax function that deals with all the list sorting. 
 *      It is now also used when importing lists - using the public static functions
 *      There are only a few modificactions from the original script by
 *      Martin Angelov
 *      http://tutorialzine.com/2010/03/ajax-todo-list-jquery-php-mysql-css/
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


/* Defining the ToDo class */

class ToDo {
	
	/* An array that stores the todo item data: */
	
	private $data;

	
	/* The constructor */
	public function __construct($par){
		if(is_array($par)) {
			$this->data = $par;
    }
	}
	
	/*
		This is an in-build "magic" method that is automatically called 
		by PHP when we output the ToDo objects with echo. 
	*/
		
	public function __toString(){
		
		// The string we return is outputted by the echo statement
		
		return '
			<li id="todo-'.$this->data['id'].'" class="todo '.$this->data['type'].'">
			
				<div class="text">'.$this->data['text'].'</div>
				
				<div class="actions">
					<a href="#" title="Edit" class="edit">Edit</a>
					<a href="#" title="Delete" class="delete">Delete</a>
				</div>
				
			</li>';
	}
	
	
	/*
		The following are static methods. These are available
		directly, without the need of creating an object.
	*/
	
	
	
	/*
		The edit method takes the ToDo item id and the new text
		of the ToDo. Updates the database.
	*/
		
	public static function edit($id, $text){
		
		$text = self::esc($text);
		if(!$text) throw new Exception("Wrong update text!");
		
		mysql_query("	UPDATE tz_todo
						SET text='".$text."'
						WHERE id=".$id
					);
		
		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Couldn't update item!");
	}
	
	/*
		The delete method. Takes the id of the ToDo item
		and deletes it from the database.
	*/
	
	public static function delete($id){
    
		//Which list is this on?
    $list = mysql_query("SELECT * FROM `tz_todo` WHERE id = " . $id);
    //echo mysql_num_rows($list);
    while($row = mysql_fetch_assoc($list)){
        $list_id = $row['list_id'];
    }
		
		mysql_query("DELETE FROM tz_todo WHERE id=".$id);
    mysql_query("UPDATE `lists` SET `last_updated` = '" . date("Y-m-d H:i:s",time()) . "' WHERE `list_id` = "  .$list_id);
		
		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Couldn't delete item!");
	}
	
	/*
		The rearrange method is called when the ordering of
		the todos is changed. Takes an array parameter, which
		contains the ids of the todos in the new order.
	*/
	
	public static function rearrange($key_value){
		
		$updateVals = array();
		foreach($key_value as $k=>$v)
		{
			$strVals[] = 'WHEN '.(int)$v.' THEN '.((int)$k+1).PHP_EOL;
		}
		
		if(!$strVals) throw new Exception("No data!");
		
		// We are using the CASE SQL operator to update the ToDo positions en masse:
		
		mysql_query("	UPDATE tz_todo SET position = CASE id
						".join($strVals)."
						ELSE position
						END");
    
		
    if(mysql_error($GLOBALS['link']))
			throw new Exception("Error updating positions!");
	}

	public static function rearrange_in_set($key_value){
    //print_r($key_value);
    ////foreach($key_value as $k=>$v) {
     // if ($v = "reserve") {
    //    $key = $k;
     // }
    //}
		//unset($key_value[$key]);
		
		$updateVals = array();
		foreach($key_value as $k=>$v)
		{
			$strVals[] = 'WHEN '.(int)$v.' THEN '.((int)$k+1).PHP_EOL;
		}
		
		if(!$strVals) throw new Exception("No data!");
		
		// We are using the CASE SQL operator to update the ToDo positions en masse:
		
		mysql_query("	UPDATE tz_todo SET position = CASE id
						".join($strVals)."
						ELSE position
						END");
    mysql_query("UPDATE tz_todo SET `in_out` = 1 WHERE id IN ( ".implode(",",$key_value).")");
    
    //update the last_updated time on a list
    $list = mysql_query("SELECT * FROM `tz_todo` WHERE id = " . $v);
    //echo mysql_num_rows($list);
    while($row = mysql_fetch_assoc($list)){
        $list_id = $row['list_id'];
    }
		mysql_query("UPDATE `lists` SET `last_updated` = '" . date("Y-m-d H:i:s",time()) . "' WHERE `list_id` = "  .$list_id);
		
		if(mysql_error($GLOBALS['link']))
			throw new Exception("Error updating positions!");
	}
  
  	public static function rearrange_not_in_set($key_value){
    //print_r($key_value);
    //foreach($key_value as $k=>$v) {
    //  if ($v = "reserve") {
    //    $key = $k;
    //  }
    //}
		//unset($key_value[$key]);
		$updateVals = array();
		foreach($key_value as $k=>$v)
		{
			$strVals[] = 'WHEN '.(int)$v.' THEN '.((int)$k+1).PHP_EOL;
		}
		
		if(!$strVals) throw new Exception("No data!");
		
		// We are using the CASE SQL operator to update the ToDo positions en masse:
		
		mysql_query("	UPDATE tz_todo SET position = CASE id
						".join($strVals)."
						ELSE position
						END");
    mysql_query("UPDATE tz_todo SET `in_out` = 0 WHERE id IN ( ".implode(",",$key_value).")");
		
		if(mysql_error($GLOBALS['link']))
			throw new Exception("Error updating positions!");
	}
	
	/*
		The createNew method takes only the text of the todo,
		writes to the databse and outputs the new todo back to
		the AJAX front-end.
	*/
	
	public static function createNew($text,$list,$import=FALSE){
		
		$text = self::esc($text);
		if(!$text) throw new Exception("Wrong input data!");
		
		$posResult = mysql_query("SELECT MAX(position)+1 FROM tz_todo");
		
		if(mysql_num_rows($posResult))
			list($position) = mysql_fetch_array($posResult);

		if(!$position) $position = 1;


    //update the last_updated time on a list
    if (!$import) { //no need if this is an import and also because it breaks for some reason on import script
      mysql_query("UPDATE `lists` SET `last_updated` = '" . date("Y-m-d H:i:s",time()) . "' WHERE `list_id` = "  .$list);
    }
    //Needs to be last query executed as mysql_insert_id($GLOBALS['link'] gets it's value from this call
		mysql_query("INSERT INTO tz_todo SET text='".$text."', type='todo', list_id = '". $list ."', position = ".$position );
    
    
		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Error inserting TODO!");
      
   
		
		// Creating a new ToDo and outputting it directly:
		if (!$import) {
      echo (new ToDo(array(
        'id'	=> mysql_insert_id($GLOBALS['link']),
        'text'	=> $text,
        'type' => 'todo'
      )));
    
		exit;
    }
	}
  
  public static function createNewList($text,$user_id = 1,$list_title = 'New List', $import=FALSE) {
		//$user_id = $GLOBALS['user_id'];
    if(!isset($user_id)) {
      $user_id = 0;
    }
		$text = self::esc($text);
		if(!$text) throw new Exception("Wrong input data!");
		
		$posResult = mysql_query("SELECT MAX(position)+1 FROM tz_todo");
		if(mysql_num_rows($posResult))
			list($position) = mysql_fetch_array($posResult);

		if(!$position) $position = 1;
    
    $listResult = mysql_query("SELECT MAX(list_id)+1 FROM tz_todo");
    if(mysql_num_rows($listResult))
			list($list_id) = mysql_fetch_array($listResult);

		if(!$list_id) $list_id = 1;
    
		mysql_query("INSERT INTO tz_todo SET text='".$text."', type='todo', list_id = '". $list_id ."', position = ".$position );
    //echo "INSERT INTO tz_todo SET text='".$text."', type='todo', list_id = '". $list_id ."', position = ".$position;
    //echo $list_id;
    //echo mysql_affected_rows($GLOBALS['link']);
    //echo $GLOBALS['link'];
		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Error inserting TODO!");
		 mysql_query("INSERT INTO lists SET name='" . $list_title . "', user_id=" . $user_id . ", list_id = ". $list_id );

     //mysql_query("INSERT INTO lists SET name='New List', user_id='". $user_id ."', list_id = '". $list ."'");
		
    //Once we have ceated a new list with one song we either
    //Return the list id (when on implort.php)
		if ($import) {
      return $list_id;
    } else {
      //OR create a new ToDo and output it directly: (when using AJAX to create a new list)
      echo (new ToDo(array(
        'id'	=> mysql_insert_id($GLOBALS['link']),
        'text'	=> $text
      )));
    }
		exit;
	}
  
  	public static function createBreak($text,$list,$import=FALSE){
		
		$text = self::esc($text);
		if(!$text) throw new Exception("Wrong input data!");
		
		$posResult = mysql_query("SELECT MAX(position)+1 FROM tz_todo");
		
		if(mysql_num_rows($posResult))
			list($position) = mysql_fetch_array($posResult);

		if(!$position) $position = 1;

		
    //update the last_updated time on a list
		if (!$import) { //no need if this is an import and also because it breaks for some reason on import script
      mysql_query("UPDATE `lists` SET `last_updated` = '" . date("Y-m-d H:i:s",time()) . "' WHERE `list_id` = "  .$list);
    }
    
    //Need to create the break as the last step cos mysql_insert_id($GLOBALS['link']) relies on this
    mysql_query("INSERT INTO tz_todo SET text='".$text."' , type='break', list_id = '". $list ."', position = ".$position);
    
		if(mysql_affected_rows($GLOBALS['link'])!=1)
			throw new Exception("Error inserting TODO!");
		
		// Creating a new ToDo and outputting it directly:
		if (!$import) {
      echo (new ToDo(array(
        'id'	=> mysql_insert_id($GLOBALS['link']),
        'text'	=> $text,
        'type' => 'break'
      )));
      
      exit;
    }
	}
	
	/*
		A helper method to sanitize a string:
	*/
	
	public static function esc($str){
		
		if(ini_get('magic_quotes_gpc'))
			$str = stripslashes($str);
		
		return mysql_real_escape_string(strip_tags($str));
	}
  
  /*
		A helper method to update the last_updated time on a list
	*/
  
  public function last_updated($id) {
    
    return true;
  }
	
} // closing the class definition

?>
