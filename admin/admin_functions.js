/********** Functions **********/

/**
 * Shows images for input files
 */
function showImages(input) {
	$(".imgz").html("");
	
    for(var i = 0; i < input.files.length; i++) {
    	var reader = new FileReader();
        reader.onload = function (e) {
			var img = $("<img>");
			img.attr("src", e.target.result);
			img.appendTo(".imgz");
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

/**
 * Asynchronous upload
 */
function upload(form) {
	$(".response").html("The images are being uploaded... Feel free to close this window.");
	$(".imgz").html("");
	
	$.ajax({
		url: "admin_resources/admin_upload.php",
		type: "POST",
		data: new FormData(form),
		contentType: false,
		processData: false,
		success: function(data) {
			var count = data;
			$(".response").html(count + " images uploaded.");
		}
	});
}

/********** Events **********/

/**
 * Event for showing images
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

/**
 * Asynchronous upload event
 */
$(".imagesForm").on("submit", function(e) {
	e.preventDefault();
	upload(this);
});


/********** Initialize page **********/

validateGuest();