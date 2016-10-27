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
	//$(".overlay").css("display", "none");
	$(".overlayImg").html("<img src=\"resources/imgz/" + img + "\">");
    $(".overlay").css("display", "inline");
}

function hideImage() {
	$(".overlay").css("display", "none");
}

$('.arrows').click(function(event){
    event.stopPropagation();
});

function changeImage(direction) {
	var currentImage = getCurrentImage();

	$.get("resources/get_picture.php", {"img": currentImage, "direction": direction}).success(function(data) {
		var img = JSON.parse(data).image;
		$(".overlayImg").html("<img src=\"resources/imgz/" + img + "\">");
	}).fail(function() {
		window.location.replace("resources/authenticate.php");
	});
}

function getCurrentImage() {
	var currentImagePath = $(".overlayImg img").attr("src");
	var currentImage = currentImagePath.substring(currentImagePath.lastIndexOf("/") + 1, currentImagePath.length);
	
	return currentImage;
}

$(document).keydown(function(e) {
    switch(e.which) {
        case 27:
        hideImage();
        break;

        case 37:
        changeImage("left");
        break;

        case 39:
        changeImage("right");
        break;

        default: return;
    }
    
    e.preventDefault();
});

getPictures(1);