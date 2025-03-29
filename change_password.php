<?php
include 'db/db_connect.php';
session_start();
if (!isset($_SESSION['loggedAccount'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accountId = $_SESSION['accountId'];
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    $sql = "SELECT password FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$accountId]);
    $result = $stmt->fetchColumn();

    $verification = password_verify($currentPassword, $result)?1:0;

    if ($verification) {
        if ($newPassword === $confirmPassword) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "SELECT change_user_password(?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$accountId, $hash]);

            header("Location: profile.php?success=updated");
            exit;
        } else {
            header("Location: profile.php?error=notMatch");
            exit;
        }
    } else {
        header("Location: profile.php?error=wrongPassword");
        exit;
    }
}
?>