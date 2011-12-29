//http://screencasts.org/episodes/introduction-to-jquery
//Copyright 2010-2011 Secret Monkey Science LLC - All Rights Reserved.
//Crafted by Josh Timonen & Andrew Chalkley 
$("#confirm").keyup(function(){
	if( $(this).val() != $("#pwd").val() ) {
		$(this).addClass("error").next().text("Passwords don't match.");
	} else {
		$(this).removeClass("error").next().text("Ok. Passwords match.");				
	}
});
