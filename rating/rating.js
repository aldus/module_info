$(document).ready(function() {
	$('.status').prepend("<div class='score_this'>(<a href='#'>Score this item ...</a>)</div>");
	$('.score_this').click(function(){
		$(this).slideUp();
		return false;
	});
	
	$('.score a').click(function() { 
		// alert ("call ...");
		$(this).parent().parent().parent().addClass('scored');
		$.get("{{ LEPTON_URL }}/modules/module_info/rating/rating.php" + $(this).attr("href") +"&update=true", {}, function(data){
			$('.scored').fadeOut("normal",function() {
				$(this).html(data);
				$(this).fadeIn();
				$(this).removeClass('scored');
			});
		});
		return false; // drp: was 'false'!
	});
});