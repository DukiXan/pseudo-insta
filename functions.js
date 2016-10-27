function getPictures(page) {
	$.get("resources/get_pictures.php", {"page" : page}).success(function(data) {
		var imgz = JSON.parse(data).images;
		var page = JSON.parse(data).page;

		var pageHtml = "";

		if (imgz.length != 0) {
			for (var i = 0; i < imgz.length; i++) {
				$(".imgz").append("<img src=\"resources/imgz/" + imgz[i] + 
									"\" onclick=\"showImage('" + imgz[i] + "')\">");
			}

			page++;

			pageHtml = "<div onclick=\"getPictures(" + page + ")\">LOAD MORE</div>";
		}

		$(".page").html(pageHtml);
	}).fail(function() {
		window.location.replace("resources/authenticate.php");
	});
}

$(".logout").click(function() {
	$.get("resources/logout.php").done(function() {
		window.location.replace("resources/authenticate.php");
	});
});

function showImage(img) {
	$(".overlayImg").html("<img src=\"resources/imgz/" + img + "\">");
    $(".overlay").css("display", "inline");
}

function hideImage() {
	$(".overlay").css("display", "none");
}

getPictures(1);