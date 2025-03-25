<!DOCTYPE html>
<html lang="cs" data-bs-theme="dark">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="kino, pyramida">
    <meta name="description" content="Web kina Pyramida">
    <meta name="author" content="Pyramida">
    <link rel="icon" type="image/x-icon" href="icons/pyramida.webp">
    <title>Registrace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
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
        .register-container {
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
<?php if (isset($_GET['error']) && $_GET['error'] === 'duplicate'): ?>
    <div class="alert alert-danger">Akce se nezdařila.<br>Tento e-mail už někdo používá!</div>
<?php elseif (isset($_GET['error']) && $_GET['error'] === 'notValidEmail'): ?>
    <div class="alert alert-danger">Akce se nezdařila.<br>Tento e-mail není validní!</div>
<?php endif; ?>
<body>
<?php
include "db/db_connect.php"; //$conn

function checkDomainExistence($email) {
    $domain = explode('@', $email)[1];
    $mxRecords = dns_get_record($domain, DNS_MX);

    return !empty($mxRecords);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sql = "SELECT * FROM user";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $accounts = $stmt->fetchAll();

    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = 2;

    $sql = "SELECT COUNT(*) FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $emailExists = $stmt->fetchColumn() > 0;

    if (!$emailExists) {
        if(checkDomainExistence($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if($password === $password_confirm) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO user (firstName, lastName, email, password, FK_role) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$firstName, $lastName, $email, $hash, $role]);

                header("Location: login.php?success=registered");
                exit;
            }
        } else {
            header("Location: register.php?error=notValidEmail");
            exit;
        }
    } else {
        header("Location: register.php?error=duplicate");
        exit;
    }
}
?>
<div class="register-container">
    <h3 class="text-center">Registrace</h3>
    <form id="register_form" method="post">
        <div class="mb-3">
            <label for="input_firstName" class="form-label">Jméno</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control" id="input_firstName" name="firstName" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="input_lastName" class="form-label">Příjmení</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control" id="input_lastName" name="lastName" required>
            </div>
        </div>
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
        <div class="mb-3">
            <label for="input_password_confirm" class="form-label">Potvrzení hesla</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-lock"></i></span>
                <input type="password" class="form-control" id="input_password_confirm" name="password_confirm" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100">Registrovat se</button>
    </form>
    <div class="text-center mt-3">
        <a href="index.php" class="btn btn-outline-secondary w-100">Zpět na hlavní stránku</a>
    </div>
    <div class="text-center mt-3">
        <p>Máte již účet? <a href="login.php">Přihlaste se</a></p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>