function getPictures(page) {
	$.get("resources/get_pictures.php", {"page" : page}).success(function(data) {
		var imgz = JSON.parse(data).images;
		var page = JSON.parse(data).page;

		var pageHtml = "";

		if (imgz.length != 0) {
			for (var i = 0; i < imgz.length; i++) {
				$(".imgz").append("<img src=\"resources/imgz/" + imgz[i] + "\">");
			}

			page++;

			pageHtml = "<div onclick=\"getPictures(" + page + ")\">LOAD MORE</div>";
		}

		$(".page").html(pageHtml);
	}).fail(function() {
		window.location.replace("resources/authenticate.php");
	});
}

getPictures(1);