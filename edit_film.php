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

$filmId = isset($_GET['id']) ? $_GET['id'] : 0;
$sql = "SELECT name, length, releaseDate, description, image, FK_genre AS genre FROM film WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$filmId]);
$film = $stmt->fetch();
if ($film == NULL) {
    header("Location: film_administration.php");
    exit;
}

$stmt = $conn->query("SELECT id, language FROM language");
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "CALL get_film_dubbings(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$filmId]);
$dubbings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "CALL get_film_subtitles(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$filmId]);
$subtitles = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $length = $_POST['length'];
    $releaseDate = $_POST['releaseDate'];
    $description = $_POST['description'];
    $genre = $_POST['genre'];
    $dubbingIds = isset($_POST['dubbing']) ? $_POST['dubbing'] : NULL;
    $subtitlesIds = isset($_POST['subtitles']) ? $_POST['subtitles'] : [];

    if (empty($name)) {
        $errors[] = "Název filmu je povinný.";
    }
    if (empty($length) || !filter_var($length, FILTER_VALIDATE_INT) || $length <= 0) {
        $errors[] = "Délka filmu musí být kladné číslo.";
    }
    if (empty($releaseDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $releaseDate)) {
        $errors[] = "Datum vydání není platné.";
    }
    if (empty($description)) {
        $errors[] = "Popis filmu je povinný.";
    }
    if (empty($genre) || !filter_var($genre, FILTER_VALIDATE_INT)) {
        $errors[] = "Vyberte platný žánr.";
    } else {
        $stmt = $conn->query("SELECT * FROM genre WHERE id = $genre");
        if ($stmt->fetch() === null) $errors[] = "Vyberte platný žánr.";
    }
    if (!isset($dubbingIds)) {
        $errors[] = "Musí být vybraný alespoň jeden dabing";
    }

    $filmId = (int)$_GET['id'];

    if (!empty($_FILES['image']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = "Obrázek musí být ve formátu JPG, PNG, GIF nebo WEBP.";
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        $uploadDir = 'img/';

        if (!empty($_FILES['image']['name'])) {
            $fileName = basename($_FILES['image']['name']);
            $targetFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $filePointer = 'img/' . $film["image"];
                if (unlink($filePointer)) {
                    echo ("$filePointer byl odstraněn");
                } else {
                    echo ("$filePointer se nepodařilo odstranit.");
                }
                $sql = "UPDATE film SET name = ?, length = ?, releaseDate = ?, description = ?, image = ?, FK_genre = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$name, $length, $releaseDate, $description, $fileName, $genre, $filmId]);
            } else {
                echo "<p style='color: red;'>Nepodařilo se nahrát obrázek.</p>";
            }
        } else {
            $sql = "UPDATE film SET name = ?, length = ?, releaseDate = ?, description = ?, FK_genre = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $length, $releaseDate, $description, $genre, $filmId]);
        }

        for ($i = 0; $i < count($languages); $i++) {
            $lanId = $languages[$i]['id'];
            $filmHasDubbing = in_array($lanId, array_column($dubbings, "languageId"));
            if ($filmHasDubbing && !in_array($lanId, $dubbingIds)) {
                $sql = "CALL delete_film_has_dubbing(?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$filmId, $lanId]);
            } else if (!$filmHasDubbing && in_array($lanId, $dubbingIds)) {
                $sql = "CALL add_film_has_dubbing(?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$filmId, $lanId]);
            }

            $filmHasSubtitles = in_array($lanId, array_column($subtitles, "languageId"));
            if ($filmHasSubtitles && !in_array($lanId, $subtitlesIds)) {
                $sql = "CALL delete_film_has_subtitles(?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$filmId, $lanId]);
            } else if (!$filmHasDubbing && in_array($lanId, $subtitlesIds)) {
                $sql = "CALL add_film_has_subtitles(?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$filmId, $lanId]);
            }
        }

        header("Location: film_administration.php");
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
    include "layout/admin_nav.php";
?>

<div class="container mt-4">
    <h2>Upravit film</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Název filmu</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($film['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Délka (minuty)</label>
            <input type="number" name="length" class="form-control" value="<?= $film['length'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Datum vydání</label>
            <input type="date" name="releaseDate" class="form-control" value="<?= $film['releaseDate'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Popis</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($film['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Obrázek</label>
            <div>
                <?php if (!empty($film['image'])): ?>
                    <img id="frame" src="img/<?= htmlspecialchars($film['image']) ?>" alt="Náhled obrázku" style="max-height: 150px; display: block; margin-bottom: 10px;">
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/*" onchange="preview()">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Žánr</label>
            <select name="genre" class="form-control">
                <?php
                $genres = $conn->query("SELECT * FROM genre");
                while ($row = $genres->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'" . ($row['id'] == $film['genre'] ? " selected" : "") . ">{$row['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Dabing</label><br>
            <?php 
                for ($i = 0; $i < count($languages); $i++) {
                    $languageId = $languages[$i]['id'];
                    $filmHasDubbing = in_array($languageId, array_column($dubbings, "languageId"));
                    $d = array_filter($dubbings, function($dubb) use ($languageId) { return $dubb["languageId"] === $languageId; });
                    $d = reset($d);
                    $hasScreening = !empty($d) && $d["haveScreening"];
                    echo "<div class='form-check form-check-inline'>
                        <input class='form-check-input' type='checkbox' name='dubbing[]' value='{$languageId}' id='dub{$i}'"
                            . ($filmHasDubbing ? " checked" . ($hasScreening ? " onclick='return false' style='pointer-events: none; filter: brightness(50%);'" : "") : "") . ">
                        <label class='form-check-label'" . ($hasScreening ? " onclick='return false' style='pointer-events: none; filter: brightness(50%);'" : "") . "for='dub{$i}'>{$languages[$i]['language']}</label>
                        </div>";
                }
            ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Titulky</label><br>
            <?php 
                for ($i = 0; $i < count($languages); $i++) {
                    $languageId = $languages[$i]['id'];
                    $filmHasSubtitles = in_array($languageId, array_column($subtitles, "languageId"));
                    $s = array_filter($subtitles, function($sub) use ($languageId) { return $sub["languageId"] === $languageId; });
                    $s = reset($s);
                    $hasScreening = !empty($s) && $s["haveScreening"];
                    echo "<div class='form-check form-check-inline'>
                        <input class='form-check-input' type='checkbox' name='subtitles[]' value='{$languages[$i]['id']}' id='sub{$i}'"
                            . ($filmHasSubtitles ? " checked" . ($hasScreening ? " onclick='return false' style='pointer-events: none; filter: brightness(50%);'" : "") : "") . ">
                        <label class='form-check-label'" . ($hasScreening ? " onclick='return false' style='pointer-events: none; filter: brightness(50%);'" : "") . "for='sub{$i}'>{$languages[$i]['language']}</label>
                        </div>";
                }
            ?>
        </div>
        <button type="submit" class="btn btn-primary" name="update">Uložit</button>
        <a href="film_administration.php" class="btn btn-secondary">Zpět</a>
    </form>
</div>

<?php include "layout/footer.php" ?>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
    function preview() {
        frame.src = URL.createObjectURL(event.target.files[0]);
    }
</script>

</body>
</html>