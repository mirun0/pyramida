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

$filmId = $_GET['id'] ?? 0;

$sql = "SELECT film.id AS film_id, film.name AS film_name, film.description, film.image AS film_image, genre.name AS genre_name, 
               COALESCE(AVG(review.stars), 0) AS average_rating 
        FROM film
        JOIN genre ON film.FK_genre = genre.id
        LEFT JOIN review ON film.id = review.FK_film 
        WHERE film.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$filmId]);
$film = $stmt->fetch();

if (!$film) {
    echo "<h1>Film nenalezen</h1>";
    exit;
}

$sql = "CALL upcoming_screenings_for_film(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$filmId]);
$screenings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($film['film_name']) ?> | Kino Pyramida</title>
    <link rel="icon" type="image/x-icon" href="icons/pyramida.webp">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include "layout/admin_nav.php" ?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <img src="img/<?= htmlspecialchars($film['film_image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($film['film_name']) ?>">
        </div>
        <div class="col-md-8">
            <h1><?= htmlspecialchars($film['film_name']) ?></h1>
            <p><strong>Žánr:</strong> <?= htmlspecialchars($film['genre_name']) ?></p>
            <p><strong>Hodnocení:</strong> <?= number_format($film['average_rating'], 1) ?> ⭐</p>
            <p><?= nl2br(htmlspecialchars($film['description'])) ?></p>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-5">
        <h2 class="m-0">Nadcházející promítání</h2>
        <a href="add_screening.php?film_id=<?= $filmId ?>" class="btn btn-success">
            <i class="fa fa-plus"></i> Přidat promítání
        </a>
    </div>
    <table class="table table-dark table-striped mt-3">
        <thead>
        <tr>
            <th>Datum a čas</th>
            <th>Sál</th>
            <th>Jazyk</th>
            <th>Titulky</th>
            <th>Volná místa</th>
            <th>Akce</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($screenings as $screening): ?>
            <tr>
                <td><?= date('d.m.Y H:i', strtotime($screening['dateTime'])) ?></td>
                <td><?= htmlspecialchars($screening['hall_id']) ?></td>
                <td><?= htmlspecialchars($screening['dubbing_language']) ?></td>
                <td><?= htmlspecialchars($screening['subtitles_language']) ?></td>
                <td><?= htmlspecialchars($screening['available_seats']) ?> / <?= htmlspecialchars($screening['total_seats']) ?></td>
                <td>
                <a href="edit_screening.php?screening_id=<?= $screening['screening_id'] ?>" class="btn btn-warning">Upravit</a>
                <a href="cancle_screening.php?film_id=<?= $filmId ?>&screening_id=<?= $screening['screening_id'] ?>" class="btn btn-danger" onclick="confirm('Opravdu chcete zrušit promítání?')">Zrušit</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include "layout/footer.php" ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>