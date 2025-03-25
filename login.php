<!DOCTYPE html>
<html lang="cs" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="kino, pyramida">
    <meta name="description" content="Web kina Pyramida">
    <meta name="author" content="Pyramida">
    <link rel="icon" type="image/x-icon" href="icons/pyramida.webp">
    <title>Přihlášení</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #121212;
            color: #f3f3f3;
            flex-direction: column;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.1);
        }
        .alert {
            text-align: center;
        }
        .form-control, .input-group-text {
            background-color: #333;
            color: #f3f3f3;
            border: 1px solid #555;
        }
        .btn-primary {
            background-color: #6200ea;
            border: none;
        }
        .btn-outline-secondary {
            color: #f3f3f3;
            border-color: #f3f3f3;
        }
    </style>
</head>
<?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
    <div class="alert alert-success">Váš účet byl úspěšně vytvořen.</div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'wrongLogin'): ?>
    <div class="alert alert-danger">Špatné přihlašovací údaje!</div>
<?php endif; ?>
<body>
<?php
include 'db/db_connect.php';
session_start();
$verification = 0;

if (isset($_POST['send'])) {
    $email = $_POST['email'];
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $password = htmlentities($_POST['password']);

        $sql = "SELECT * FROM user WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        if (isset($result['password'])) {
            $verification = password_verify($password,$result['password'])?1:0;
        }

        if ($verification) {
            session_start();
            $_SESSION['loggedAccount'] = $verification;
            $_SESSION['accountId'] = $result['id'];
            $_SESSION['accountEmail'] = $email;
            $_SESSION['accountFirstName'] = $result['firstName'];
            $_SESSION['accountLastName'] = $result['lastName'];
            header("Location: profile.php");
            exit;
        } else {
            header("Location: login.php?error=wrongLogin");
            exit();
        }
    } else {
        echo '<script>alert("Zadejte validní email!");</script>';
        echo '<script>window.location.href = "login.php";</script>';
    }
}
?>
<body>
<div class="login-container">
    <h3 class="text-center">Přihlášení</h3>
    <form id="login_form" method="post">
        <div class="mb-3">
            <label for="input_email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                <input type="email" class="form-control" id="input_email" name="email" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="input_password" class="form-label">Heslo</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-lock"></i></span>
                <input type="password" class="form-control" id="input_password" name="password" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100" name="send">Přihlásit se</button>
    </form>
    <div class="text-center mt-3">
        <a href="index.php" class="btn btn-outline-secondary w-100">Zpět na hlavní stránku</a>
    </div>
    <div class="text-center mt-3">
        <p>Nemáte účet? <a href="register.php" style="color: #bb86fc;">Zaregistrujte se</a></p>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Předvyplnění e-mailu, pokud existuje v localStorage
        let savedEmail = localStorage.getItem("savedEmail");
        if (savedEmail) {
            document.getElementById("input_email").value = savedEmail;
        }
    });

    function saveEmail() {
        let email = document.getElementById("input_email").value;
        localStorage.setItem("savedEmail", email);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
