/********** Functions **********/

/**
 * Shows images for input files
 */
function showImages(input) {
	$(".imgz").html("");
    for(var i = 0; i < input.files.length; i++) {
    	var reader = new FileReader();
        reader.onload = function (e) {
        	var img = resizeImage(e.target.result);
			$(".imgz").append(img);
		}

        reader.readAsDataURL(input.files[i]);
	}
}

/**
 * Resize image using a canvas element
 */
function resizeImage(source) {
	var img = new Image();
	img.src = source;

	var canvas = document.getElementById("canvas");
	var ctx = canvas.getContext("2d");
	ctx.drawImage(img, 0, 0);

	var width = img.width > img.height ? 1600 : 1000;
	var height = Math.floor(img.height * (width / img.width));

	canvas.width = width;
	canvas.height = height;
	ctx = canvas.getContext("2d");
	ctx.drawImage(img, 0, 0, width, height);

	// Add image to global files array used for upload
	files.push(canvas.toDataURL("image/jpg"));

	return img;
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
function upload() {
	$(".response").html("The images are being uploaded... Feel free to close this window.");
	$(".imgz").html("");
	
	$.ajax({
		xhr: function () {
	        $('.progress').removeClass('hide');
	        var xhr = new window.XMLHttpRequest();
	        xhr.upload.addEventListener("progress", function (evt) {
	            if (evt.lengthComputable) {
	                var percentComplete = evt.loaded / evt.total;
	                console.log(percentComplete);
	                $('.progress').css({
	                    width: percentComplete * 100 + '%'
	                });
	                if (percentComplete === 1) {
	                    $('.progress').addClass('hide');
	                }
	            }
	        }, false);
	        return xhr;
	    },
		url: "admin_resources/admin_upload.php",
		type: "POST",
		dataType: "json",
  		data: { "files": files },
		success: function(data) {
			var count = data;
			$(".response").html(count + " images uploaded.");
		}
	});

	files = [];
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
	upload();
});


/********** Initialize page **********/
var files = [];
validateGuest();
