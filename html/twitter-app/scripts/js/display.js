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

$(document).ready(function() {
	$(window).resize(function() {
		if($(window).width() > MOBILE_WIDTH && $("#search_menu").hasClass("animated")) {
			$("#search_menu").removeClass("animated")
		}
	});
});
