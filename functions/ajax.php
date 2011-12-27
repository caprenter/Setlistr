<?php
/*
 *      ajax.php
 *      This is the other Ajax function that deals with all the list sorting. 
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

require "connect.php";
require "todo.class.php";

$id = (int)$_GET['id'];

try{

	switch($_GET['action'])
	{
		case 'delete':
			ToDo::delete($id);
			break;
			
		case 'rearrange':
			ToDo::rearrange($_GET['positions']);
			break;
    case 'rearrange_in_set':
			ToDo::rearrange_in_set($_GET['positions']);
			break;
    case 'rearrange_not_in_set':
			ToDo::rearrange_not_in_set($_GET['positions']);
			break;
			
		case 'edit':
			ToDo::edit($id,$_GET['text']);
			break;
    
    case 'edit_title':
			ToDo::edit($id,$_GET['text']);
			break;
			
		case 'new':
			ToDo::createNew($_GET['text'],$_GET['list']);
			break;
    case 'new-list':
			ToDo::createNewList($_GET['text'],$GLOBALS['user_id']);
			break;
    case 'break':
			ToDo::createBreak($_GET['text'],$_GET['list']);
			break;
	}

}
catch(Exception $e){
//	echo $e->getMessage();
	die("0");
}

echo "1";
?>
