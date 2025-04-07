<?php
include 'db/db_connect.php';
session_start();
if (!isset($_SESSION['loggedAccount'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    var_dump($_POST);
    var_dump($_GET);
    var_dump($_SESSION);
}
?>
