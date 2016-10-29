<?php

function uploadFiles() {
	if (!empty($_FILES)) {
		$files = $_FILES["files"];

		foreach ($files["name"] as $i => $val) {
			$data = getData($files, $i);
			saveFile($data);
		}
	}

	header("Location: ../index.php");
}

function getData($files, $i) {
	$data = array();

	$data["name"] = $files["tmp_name"][$i];
	$data["size"] = $files["size"][$i];
	$data["error"] = $files["error"][$i];

	$extension = explode('.', $files["name"][$i]);
	$data["extension"] = strtolower(end($extension));

	return $data;
}

function saveFile($data) {
	if (!$data["error"] && $data["size"] < 10000000 && checkExtension($data["extension"])) {
		$name = date("Y-m-d-H-i-s") . uniqid() . rand(1, 10000) . "." . $data["extension"];
		$destination = "../../resources/imgz/" . $name;
		move_uploaded_file($data["name"], $destination);
	}
}

function checkExtension($extension) {
 	return $extension == "png" || $extension == "jpg" || $extension == "jpeg";
}

uploadFiles();