$(document).ready(function() {
	$("#toggleCreateThread").click(function(event) {
		if($("#createThread").is(":visible"))
		{
			$("#toggleCreateThread").html("Create new thread &raquo;");
			$("#createThread").slideUp();
		} else
		{
			$("#toggleCreateThread").html("Hide");
			$("#createThread").slideDown();
		}
	});
	$(".clickToReply").click(function(event) {
		event.preventDefault();
		// There are two classes for this element. One is clickToReply, the other is the post number.
		// We get those classes as an array, then grab the second element, which is the post number (index 1).
		var classList = $(this).attr('class').split(/\s+/);
		var reply = classList[1];
		$("#parent").val(classList[1])
		$(".singlepost."+reply).append($("#createThread"));
		$("#createThread").fadeIn(200);
	});
	$(".postimage").click(function(event) {
		event.preventDefault();
		var full = $(this).find("a").attr("href");
		if($(this).hasClass("expanded")) {
			var n = full.lastIndexOf("/");
			var thumb = full.substr(0,n) + "/thumbs/" + full.substr(n+1);
			$(this).find("img").attr("src",thumb);
			$(this).removeClass("expanded");
		} else {
			$(this).find("img").attr("src",full);
			$(this).addClass("expanded");
		}
	});
});