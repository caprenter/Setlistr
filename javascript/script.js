//      script.js
//      There are only a few modificactions from the original script by
//      Martin Angelov
//      http://tutorialzine.com/2010/03/ajax-todo-list-jquery-php-mysql-css/
//      
//      Copyright 2011 caprenter <caprenter@gmail.com>
//      
//      This file is part of Setlistr.
//      
//      Setlistr is free software: you can redistribute it and/or modify
//      it under the terms of the GNU Affero General Public License as published by
//      the Free Software Foundation, either version 3 of the License, or
//      (at your option) any later version.
//      
//      Setlistr is distributed in the hope that it will be useful,
//      but WITHOUT ANY WARRANTY; without even the implied warranty of
//      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//      GNU Affero General Public License for more details.
//      
//      You should have received a copy of the GNU Affero General Public License
//      along with Setlistr.  If not, see <http://www.gnu.org/licenses/>.
//      
//      Setlistr relies on other free software products. See the README.txt file 
//      for more details.

$(document).ready(function(){
	/* The following code is executed once the DOM is loaded */

	$("#sortable1, #sortable2").sortable({
		axis		: 'x,y',				// Only vertical movements allowed
    items: 'li:not(.notice)', //restricts the sortable items to li elements only
		//containment	: 'window',			// Constrained by the window
    connectWith: '.todoList', //Conct with another list
		update		: function(){		// The function is called after the todos are rearranged
    
		
			// The toArray method returns an array with the ids of the todos
			//var arr = $(".todoList").sortable('toArray');
      var arrIn = $("#sortable1").sortable('toArray');
      var arrOut = $("#sortable2").sortable('toArray');
			
			
			// Striping the todo- prefix of the ids:
			
			//arr = $.map(arr,function(val,key){
			//	return val.replace('todo-','');
			//});
      arrIn = $.map(arrIn,function(val,key){
				return val.replace('todo-','');
			});
      
      
      //for(var i=0;i<arrIn.length;i++){
      //  document.write("<b>arrIn["+i+"] is </b>=>"+arrIn[i]+"<br>");
      //}
      
      
      
      arrOut = $.map(arrOut,function(val,key){
				return val.replace('todo-','');
			});
      
      //for(var i=0;i<arrOut.length;i++){
      //  document.write("<b>arrOut["+i+"] is </b>=>"+arrOut[i]+"<br>");
      //}
			
			// Saving with AJAX
			//$.get('functions/ajax.php',{action:'rearrange',positions:arr});
      //if (arrIn.length >=1) {
        $.get('functions/ajax.php',{action:'rearrange_in_set',positions:arrIn});
      //}
      //if (arrOut.length !=0) {
        $.get('functions/ajax.php',{action:'rearrange_not_in_set',positions:arrOut});
     // }
        //Rewrite the last update string: - updated: 21st Jan, 2012 18:21:11
        document.getElementById('updated').innerHTML = toUTCStr().replace('Current setting is','updated:');
		},
		
		/* Opera fix: */
		
		stop: function(e,ui) {
			ui.item.css({'top':'0','left':'0'});
		}
	});
	
	// A global variable, holding a jQuery object 
	// containing the current todo item:
	
	var currentTODO;
	
	// Configuring the delete confirmation dialog
	$("#dialog-confirm").dialog({
		resizable: false,
		height:130,
		modal: true,
		autoOpen:false,
		buttons: {
			'Delete item': function() {
				
				$.get("functions/ajax.php",{"action":"delete","id":currentTODO.data('id')},function(msg){
					currentTODO.fadeOut('fast');
				})
				
				$(this).dialog('close');
        //Rewrite the last update string: - updated: 21st Jan, 2012 18:21:11
        document.getElementById('updated').innerHTML = toUTCStr().replace('Current setting is','updated:');
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});

	// When a double click occurs, just simulate a click on the edit button:
	$('.todo').live('dblclick',function(){
		$(this).find('a.edit').click();
	});
	
	// If any link in the todo is clicked, assign
	// the todo item to the currentTODO variable for later use.

	$('.todo a').live('click',function(e){
									   
		currentTODO = $(this).closest('.todo');
		currentTODO.data('id',currentTODO.attr('id').replace('todo-',''));
		
		e.preventDefault();
	});

	// Listening for a click on a delete button:

	$('.todo a.delete').live('click',function(){
		$("#dialog-confirm").dialog('open');
	});
	
	// Listening for a click on a edit button
	
	$('.todo a.edit').live('click',function(){

		var container = currentTODO.find('.text');
		
		if(!currentTODO.data('origText'))
		{
			// Saving the current value of the ToDo so we can
			// restore it later if the user discards the changes:
			
			currentTODO.data('origText',container.text());
		}
		else
		{
			// This will block the edit button if the edit box is already open:
			return false;
		}
		
		$('<input type="text" class="song-entry">').val(container.text()).appendTo(container.empty()).select();

		
		// Appending the save and cancel links:
		container.append(
			'<div class="editTodo">'+
				'<a class="saveChanges" href="#">Save</a> or <a class="discardChanges" href="#">Cancel</a>'+
			'</div>'
		);
		
		//Save/Cancel on enter/escape
		var KEYCODE_ENTER = 13;
        var KEYCODE_ESC = 27;

	    $(container).keyup(function(e) {
            if (e.keyCode == KEYCODE_ENTER) { $('.todo a.saveChanges').click(); } 
            if (e.keyCode == KEYCODE_ESC) { $('.todo a.discardChanges').click(); } 
            //Rewrite the last update string: - updated: 21st Jan, 2012 18:21:11
            document.getElementById('updated').innerHTML = toUTCStr().replace('Current setting is','updated:');
        });
	});
	


	
	// The cancel edit link:
	
	$('.todo a.discardChanges').live('click',function(){
		currentTODO.find('.text')
					.text(currentTODO.data('origText'))
					.end()
					.removeData('origText');
	});
	
	// The save changes link:
	
	$('.todo a.saveChanges').live('click',function(){
		var text = currentTODO.find("input[type=text]").val();
		
		$.get("functions/ajax.php",{'action':'edit','id':currentTODO.data('id'),'text':text});
		
		currentTODO.removeData('origText')
					.find(".text")
					.text(text);
    //Rewrite the last update string: - updated: 21st Jan, 2012 18:21:11
    document.getElementById('updated').innerHTML = toUTCStr().replace('Current setting is','updated:');
	});
  
  
  
  
  
  
  
  
  
  
  
  
  //$("h4").html("Public offers");

	
	// The Add New ToDo button:
	
	var timestamp=0;
  var list = $("h4").attr("id").replace('list-','');
  if (list== null) {
    list = 0;
  }
  //document.write("<b>"+list+"</b>");

	$('#addButton').click(function(e){

		// Only one todo per 5 seconds is allowed:
		if((new Date()).getTime() - timestamp<1000) return false;
		
		$.get("functions/ajax.php",{'action':'new','text':'New Song','list':list,'rand':Math.random()},function(msg){

			// Appending the new todo and fading it into view:
			$(msg).hide().appendTo('#sortable1').fadeIn().find('a.edit').click();
      //Rewrite the last update string: - updated: 21st Jan, 2012 18:21:11
      document.getElementById('updated').innerHTML = toUTCStr().replace('Current setting is','updated:');
		});

		// Updating the timestamp:
		timestamp = (new Date()).getTime();
		
		e.preventDefault();
	});
  
  	// The Set Break button:
	
	var timestamp=0;
  var list = $("h4").attr("id").replace('list-','');
  if (list == null) {
    list = 0;
  }
	$('#addBreak').click(function(e){

		// Only one todo per 5 seconds is allowed:
		if((new Date()).getTime() - timestamp<1000) return false;
		
		$.get("functions/ajax.php",{'action':'break','text':'Set Break','list':list,'rand':Math.random()},function(msg){

			// Appending the new todo and fading it into view:
			$(msg).hide().appendTo('#sortable1').fadeIn().find('a.edit').click();
      //Rewrite the last update string: - updated: 21st Jan, 2012 18:21:11
      document.getElementById('updated').innerHTML = toUTCStr().replace('Current setting is','updated:');
		});

		// Updating the timestamp:
		timestamp = (new Date()).getTime();
    //document.getElementById('updated').innerHTML = 'Fred Flinstone';
		
		e.preventDefault();
	});
  
  // Start New List:
	
	var timestamp=0;
  //var user=10;
	$('#newList').click(function(e){

		// Only one todo per 5 seconds is allowed:
		if((new Date()).getTime() - timestamp<5000) return false;
		
		$.get("functions/ajax.php",{'action':'new-list','text':'New Song. Doubleclick to Edit.','rand':Math.random()},function(msg){

			// Appending the new todo and fading it into view:
			$(msg).hide().appendTo('#sortable1').fadeIn();
		});

		// Updating the timestamp:
		timestamp = (new Date()).getTime();
		
		e.preventDefault();
	});
  
  
  //edit-in-place
  $('.edit').editable('http://localhost/Webs/setlistr/functions/updateTitle.php', {
      type   : 'textarea',
      select : true,
      submit : 'OK',
      cancel : 'cancel'
     });
     
  //http://msdn.microsoft.com/en-us/library/7ew14035%28v=vs.94%29.aspx
  function toUTCStr(){
   var d, s;                   //Declare variables.
   d = new Date();             //Create Date object.
   s = "Current setting is ";
   s += d.toUTCString();       //Convert to UTC string.
   return(s);                  //Return UTC string.
  }
  
	
}); // Closing $(document).ready()
