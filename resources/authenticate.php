<?php
session_start();
require_once("functions.php");

if (isset($_POST['secret']) && md5($_POST['secret']) == HASHED_PASSWORD) {
	$_SESSION[SESSION_KEY] = SESSION_VALUE;
}

if (isset($_SESSION[SESSION_KEY]) && $_SESSION[SESSION_KEY] == SESSION_VALUE) {
	header("Location: ../index.html");
} else {
	header("Location: ../login.html");
}
