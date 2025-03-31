<?php
include 'db/db_connect.php';
session_start();
if (!isset($_SESSION['loggedAccount'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['accountId'];
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];

    $sql = "SELECT COUNT(*) FROM user WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email, $userId]);
    $emailExists = $stmt->fetchColumn() > 0;

    if ($emailExists) {
        header("Location: profile.php?error=duplicate");
        exit;
    }

    $sql = "UPDATE user SET firstName = ?, lastName = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$firstName, $lastName, $email, $userId]);
    $_SESSION['accountEmail'] = $email;
    $_SESSION['accountFirstName'] = $firstName;
    $_SESSION['accountLastName'] = $lastName;

    header("Location: profile.php?success=updated");
    exit;

}
?>