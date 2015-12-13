<?php


error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once '../includes/connection.php';
include_once '../includes/functions.php';

$_POST['date_created'] = date("Y-m-d H:i:s");
$_POST['incident_id'] = $_GET['id'];

// var_dump($_POST);

$results = store_incident($_POST, $dbh);

// header("location:http://dev.project.com/pages/incident_manager.php?id=".$results);
?>