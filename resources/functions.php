<?php
session_start();
require_once("constants.php");

/********** Get image functions **********/

/**
 * Gets images based on GET parameter (page)
 */
function getImages() {
	validateGuest();
	$page = getPageNumber();
	$imgz = generateImageArray($page);

	$ret = array(
		"images" => $imgz,
		"page" => $page
	);

	$json = json_encode($ret);
	return $json;
}

/**
 * Gets next or previous image based on GET parameters (img, direction)
 */
function getImage() {
	validateGuest();
	checkGetImageRequest();

	$img = $_GET["img"];
	$direction = $_GET["direction"];

	$retImg = getPictureForNameAndDirection($img, $direction);
	
	$ret = array(
		"image" => $retImg
	);
	
	$json = json_encode($ret);
	return $json;
}

/********** Utility functions **********/

/**
 * Validates required parameters for the getImage function
 */
function checkGetImageRequest() {
	if (empty($_GET["img"]) || empty($_GET["direction"])) {
		header('HTTP/1.1 401');
        die();
	}
}

/**
 * Handles pagination
 * @param int $page
 */
function generateImageArray($page) {
	$imgz = getAllImages();
	$imgz = array_slice($imgz, ($page - 1) * IMAGES_PER_PAGE, IMAGES_PER_PAGE);

	return $imgz;
}

/**
 * Gets all images ordered by date (descending)
 */
function getAllImages() {
	$imgz = scandir("imgz");
	$imgz = array_slice($imgz, 2); // remove . and ..
	$imgz = array_reverse($imgz);

	return $imgz;
}

/**
 * Validate GET for the getImages function and get page number
 */
function getPageNumber() {
	$page = 1;

	if (!empty($_GET["page"]) && is_numeric($_GET["page"])) {
		$page = (int)$_GET["page"];
	}

	return $page;
}

/**
 * Get next or previous image
 * @param String $img
 * @param String $direction
 */
function getPictureForNameAndDirection($img, $direction) {
	$imgz = getAllImages();
	$index = getImageIndex($img, $imgz);
	$index = adjustIndexForDirection($index, $direction, count($imgz));

	$image = $imgz[$index];
	return $image;
}

/**
 * Get index for image name
 * @param String $img
 * @param Array $imgz
 */
function getImageIndex($img, $imgz) {
	$index = -1;
 
	for ($i = 0; $i < count($imgz); $i++) {
		if ($imgz[$i] == $img) {
			$index = $i;
			break;
		}
	}

	if ($index == -1) {
		header('HTTP/1.1 401');
        die();
	}

	return $index;
}

/**
 * Increment / decrement index based on direction
 * @param int $index
 * @param String $direction
 * @param int $limit
 */
function adjustIndexForDirection($index, $direction, $limit) {
	if ($direction == "right") {
		$index++;
	} else if ($direction == "left") {
		$index--;
	}

	if ($index >= $limit) {
		$index = 0;
	}

	if ($index <= -1) {
		$index = $limit - 1;
	}

	return $index;
}

/********** Authentication **********/

/**
 * Validate session
 */
function validateGuest() {
	if (!(isset($_SESSION[SESSION_KEY]) && $_SESSION[SESSION_KEY] == SESSION_VALUE)) {
		header('HTTP/1.1 401');
        die();
	}
}

/**
 * Create session for authenticated user
 */
function authenticate() {
	if (isset($_POST['secret']) && md5($_POST['secret']) == HASHED_PASSWORD) {
		$_SESSION[SESSION_KEY] = SESSION_VALUE;
	}

	if (isset($_SESSION[SESSION_KEY]) && $_SESSION[SESSION_KEY] == SESSION_VALUE) {
		header("Location: index.html");
	} else {
		header("Location: login.html");
	}
}

/**
 * Destroy all sessions - logout
 */
function logout() {
	session_destroy();
}
