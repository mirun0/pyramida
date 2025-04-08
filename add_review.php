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
if ($userRole === null) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['film_id'])) die("Skript nelze spustit samostatne!");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $text = $_POST['text'];
    $stars = $_POST['stars'];
    $filmId = $_GET['film_id'];

    if (empty($text)) {
        $errors[] = "Text komentare je povinny.";
    }
    if (empty($stars) || !filter_var($stars, FILTER_VALIDATE_INT) || $stars <= 0) {
        $errors[] = "Pocet hvezd spatne zadany";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        $sql = "CALL add_review(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$text, $stars, $_SESSION['accountId'], $filmId]);

        $errorInfo = $stmt->errorInfo();

        if ($errorInfo[0] != '00000') {
            echo "Chyba: " . $errorInfo[2];
        }

        header("Location: screening_of_film.php?film_id=" . $filmId . "#reviews-anchor");
        exit();
    }
}
?>
