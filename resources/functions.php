<?php
require_once("constants.php");

function getPictures() {
	$page = getPageNumber();
	$imgz = generateImageArray($page);

	$ret = array(
		"images" => $imgz,
		"page" => $page
	);

	$json = json_encode($ret);
	return $json;
}

function generateImageArray($page) {
	$imgzPerPage = 8;
	$imgz = getAllPictures();
	$imgz = array_slice($imgz, ($page - 1) * $imgzPerPage, $imgzPerPage);

	return $imgz;
}

function getAllPictures() {
	$imgz = scandir("imgz");
	$imgz = array_slice($imgz, 2);
	$imgz = array_reverse($imgz);

	return $imgz;
}

function getPageNumber() {
	$page = 1;

	if (!empty($_GET["page"]) && is_numeric($_GET["page"])) {
		$page = (int)$_GET["page"];
	}

	return $page;
}

function getPicture() {
	if (empty($_GET["img"]) || empty($_GET["direction"])) {
		header('HTTP/1.1 401');
        die();
	}

	$img = $_GET["img"];
	$direction = $_GET["direction"];

	$retImg = getPictureForNameAndDirection($img, $direction);
	
	$ret = array(
		"image" => $retImg
	);
	
	$json = json_encode($ret);
	return $json;
}

function getPictureForNameAndDirection($img, $direction) {
	$imgz = getAllPictures();

	$retIndex = -1;
	for ($i = 0; $i < count($imgz); $i++) {
		if ($imgz[$i] == $img) {
			if ($direction == "right") {
				$retIndex = $i + 1;
			} else if ($direction == "left") {
				$retIndex = $i - 1;
			}
		}
	}

	if ($retIndex >= count($imgz)) {
		$retIndex = 0;
	}

	if ($retIndex <= -1) {
		$retIndex = count($imgz) - 1;
	}

	$ret = $imgz[$retIndex];
	return $ret;
}

function authenticate() {
	if (!(isset($_SESSION[SESSION_KEY]) && $_SESSION[SESSION_KEY] == SESSION_VALUE)) {
		header('HTTP/1.1 401');
        die();
	}
}
