<?php
session_start();
require_once("admin_constants.php");

/********** Upload files function **********/

/**
 * Uploads images from the POST request
 */
function uploadFiles() {
	validateGuest();

	$count = 0;
	if (!empty($_POST["files"])) {
		$files = $_POST["files"];

		mkdir(PATH_TEMP); // Creates temp folder

		foreach ($files as $file) {
			saveFile($file);
			$count++;
		}

		rmdir(PATH_TEMP); // Deletes temp folder
	}

	return $count;
}

/********** Utility functions **********/

/**
 * Saves an image (and its thumbnail)
 * @param Array $data
 */
function saveFile($data) {
	$name = generateImageName();
	
	$pathTemp = PATH_TEMP . $name;
	$pathImage = PATH_IMAGES . $name;
	$pathThumbnail = PATH_THUMBNAILS . $name;

	$image = getImageFromString($data);

	imagejpeg($image, $pathTemp); // Saves temp image
	generateImage($pathTemp, $pathImage, IMAGE);
	generateImage($pathTemp, $pathThumbnail, THUMBNAIL);
	unlink($pathTemp); // Deletes temp image
}

/**
 * Generates image from string
 * @param String $data
 */
function getImageFromString($data) {
	$data = base64_decode(preg_replace("#^data:image/\w+;base64,#i", '', $data));
	$image = imagecreatefromstring($data);
	return $image;
}

/**
 * Generates a unique name for the image
 */
function generateImageName() {
	$name = date("Y-m-d-H-i-s") . uniqid() . rand(1, 10000) . ".jpg";
	return $name;
}

/**
 * Generates an image from an upload
 * @param String $source
 * @param String $destination
 * @param String $type
 */
function generateImage($source, $destination, $type) {
	$original = imagecreatefromjpeg($source);
	$width = imagesx($original);
	$height = imagesy($original);

	$newWidth = generateImageWidth($width, $height, $type);
	$newHeight = generateImageHeight($width, $height, $newWidth);

	$tempImage = imagecreatetruecolor($newWidth, $newHeight);
	imagecopyresampled($tempImage, $original, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
	imagejpeg($tempImage, $destination);
}

/**
 * Generates image width
 * @param int $width
 * @param int $height
 * @param String $type
 */
function generateImageWidth($width, $height, $type) {
	if ($type == THUMBNAIL) {
		$newWidth = $width > $height ? THUMBNAIL_WIDTH_LANDSCAPE : THUMBNAIL_WIDTH_PORTRAIT;
	} else if ($type == IMAGE) {
		$newWidth = $width > $height ? IMAGE_WIDTH_LANDSCAPE : IMAGE_WIDTH_PORTRAIT;
	}

	return $newWidth;
}

/**
 * Generates image height
 * @param int $width
 * @param int $height
 * @param int $newWidth
 */
function generateImageHeight($width, $height, $newWidth) {
	$newHeight = floor($height * ($newWidth / $width));
	return $newHeight;
}

/********** Authentication **********/

/**
 * Validate session
 */
function validateGuest() {
	if (!(isset($_SESSION[ADMIN_SESSION_KEY]) && $_SESSION[ADMIN_SESSION_KEY] == ADMIN_SESSION_VALUE)) {
		header('HTTP/1.1 401');
        die();
	}
}

/**
 * Create session for authenticated user
 */
function authenticate() {
	if (isset($_POST['admin_secret']) && md5($_POST['admin_secret']) == HASHED_ADMIN_PASSWORD) {
		$_SESSION[ADMIN_SESSION_KEY] = ADMIN_SESSION_VALUE;
	}

	if (isset($_SESSION[ADMIN_SESSION_KEY]) && $_SESSION[ADMIN_SESSION_KEY] == ADMIN_SESSION_VALUE) {
		header("Location: admin_index.html");
	} else {
		header("Location: admin_login.html");
	}
}

/**
 * Destroy all sessions - logout
 */
function logout() {
	session_destroy();
	header("Location: ../admin_login.html");
}
