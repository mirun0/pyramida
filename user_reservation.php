<?php
include 'db/db_connect.php';
session_start();
if (!isset($_SESSION['loggedAccount'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['accountId'];
$sql = "CALL bookings_of_user(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$userId]);
$bookings = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kino Pyramida</title>
    <link rel="icon" type="image/x-icon" href="icons/pyramida.webp">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .btn-outline-secondary {
            color: #f3f3f3;
            border-color: #f3f3f3;
        }
    </style>
</head>
<body>
<?php include "layout/nav.php" ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
        <h2 class="m-0">Moje rezervace</h2>
        <a href="profile.php" class="btn btn-outline-secondary">
            <i class="fa fa-user"></i> Zpět na profil
        </a>
    </div>
    <table class="table table-dark table-striped mt-3">
        <thead>
        <tr>
            <th>Film</th>
            <th>Datum a čas</th>
            <th>Sál</th>
            <th>Dabing</th>
            <th>Titulky</th>
            <th>Sedadla</th>
            <th>Cena</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?= htmlspecialchars($booking['film_name']) ?></td>
                <td><?= date('d.m.Y H:i', strtotime($booking['screening_time'])) ?></td>
                <td><?= htmlspecialchars($booking['hall_id']) ?></td>
                <td><?= htmlspecialchars($booking['dubbing_language'] ?? '—') ?></td>
                <td><?= htmlspecialchars($booking['subtitle_language'] ?? '—') ?></td>
                <td><?= htmlspecialchars($booking['seat_list']) ?></td>
                <td><?= htmlspecialchars(number_format($booking['booking_price'], 2)) ?> Kč</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include "layout/footer.php" ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>