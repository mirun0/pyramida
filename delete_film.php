<?php

session_start();
if(!$_SESSION['loggedAccount']) {
    header('Location: login.php');
    exit();
}

include 'db/db_connect.php';
if (!isset($_GET['id'])) die("Skript nelze spustit samostatne!");

$filmId = $_GET['id'];

$sql = "SELECT FK_role from user where id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION['accountId']]);
$userRole = $stmt->fetch();

if ($userRole === null || $userRole['FK_role'] > 2) {
    header("Location: index.php");
    exit();
} else {
    $sql = "SELECT image FROM film WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$filmId]);
    $result = $stmt->fetch();

    $filePointer = 'img/' . $result["image"];
    if (unlink($filePointer)) {
        echo ("$filePointer byl odstraněn");
    } else {
        echo ("$filePointer se nepodařilo odstranit.");
    }

    $sql = "DELETE FROM film WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$filmId]);
}

header("Location: film_administration.php");
exit();

?>