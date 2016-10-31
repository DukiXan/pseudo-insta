<?php
session_start();
require_once("admin_constants.php");

/********** Upload files function **********/

/**
 * Uploads images from the POST request
 */
function uploadFiles() {
	validateGuest();
	if (!empty($_FILES)) {
		$files = $_FILES["files"];

		foreach ($files["name"] as $i => $val) {
			$data = getData($files, $i);
			saveFile($data);
		}
	}

	header("Location: ../index.php?success=yes");
}

/********** Utility functions **********/

/**
 * Gets data from FILES array
 * @param Array $files
 * @param int $index
 */
function getData($files, $index) {
	$data = array();

	$data["name"] = $files["tmp_name"][$index];
	$data["size"] = $files["size"][$index];
	$data["error"] = $files["error"][$index];

	$extension = explode('.', $files["name"][$index]);
	$data["extension"] = strtolower(end($extension));

	return $data;
}

/**
 * Saves an image (large and thumbnail)
 * @param Array $data
 */
function saveFile($data) {
	if (!$data["error"] && checkExtension($data["extension"])) {
		$name = generateImageName($data["extension"]);
		
		$pathImage = PATH_IMAGES . $name;
		$pathThumbnail = PATH_THUMBNAILS . $name;

		move_uploaded_file($data["name"], $pathImage);
		generateThumbnail($pathImage, $pathThumbnail);
	}
}

/**
 * Generates a unique name for the image
 * @param String $extension
 */
function generateImageName($extension) {
	$name = date("Y-m-d-H-i-s") . uniqid() . rand(1, 10000) . "." . $extension;
	return $name;
}

/**
 * Generates a thumbnail for an image
 * @param String $source
 * @param String $destination
 */
function generateThumbnail($source, $destination) {
	$original = imagecreatefromjpeg($source);
	$width = imagesx($original);
	$height = imagesy($original);
	
	$thumbnailWidth = $width > $height ? 400 : 250;
	$thumbnailHeight = floor($height * ($thumbnailWidth / $width));

	$tempImage = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
	imagecopyresampled($tempImage, $original, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);
	imagejpeg($tempImage, $destination);
}

/**
 * Validates image format
 * @param String $extension
 */
function checkExtension($extension) {
 	return $extension == "jpg" || $extension == "jpeg";
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
