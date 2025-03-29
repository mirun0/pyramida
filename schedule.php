<?php
include 'db/db_connect.php';

// Získání všech dostupných žánrů pro filtr
$sql = "SELECT id, name FROM genre";
$stmt = $conn->prepare($sql);
$stmt->execute();
$genres = $stmt->fetchAll();

// Získání aktuální stránky (výchozí je 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 18;
$offset = ($page - 1) * $limit;

// Získání vybraného žánru (pokud byl vybrán)
$selectedGenre = isset($_GET['genre']) ? (int)$_GET['genre'] : null;

// SQL dotaz s filtrováním podle žánru (pokud je vybrán)
$sql = "SELECT film.id AS film_id, film.name AS film_name, film.description, film.image AS film_image, genre.name AS genre_name, 
               COALESCE(AVG(review.stars), 0) AS average_rating 
        FROM film
        JOIN genre ON film.FK_genre = genre.id
        LEFT JOIN review ON film.id = review.FK_film";

if ($selectedGenre) {
    $sql .= " WHERE genre.id = :genre";
}

$sql .= " GROUP BY film.id LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
if ($selectedGenre) {
    $stmt->bindParam(':genre', $selectedGenre, PDO::PARAM_INT);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$films = $stmt->fetchAll();

// Počet filmů pro výpočet celkových stránek
$sql = "SELECT COUNT(*) FROM film";
if ($selectedGenre) {
    $sql .= " WHERE FK_genre = :genre";
}
$stmt = $conn->prepare($sql);
if ($selectedGenre) {
    $stmt->bindParam(':genre', $selectedGenre, PDO::PARAM_INT);
}
$stmt->execute();
$totalFilms = $stmt->fetchColumn();
$totalPages = ceil($totalFilms / $limit);
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
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include "layout/nav.php" ?>

<header class="text-center py-5">
    <h1>Promítané filmy</h1>
</header>

<div class="container">
    <!-- Filtr podle žánru -->
    <form method="GET" class="mb-4">
        <label for="genre">Filtr podle žánru:</label>
        <select name="genre" id="genre" class="form-select w-auto d-inline-block">
            <option value="">Všechny žánry</option>
            <?php foreach ($genres as $genre): ?>
                <option value="<?= $genre['id'] ?>" <?= $selectedGenre == $genre['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($genre['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filtrovat</button>
    </form>

    <!-- Seznam filmů -->
    <div class="row">
        <?php foreach ($films as $film): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="img/<?= htmlspecialchars($film['film_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($film['film_name']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($film['film_name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($film['description']) ?></p>
                        <p><strong>Žánr:</strong> <?= htmlspecialchars($film['genre_name']) ?></p>
                        <p><strong>Hodnocení:</strong> <?= number_format($film['average_rating'], 1) ?> ★</p>
                        <a href="screeningOfFilm.php?film_id=<?= number_format($film['film_id']) ?>" class="btn btn-primary">Zobrazit promítání</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Stránkování -->
    <div>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&genre=<?= $selectedGenre ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
</div>

<?php include "layout/footer.php" ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
