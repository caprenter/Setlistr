//Thanks to ZeeShaN RasooL: http://www.99points.info/2010/05/how-to-create-dynamic-content-loading-using-ajax-jquery/
$(document).ready(function(){	
		$('a.setlist').on('click',function(event){
      event.preventDefault();
			$('#list-container').fadeOut();
			var a = $(this).attr('id');
			$.post("functions/show_list_ajax.php?list="+a, {
			}, function(response){
				//$('#list-container').html(unescape(response));
				///$('#list-container').fadeIn();
				setTimeout("finishAjax('list-container', '"+escape(response)+"')", 400);
			});
		});	
	});	
	function finishAjax(id, response){
	  $('#'+id).html(unescape(response));
	  $('#'+id).fadeIn();
	}

