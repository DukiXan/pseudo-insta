/********** Functions **********/

/**
 * Shows images for input files
 */
function showImages(input) {
    for(var i = 0; i < input.files.length; i++) {
    	var reader = new FileReader();
        reader.onload = function (e) {
			var img = $('<img>');
			img.attr('src', e.target.result);
			img.appendTo('.imgz');
		}
        reader.readAsDataURL(input.files[i]);
	}
}

/**
 * Validates guest
 */
function validateGuest() {
	$.get("admin_resources/admin_validate.php").fail(function() {
		window.location.replace("index.php");
	});
}

/********** Events **********/

/**
 * Event for file upload
 */
$("#file").change(function(){
    showImages(this);
});

/**
 * Logout function
 */
$(".logout").click(function() {
	window.location.replace("admin_resources/admin_logout.php");
});


/********** Initialize page **********/

validateGuest();