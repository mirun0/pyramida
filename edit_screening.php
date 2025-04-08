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

$sql = "SELECT 
            DATE_FORMAT(dateTime, '%Y-%m-%d') AS date,
            DATE_FORMAT(dateTime, '%H:%i') AS time,
            price, FK_hall AS hall, 
            film.id AS filmId,
            film.length AS filmLength,
            FK_film_has_dubbing AS filmHasDubbing, 
            FK_film_has_subtitles AS filmHasSubtitles 
        FROM film_screening 
        JOIN film ON film.id = film_screening.FK_film 
        WHERE film_screening.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$screeningId]);
$screening = $stmt->fetch();

if (!isset($screening["filmId"])) {
    header("Location: film_administration.php");
    exit;
}
$filmId = $screening["filmId"];
$filmLength = $screening["filmLength"];

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

$sql = "SELECT id FROM hall";
$halls = $conn->query($sql);

if (isset($_POST['update'])) {
    $date = $_POST['date'];
    $hall = $_POST['hall'];
    $time = $_POST['time'];
    $price = $_POST['price'];
    $dubbing = $_POST['dubbing'];
    $subtitles = $_POST['subtitles'];

    if (empty($hall)) {
        $errors[] = "Neplatná cena";
    } else {
        $stmt = $conn->query("SELECT * FROM hall WHERE id = $hall");
        if ($stmt->fetch() === null) $errors[] = "Vyberte platný sál.";
    }
    if (empty($date) || empty($time)) {
        $errors[] = "Neplatný datum nebo čas";
    } else {
        $sql = "call validate_screening_time(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$hall, $screeningId, $date, $time]);
        $isValidScreeningTime = $stmt->fetch();
        $stmt->closeCursor();
        if ($isValidScreeningTime['result'] === FALSE)
            $errors[] = "Čas se překrývá s jiným promítáním";
    }
    if (empty($price) || !filter_var($price, FILTER_VALIDATE_INT)) {
        $errors[] = "Neplatná cena";
    }
    if (empty($dubbing) || !filter_var($dubbing, FILTER_VALIDATE_INT)) {
        $errors[] = "Neplatný dabing";
    } else {
        $stmt = $conn->query("SELECT * FROM film_has_dubbing WHERE FK_film = $filmId AND id = $dubbing");
        if ($stmt->fetch() == false) $errors[] = "Neexistující dabing";
    }
    if (!filter_var($subtitles, FILTER_VALIDATE_INT)) {
        $errors[] = "Neplatné titulky";
    } else if ($subtitles != -1) {
        $stmt = $conn->query("SELECT * FROM film_has_subtitles WHERE FK_film = $filmId AND id = $subtitles");
        if ($stmt->fetch() == false) $errors[] = "Neexistující titulky";
    } else $subtitles = null;

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>
                    alert('$error');
                </script>";
        }
    } else {
        $dateTime = $date . " " . $time;
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
        <input type="hidden" id="film_id" value="<?= $filmId ?>">
        <input type="hidden" id="film_length" value="<?= $filmLength ?>">
        <input type="hidden" id="screening_id" value="<?= $screeningId ?>">
        <input type="hidden" id="screening_time" value="<?= $screening["time"] ?>">
        <div class="mb-3">
            <label class="form-label">Datum</label>
            <input type="date" name="date" id="date" class="form-control" value="<?= $screening['date'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Sál</label>
            <select name="hall" id="hall" class="form-control">
                <?php 
                    while ($row = $halls->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'" . ($row['id'] == $screening['hall'] ? " selected" : "") . ">{$row['id']}</option>";
                    }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Čas</label>
            <select name="time" id="time" class="form-control">
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Cena</label>
            <input type="number" name="price" class="form-control" value="<?= $screening['price'] ?>" required>
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
                <option value="-1">Žádné titulky</option>
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

<div id="warning" style="display: block; position: fixed; bottom: 10px; left: 10px; background-color: red; color: white; padding: 10px; border-radius: 5px;"></div>

<?php include "layout/footer.php" ?>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/new_edit_screening.js"></script>
</body>
</html>