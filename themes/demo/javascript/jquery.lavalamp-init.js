jQuery(function() {
	$("#nav").lavaLamp({
		fx: "backout",
		speed: 700
	});
	$(" #nav ul ").css({
		display: "none"
	}); // Opera Fix

	$(" #nav li").hover(function() {
		$(this).find('ul:first').css({
			visibility: "visible",
			display: "none"
		}).slideDown(400);
	}, function() {
		$(this).find('ul:first').css({
			visibility: "hidden",
			display: "none"
		});
	});
});

