<?php

session_start();
if(!$_SESSION['loggedAccount']) {
    header('Location: login.php');
    exit();
}

include 'db/db_connect.php';
if (!isset($_GET['review_id']) || !isset($_GET['film_id'])) die("Skript nelze spustit samostatne!");

$reviewId = $_GET['review_id'];

$sql = "SELECT FK_role from user where id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION['accountId']]);
$userRole = $stmt->fetch();

$sql = "SELECT FK_user from review where review.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$reviewId]);
$reviewUserId = $stmt->fetch();

if (!isset($userRole) && ($userRole['FK_role'] > 2  || trim($reviewUserId['FK_user']) != trim($_SESSION['accountId']))) {
    header("Location: index.php");
    exit();
} else {
    $sql = "call delete_review(?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$reviewId]);
}

header("Location: screening_of_film.php?film_id=" . $_GET['film_id'] . "#reviews-anchor");
exit();

?>