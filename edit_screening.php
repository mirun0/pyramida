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

$screeningId = (int)$_GET['screening_id'];

if (!isset($screeningId)) {
    header("Location: film_administration.php");
    exit;
}

$sql = "SELECT dateTime, price, FK_hall AS hall, film_screening.FK_film AS filmId, FK_film_has_dubbing AS filmHasDubbing, FK_film_has_subtitles AS filmHasSubtitles FROM film_screening 
	WHERE film_screening.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$screeningId]);
$screening = $stmt->fetch();

if (!isset($screening["filmId"])) {
    header("Location: film_administration.php");
    exit;
}
$filmId = $screening["filmId"];

$sql = "SELECT film_has_dubbing.id AS id, language.language AS language FROM film_has_dubbing 
    JOIN film ON film.id = film_has_dubbing.FK_film
    JOIN language ON language.id = film_has_dubbing.FK_language 
    WHERE film.id = ?";
$dubbings = $conn->prepare($sql);
$dubbings->execute([$filmId]);

$sql = "SELECT film_has_subtitles.id AS id, language.language AS language FROM film_has_subtitles
    JOIN film ON film.id = film_has_subtitles.FK_film
    JOIN language ON language.id = film_has_subtitles.FK_language 
    WHERE film.id = ?";
$subtitles = $conn->prepare($sql);
$subtitles->execute([$filmId]);

if (isset($_POST['update'])) {
    $dateTime = $_POST['dateTime'];
    $price = $_POST['price'];
    $hall = $_POST['hall'];
    $dubbing = $_POST['dubbing'];
    $subtitles = $_POST['subtitles'];

    if (empty($dateTime)) {
        $errors[] = "Neplatný datum";
    }
    if (empty($price) || !filter_var($price, FILTER_VALIDATE_INT)) {
        $errors[] = "Neplatná cena";
    }
    if (empty($hall)) {
        $errors[] = "Neplatná cena";
    } else {
        $stmt = $conn->query("SELECT * FROM hall WHERE id = $hall");
        if ($stmt->fetch() === null) $errors[] = "Vyberte platný sál.";
    }
    if (empty($dubbing)) {
        $errors[] = "Neplatný dabing";
    } else {
        $stmt = $conn->query("SELECT * FROM film_has_dubbing WHERE FK_film = $filmId AND id = $dubbing");
        if ($stmt->fetch() === null) $errors[] = "Neexistující dabing";
    }
    if ((empty($subtitles) || !filter_var($subtitles, FILTER_VALIDATE_INT)) && $subtitles != 0) {
        $errors[] = "Neplatné titulky";
    } else {
        $stmt = $conn->query("SELECT * FROM film_has_subtitles WHERE FK_film = $filmId AND id = $subtitles");
        if ($stmt->fetch() === null) $errors[] = "Neexistující titulky";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        $sql = "UPDATE film_screening SET dateTime = ?, price = ?, FK_hall = ?, FK_film_has_dubbing = ?, FK_film_has_subtitles = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$dateTime, $price, $hall, $dubbing, $subtitles, $screeningId]);

        header("Location: manage_screening.php?id=$filmId");
        exit;
    }
}
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide/dist/css/splide.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide/dist/js/splide.min.js"></script>
</head>
<body>
<?php
    include "layout/nav.php";
?>

<div class="container mt-4">
    <h2>Upravit promítání</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Datum a čas</label>
            <input type="datetime-local" name="dateTime" class="form-control" value="<?= $screening['dateTime'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Cena</label>
            <input type="number" name="price" class="form-control" value="<?= $screening['price'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Sál</label>
            <select name="hall" class="form-control">
                <option value="<?= $screening['hall'] ?>"><?= $screening['hall'] ?></option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Dabing</label>
            <select name="dubbing" class="form-control">
                <?php 
                    while ($row = $dubbings->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'" . ($row['id'] == $screening['filmHasDubbing'] ? " selected" : "") . ">{$row['language']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Titulky</label>
            <select name="subtitles" class="form-control">
                <option value="0">Žádné titulky</option>
                <?php 
                    while ($row = $subtitles->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'" . ($row['id'] == $screening['filmHasSubtitles'] ? " selected" : "") . ">{$row['language']}</option>";
                    }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" name="update">Uložit</button>
        <a href="manage_screening.php?id=<?= $filmId ?>" class="btn btn-secondary">Zpět</a>
    </form>
</div>

<?php include "layout/footer.php" ?>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>