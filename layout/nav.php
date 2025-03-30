<?php
if (isset($_SESSION['accountId'])) {
    $userId = $_SESSION['accountId'];
    $sql = "SELECT get_user_full_name(?) as full_name";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId]);
    $userFullName = $stmt->fetch();

    $sql = "SELECT FK_role from user where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId]);
    $userRole = $stmt->fetch();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="icons/pyramida.png" alt="Pyramida" width="30" height="30" class="me-2">
            Kino Pyramida
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-lg-end text-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="schedule.php">Filmy</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Kontakty</a></li>
                <?php
                if (isset($userRole['FK_role']) &&  $userRole['FK_role'] < 2) echo '<li class="nav-item"><a class="nav-link" href="administration.php">Administrace</a></li>';
                ?>
                <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fa-solid fa-user"></i> <?php if (isset($_SESSION['loggedAccount'])) echo(htmlspecialchars($userFullName['full_name'])); ?></a></li>
            </ul>
        </div>
    </div>
</nav>