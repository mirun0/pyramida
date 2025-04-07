<?php
include 'db/db_connect.php';
session_start();

if (isset($_SESSION['accountId'])) {
    $userId = $_SESSION['accountId'];

    $sql = "SELECT FK_role from user where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId]);
    $userRole = $stmt->fetch();
}
if ($userRole === null || $userRole['FK_role'] > 2) {
    header("Location: index.php");
    exit();
}

$filmId = $_GET['film_id'];
$screeningId = $_GET['screening_id'];

$sql = "SELECT FK_role from user where id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION['accountId']]);
$userRole = $stmt->fetch();

if ($userRole === null || $userRole['FK_role'] > 2) {
    header("Location: index.php");
    exit();
} else {
    // $sql = "DELETE FROM film_screening WHERE id = ?";
    // $stmt = $conn->prepare($sql);
    // $stmt->execute([$screeningId]);
}

header("Location: manage_screening.php?id=" . $filmId);
exit();

?>