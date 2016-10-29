<?php
session_start();
require_once("admin_resources/admin_constants.php");

if (isset($_POST['admin_secret']) && md5($_POST['admin_secret']) == HASHED_ADMIN_PASSWORD) {
	$_SESSION[ADMIN_SESSION_KEY] = ADMIN_SESSION_VALUE;
}

if (isset($_SESSION[ADMIN_SESSION_KEY]) && $_SESSION[ADMIN_SESSION_KEY] == ADMIN_SESSION_VALUE) {
	header("Location: admin_index.html");
} else {
	header("Location: admin_login.html");
}