<?php
include 'db/db_connect.php';
session_start();

// Získání všech dostupných žánrů pro filtr
$sql = "SELECT id, name FROM genre";
$stmt = $conn->prepare($sql);
$stmt->execute();
$genres = $stmt->fetchAll();

// Získání aktuální stránky (výchozí je 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 18;
$offset = ($page - 1) * $limit;

// Získání parametrů pro filtrování
$selectedGenre = isset($_GET['genre']) ? (int)$_GET['genre'] : null;
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// SQL dotaz s podmínkami
$sql = "SELECT film.id AS film_id, film.name AS film_name, film.description, film.image AS film_image, genre.name AS genre_name, film.releaseDate AS film_date,
               COALESCE(AVG(review.stars), 0) AS average_rating 
        FROM film
        JOIN genre ON film.FK_genre = genre.id
        LEFT JOIN review ON film.id = review.FK_film";

$whereConditions = [];
$params = [];

if (!empty($searchTerm)) {
    $whereConditions[] = "film.name LIKE :search";
    $params[':search'] = '%' . $searchTerm . '%';
}

if ($selectedGenre) {
    $whereConditions[] = "genre.id = :genre";
    $params[':genre'] = $selectedGenre;
}

if (!empty($whereConditions)) {
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
}

$sql .= " GROUP BY film.id ORDER BY film.releaseDate DESC LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
foreach ($params as $key => &$val) {
    $stmt->bindParam($key, $val);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$films = $stmt->fetchAll();

// Počet filmů pro stránkování
$sql = "SELECT COUNT(*) FROM film JOIN genre ON film.FK_genre = genre.id";
if (!empty($whereConditions)) {
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
}
$stmt = $conn->prepare($sql);
foreach ($params as $key => &$val) {
    $stmt->bindParam($key, $val);
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
</head>
<body>
<?php include "layout/nav.php" ?>

<header class="text-center py-5">
    <h1>Promítané filmy</h1>
</header>

<div class="container">
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="form-label">Hledat podle názvu</label>
                        <input type="text" class="form-control" id="search" name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Zadejte název filmu">
                    </div>
                    <div class="col-md-4">
                        <label for="genre" class="form-label">Filtrovat podle žánru</label>
                        <select class="form-select" id="genre" name="genre">
                            <option value="0">Všechny žánry</option>
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?= $genre['id'] ?>" <?= ($selectedGenre == $genre['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($genre['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Hledat</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                        <a href="screening_of_film.php?film_id=<?= $film['film_id'] ?>" class="btn btn-primary">Zobrazit promítání</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div>
        <ul class="pagination justify-content-center">
            <?php
            if ($totalPages > 1) {
                $range = 3;
                $showDotsBefore = $page > ($range + 2);
                $showDotsAfter = $page < ($totalPages - ($range + 1));

                echo '<li class="page-item ' . ($page == 1 ? 'active' : '') . '">
                    <a class="page-link" href="?page=1' . (!empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '') . ($selectedGenre ? '&genre=' . $selectedGenre : '') . '">1</a>
                  </li>';

                if ($showDotsBefore) {
                    echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                }

                for ($i = max(2, $page - $range); $i <= min($totalPages - 1, $page + $range); $i++) {
                    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">
                        <a class="page-link" href="?page=' . $i . (!empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '') . ($selectedGenre ? '&genre=' . $selectedGenre : '') . '">' . $i . '</a>
                      </li>';
                }

                if ($showDotsAfter) {
                    echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                }

                echo '<li class="page-item ' . ($page == $totalPages ? 'active' : '') . '">
                    <a class="page-link" href="?page=' . $totalPages . (!empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '') . ($selectedGenre ? '&genre=' . $selectedGenre : '') . '">' . $totalPages . '</a>
                  </li>';
            }
            ?>
        </ul>
    </div>
</div>

<?php include "layout/footer.php" ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
