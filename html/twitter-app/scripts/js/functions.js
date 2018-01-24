$(document).ready(function() {
	//Bind the enter key to the search button upon typing into the search box.
	$('#search_box').keypress(function(event){
		if(event.keyCode == 13){
			$('#search_btn').click();
		}
	});

	$("#search_btn").unbind().click(function() {
	});
});