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
	$imgz = scandir("imgz");
	$imgz = array_slice($imgz, 2);
	$imgz = array_slice($imgz, ($page - 1) * $imgzPerPage, $imgzPerPage);
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

function authenticate() {
	if (!(isset($_SESSION[SESSION_KEY]) && $_SESSION[SESSION_KEY] == SESSION_VALUE)) {
		header('HTTP/1.1 401');
        die();
	}
}
