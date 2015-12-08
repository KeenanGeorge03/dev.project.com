<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (isset($_POST['username'])) {
  $time = 9999999999;

  if (isset($_POST['username']) && $_POST['username'] != "") {

    include_once '../includes/connection.php';
	include_once '../includes/functions.php';
	include_once '../includes/password_manager.php';

    /* Execute a prepared statement by passing an array of values */
    $sth = $dbh->prepare('SELECT * FROM authentication_tbl WHERE username = :user');
    $sth->execute(array(':user' => $_POST['username']));
    $user = $sth->fetchAll();

    if (validate_password($_POST['password'], $user[0]['password'])) {
      setcookie("user", $user[0]['username'], $time, "/", "dev.project.com");
      // setcookie("hash", $user[0]['cookiehash'], $time, "/", "dev.project.com");
      header("location:http://dev.project.com/pages/index.php");
    }
    else {
      header("location:http://dev.project.com/pages/login.php?error=Password%20incorrect");
    }

  }
	else {
		header("location:http://dev.project.com/pages/login.php?error=Username%20empty");
	}
}
else {
	header("location:http://dev.project.com/pages/login.php?error=Username%20empty");
}
?>