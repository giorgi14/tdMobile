$(document).ready(function(){
	$("#side_nav a.minimize").click(function(){
			$("#side_nav").toggleClass("closed", 800);
			$("#side_nav > ul li > span.icon").fadeToggle();
			$("#main").toggleClass("grid_15", 800);
			$(this).toggleClass("minimize_closed");
			
			if ($(this).hasClass("minimize_closed")) {
				$.cookie("SIDEMENU", "closed");
			}
			else{
				$.cookie("SIDEMENU","open");
			}
			
			return false;
	});
	
	$("#top_nav a.minimize").click(function(){
			$("#top_nav").toggleClass("closed", 800);
			$(this).toggleClass("minimize_closed");
			
			if($(this).hasClass("minimize_closed")) {
				$.cookie("TOPMENU", "closed");
			}else{
				$.cookie("TOPMENU", "open");
			}
			
			return false;
	});
	
	
	$("ul li ul").hide();
	
	$(document.body).mouseup(function(e) {
		$("ul li ul").hide();
    });
	
	$(document).on("click", "li", function(event) {
		$(this).children("ul").css("display", "block");
    });
	
	//Cookies for layout
	
	if($.cookie("SIDEMENU") === "closed") {
		$("#side_nav").addClass("closed");
		$("#side_nav a.minimize").addClass("minimize_closed");
		$("#main").addClass("grid_15");
	}
	
	if($.cookie("TOPMENU") === "closed") {
		$("#top_nav").addClass("closed");
		$("a#top_nav_toggle").addClass("minimize_closed");
	}
	
});