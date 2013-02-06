$(document).ready(function() {
	$("#showCreatePost").click(function(event) {
		event.preventDefault();
		$("#createpost").fadeIn(200);
	});
	$("#hideCreatePost").click(function(event) {
		event.preventDefault();
		$("#createpost").fadeOut(200);
	});
	$(".clickToReply").click(function(event) {
		event.preventDefault();
		// There are two classes for this element. One is clickToReply, the other is the post number.
		// We get those classes as an array, then grab the second element, which is the post number (index 1).
		var classList = $(this).attr('class').split(/\s+/);
		var reply = classList[1];
		$(".singlepost."+reply).append($("#createpost"));
		$("#createpost").fadeIn(200);
	});
});