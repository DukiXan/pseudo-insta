/********** Functions **********/

/**
 * Shows next page of images
 */
function getImages() {
	$.get("resources/get_images.php", {"page" : globalPage}).success(function(data) {
		globalPage++;
		
		var imgz = JSON.parse(data).images;
		generateThumbnails(imgz);
		generateShowMoreButton(imgz);
	}).fail(function() {
		window.location.replace("index.php");
	});
}

/**
 * Generates thumbnails for an image array
 * @param {Array} imgz
 */
function generateThumbnails(imgz) {
	var thumbnailHtml = "";

	if (imgz.length != 0) {
		for (var i = 0; i < imgz.length; i++) {
			thumbnailHtml += "<img src=\"resources/thumbnails/" + imgz[i] + 
								"\" onclick=\"showImage('" + imgz[i] + "')\">";
		}
	}

	$(".imgz").append(thumbnailHtml);
}

/**
 * Generates the show more button
 * @param {Array} imgz
 */
function generateShowMoreButton(imgz) {
	var pageHtml = "";

	if (imgz.length != 0) {
		pageHtml = "<div>&#8635;</div>";
	}

	$(".page").html(pageHtml);
}

/**
 * Shows a specific image on a large preview
 * @param {String} img
 */
function showImage(img) {
	$(".overlayImg").html("<img src=\"resources/imgz/" + img + "\">");
    $(".overlay").css("display", "inline");
}

/**
 * Hides the large preview
 */
function hideImage() {
	$(".overlay").css("display", "none");
}

/**
 * Changes the large preview to next or previous image
 * @param {String} direction
 */
function changeImage(direction) {
	var currentImage = getCurrentImage();

	$.get("resources/get_image.php", {"img": currentImage, "direction": direction}).success(function(data) {
		var img = JSON.parse(data).image;
		$(".overlayImg").html("<img src=\"resources/imgz/" + img + "\">");
	}).fail(function() {
		window.location.replace("index.php");
	});
}

/**
 * Gets the path to the current image on the large preview
 */
function getCurrentImage() {
	var currentImagePath = $(".overlayImg img").attr("src");
	var currentImage = currentImagePath.substring(currentImagePath.lastIndexOf("/") + 1, currentImagePath.length);
	
	return currentImage;
}

/********** Events **********/

/**
 * Hide image when pressing on the overlay
 */
$(".overlay").click(function() {
	hideImage();
});

/**
 * Left arrow event
 */
$(".leftArrow").click(function() {
	changeImage('left');
});

/**
 * Right arrow event
 */
$(".rightArrow").click(function() {
	changeImage('right');
});

/**
 * Stops hiding of the large preview when clicking on the arrows
 */
$(".arrows").click(function(event){
    event.stopPropagation();
});

/**
 * Logout function
 */
$(".logout").click(function() {
	window.location.replace("resources/logout.php");
});

/**
 * Handles left, right, and ESC key presses
 */
$(document).keydown(function(e) {
    switch(e.which) {
        case 27: // ESC
        hideImage();
        break;

        case 37: // LEFT
        changeImage("left");
        break;

        case 39: // RIGHT
        changeImage("right");
        break;

        default: return;
    }
    
    e.preventDefault();
});

/**
 * Click on infinite scroll
 */
$(".page").click(function() {
	getImages();
});

/********** Initialize page **********/

var globalPage = 1; // Tracks last loaded images
getImages();
