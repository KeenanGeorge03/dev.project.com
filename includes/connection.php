
<?php 
$dsn = 'mysql:host=journeyatrest.com;dbname=george';
$username = 'keenangeorge';
$password = 'C1JSkScZ4gWkLTTvWQm4';
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

$dbh = new PDO($dsn, $username, $password, $options);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>
