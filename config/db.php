<?php
$host = 'localhost';
$db   = 'skillshare';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


class Util {
    static $GO_BACK = "98";
    static $GO_MAIN_MENU = "99";

    static $DB_NAME = "ussd";
    static $DB_USER = "root";
    static $DB_PASSWORD="";
    static $USER_BALANCE=500;
    static $TRANSACTION_FEES=20;
    static $WITHDRAW_FEES=200;

}
?>
