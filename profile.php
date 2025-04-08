<!DOCTYPE html>
<html lang="cs" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="kino, pyramida">
    <meta name="description" content="Web kina Pyramida">
    <meta name="author" content="Pyramida">
    <link rel="icon" type="image/x-icon" href="icons/pyramida.webp">
    <link rel="stylesheet" href="css/style.css">
    <title>Profil uživatele</title>
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
        .profile-container {
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
        .form-control {
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
<body>
<?php
include 'db/db_connect.php';
session_start();
if (!isset($_SESSION['loggedAccount'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['accountId'];
$sql = "SELECT * FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'updated'): ?>
    <div class="alert alert-success">Změny byly úspěšně uloženy.</div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'duplicate'): ?>
    <div class="alert alert-danger">Akce se nezdařila.<br>Tento e-mail už někdo používá!</div>
<?php elseif (isset($_GET['error']) && $_GET['error'] === 'notMatch'): ?>
    <div class="alert alert-danger">Akce se nezdařila.<br>Nová hesla se neshodují!</div>
<?php elseif (isset($_GET['error']) && $_GET['error'] === 'wrongPassword'): ?>
    <div class="alert alert-danger">Akce se nezdařila.<br>Bylo zadáno špatné heslo!</div>
<?php endif; ?>

<div class="profile-container">
    <h3 class="text-center">Profil</h3>
    <p><strong>Jméno:</strong> <?php echo htmlspecialchars($user['firstName']); ?></p>
    <p><strong>Příjmení:</strong> <?php echo htmlspecialchars($user['lastName']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

    <button class="btn btn-primary w-100" onclick="toggleForm('edit_form')">Upravit údaje</button>
    <form id="edit_form" method="post" action="update_profile.php" style="display:none;">
        <div class="mt-3">
            <label for="edit_firstName" class="form-label">Nové jméno</label>
            <input type="text" class="form-control" id="edit_firstName" name="firstName" value="<?php echo $user['firstName'] ?>" required>
        </div>
        <div class="mt-3">
            <label for="edit_lastName" class="form-label">Nové příjmení</label>
            <input type="text" class="form-control" id="edit_lastName" name="lastName" value="<?php echo $user['lastName'] ?>" required>
        </div>
        <div class="mt-3">
            <label for="edit_email" class="form-label">Nový email</label>
            <input type="email" class="form-control" id="edit_email" name="email" value="<?php echo $user['email'] ?>" required>
        </div>
        <button type="submit" class="btn btn-success w-100 mt-3">Uložit změny</button>
    </form>

    <button class="btn btn-warning w-100 mt-3" onclick="toggleForm('password_form')">Změnit heslo</button>
    <form id="password_form" method="post" action="change_password.php" style="display:none;">
        <div class="mt-3">
            <label for="current_password" class="form-label">Současné heslo</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mt-3">
            <label for="new_password" class="form-label">Nové heslo</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mt-3">
            <label for="confirm_password" class="form-label">Potvrzení hesla</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-success w-100 mt-3">Změnit heslo</button>
    </form>

    <div class="text-center mt-3">
        <a href="user_reservation.php" class="btn btn-outline-secondary w-100">Moje rezervace</a>
    </div>

    <div class="text-center mt-3">
        <a href="index.php" class="btn btn-outline-secondary w-100">Zpět na hlavní stránku</a>
    </div>

    <form method="post" action="logout.php" class="mt-3">
        <button type="submit" class="btn btn-danger w-100">Odhlásit se</button>
    </form>
</div>

<script>
    // vymazani localStorage
    document.addEventListener("DOMContentLoaded", function () {
        localStorage.removeItem("savedEmail");
    });

    function toggleForm(formId) {
        var editForm = document.getElementById('edit_form');
        var passwordForm = document.getElementById('password_form');

        if (formId === 'edit_form') {
            editForm.style.display = (editForm.style.display === 'none' || editForm.style.display === '') ? 'block' : 'none';
            // zavřít druhý formulář
            passwordForm.style.display = 'none';
        } else if (formId === 'password_form') {
            passwordForm.style.display = (passwordForm.style.display === 'none' || passwordForm.style.display === '') ? 'block' : 'none';
            // zavřít druhý formulář
            editForm.style.display = 'none';
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>

