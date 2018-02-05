/*
* This file is for some small visual Javascript on the index page.
*
* Author: Bob Henley
*/

/*
* This function is for mobile only. It toggles whether the search is visible or not.
*/
function toggle_search() {
	$("#search_menu").css("display", "block");

	if($("#search_menu").hasClass("is-active")) {
		$("#search_menu").removeClass("slideInDown");
		$("#search_menu").addClass("animated");
		$("#search_menu").addClass("slideOutUp");
		$("#search_menu").removeClass("is-active");
	}else {
		$("#search_menu").removeClass("slideOutUp");
		$("#search_menu").addClass("animated");
		$("#search_menu").addClass("slideInDown");
		$("#search_menu").addClass("is-active");
	}
}

/*
* Things to do when the page has loaded.
*/
$(document).ready(function() {
	/*
	* Remove animations from the menu when the page is larger than mobile size.
	*/
	$(window).resize(function() {
		if($(window).width() > MOBILE_WIDTH && $("#search_menu").hasClass("animated")) {
			$("#search_menu").removeClass("animated")
		}
	});
});
