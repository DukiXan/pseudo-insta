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
	if (!empty($_FILES)) {
		$files = $_FILES["files"];

		mkdir(PATH_TEMP);

		foreach ($files["name"] as $i => $val) {
			$data = getData($files, $i);
			saveFile($data);
			$count++;
		}

		rmdir(PATH_TEMP);
	}

	return $count;
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
 * Saves an image (and its thumbnail)
 * @param Array $data
 */
function saveFile($data) {
	if (!$data["error"] && checkExtension($data["extension"])) {
		$name = generateImageName($data["extension"]);
		
		$pathTemp = PATH_TEMP . $name;
		$pathImage = PATH_IMAGES . $name;
		$pathThumbnail = PATH_THUMBNAILS . $name;

		move_uploaded_file($data["name"], $pathTemp);
		generateImage($pathTemp, $pathImage, IMAGE);
		generateImage($pathTemp, $pathThumbnail, THUMBNAIL);
		unlink($pathTemp);
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
