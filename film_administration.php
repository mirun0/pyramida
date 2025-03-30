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

if ($userRole['FK_role'] == 3 || !isset($_SESSION['accountId'])) {
    header("Location: index.php");
    exit();
}

// Nastavení stránkování
$filmsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $filmsPerPage;

// Získání parametrů pro vyhledávání
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$genreFilter = isset($_GET['genre']) ? (int)$_GET['genre'] : 0;

$sql = "SELECT film.id AS film_id, film.name AS film_name, film.description, film.image AS film_image, film.length AS film_length, film.releaseDate AS film_date,
               genre.id AS genre_id, genre.name AS genre_name, COALESCE(AVG(review.stars), 0) AS average_rating 
        FROM film
        JOIN genre ON film.FK_genre = genre.id
        LEFT JOIN review ON film.id = review.FK_film";

$whereConditions = [];
$params = [];

if (!empty($searchTerm)) {
    $whereConditions[] = "film.name LIKE :search";
    $params[':search'] = '%' . $searchTerm . '%';
}

if ($genreFilter > 0) {
    $whereConditions[] = "genre.id = :genre";
    $params[':genre'] = $genreFilter;
}

if (!empty($whereConditions)) {
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
}

$sql .= " GROUP BY film.id ORDER BY film.id DESC LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
foreach ($params as $key => &$val) {
    $stmt->bindParam($key, $val);
}
$stmt->bindParam(':limit', $filmsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$films = $stmt->fetchAll();

$genresQuery = "SELECT id, name FROM genre";
$genresStmt = $conn->query($genresQuery);
$allGenres = $genresStmt->fetchAll();

$countSql = "SELECT COUNT(*) FROM film JOIN genre ON film.FK_genre = genre.id";
if (!empty($whereConditions)) {
    $countSql .= " WHERE " . implode(" AND ", $whereConditions);
}
$totalFilmsStmt = $conn->prepare($countSql);
foreach ($params as $key => &$val) {
    if ($key !== ':limit' && $key !== ':offset') {
        $totalFilmsStmt->bindParam($key, $val);
    }
}
$totalFilmsStmt->execute();
$totalFilms = $totalFilmsStmt->fetchColumn();
$totalPages = ceil($totalFilms / $filmsPerPage);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Správa filmů | Kino Pyramida</title>
    <link rel="icon" type="image/x-icon" href="icons/pyramida.webp">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include "layout/admin_nav.php" ?>

<header class="text-center py-5">
    <h1>Seznam filmů</h1>
    <a href="add_film.php" class="btn btn-success">Přidat nový film</a>
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
                            <?php foreach ($allGenres as $genre): ?>
                                <option value="<?= $genre['id'] ?>" <?= ($genreFilter == $genre['id']) ? 'selected' : '' ?>>
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

    <?php foreach ($films as $film): ?>
        <div class="card mb-4">
            <div class="row g-0">
                <div class="col-md-2">
                    <img src="img/<?= htmlspecialchars($film['film_image']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($film['film_name']) ?>">
                </div>
                <div class="col-md-10">
                    <div class="card-body">
                        <h5 class="card-title"> <?= htmlspecialchars($film['film_name']) ?> </h5>
                        <p class="card-text"> <strong>Žánr:</strong> <?= htmlspecialchars($film['genre_name']) ?> </p>
                        <p class="card-text"> <strong>Délka:</strong> <?= htmlspecialchars($film['film_length']) ?> minut</p>
                        <p class="card-text"> <strong>Datum vydání:</strong> <?= htmlspecialchars($film['film_date']) ?> </p>
                        <p class="card-text"> <strong>Hodnocení:</strong> <?= number_format($film['average_rating'], 1) ?> ⭐</p>
                        <p class="card-text"> <?= nl2br(htmlspecialchars($film['description'])) ?> </p>
                        <a href="edit_film.php?id=<?= $film['film_id'] ?>" class="btn btn-warning">Upravit</a>
                        <a href="delete_film.php?id=<?= $film['film_id'] ?>" class="btn btn-danger" onclick="return confirm('Opravdu chcete tento film smazat?');">Smazat</a>
                        <a href="manage_screening.php?id=<?= $film['film_id'] ?>" class="btn btn-info">Správa promítání</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<div>
    <ul class="pagination justify-content-center">
        <?php
        if ($totalPages > 1) {
            $range = 3; // Počet stran kolem aktuální stránky
            $showDotsBefore = $page > ($range + 2);
            $showDotsAfter = $page < ($totalPages - ($range + 1));

            // První stránka
            echo '<li class="page-item ' . ($page == 1 ? 'active' : '') . '">
                    <a class="page-link" href="?page=1' . (!empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '') . ($genreFilter > 0 ? '&genre=' . $genreFilter : '') . '">1</a>
                  </li>';

            // Tři tečky před aktuální stránkou
            if ($showDotsBefore) {
                echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }

            // Stránky kolem aktuální stránky
            for ($i = max(2, $page - $range); $i <= min($totalPages - 1, $page + $range); $i++) {
                echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">
                        <a class="page-link" href="?page=' . $i . (!empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '') . ($genreFilter > 0 ? '&genre=' . $genreFilter : '') . '">' . $i . '</a>
                      </li>';
            }

            // Tři tečky za aktuální stránkou
            if ($showDotsAfter) {
                echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }

            // Poslední stránka
            if ($totalPages > 1) {
                echo '<li class="page-item ' . ($page == $totalPages ? 'active' : '') . '">
                        <a class="page-link" href="?page=' . $totalPages . (!empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '') . ($genreFilter > 0 ? '&genre=' . $genreFilter : '') . '">' . $totalPages . '</a>
                      </li>';
            }
        }
        ?>
    </ul>
</div>


<?php include "layout/footer.php" ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>