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

$stmt = $conn->query("SELECT id, language FROM language");
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $length = $_POST['length'];
    $releaseDate = $_POST['releaseDate'];
    $description = $_POST['description'];
    $genre = $_POST['genre'];
    $dubbingIds = $_POST['dubbing'];
    $subtitlesIds = isset($_POST['subtitles']) ? $_POST['subtitles'] : [];

    if (empty($name)) {
        $errors[] = "Název filmu je povinný.";
    }
    if (empty($length) || !filter_var($length, FILTER_VALIDATE_INT) || $length <= 0) {
        $errors[] = "Délka filmu musí být kladné číslo.";
    }
    if (empty($releaseDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $releaseDate)) {
        $errors[] = "Datum vydání není platné." . $releaseDate;
    }
    if (empty($description)) {
        $errors[] = "Popis filmu je povinný.";
    }
    if (empty($_POST['description'])) {
        $errors[] = "Popis filmu je povinný.";
    }
    if (empty($genre) || !filter_var($genre, FILTER_VALIDATE_INT)) {
        $errors[] = "Vyberte platný žánr.";
    }
    if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
        $errors[] = "Obrázek je povinný.";
    } else {
        $allowedTypes = ['image/jpeg'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = "Obrázek musí být ve formátu JPG, PNG, GIF nebo WEBP.";
        }
    }
    if (!isset($dubbingIds)) {
        $errors[] = "Musí být vybraný alespoň jeden dabing";
    }

    // Pokud jsou chyby, vypíšeme je
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        $uploadDir = 'img/';
        $fileName = basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $sql = "CALL add_film(?, ?, ?, ?, ?, ?, @newId)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $length, $releaseDate, $description, $fileName, $genre]);

            $result = $conn->query("SELECT @newId AS film_id")->fetch(PDO::FETCH_ASSOC);
            $filmId = $result['film_id'];
            foreach ($dubbingIds as $languageId) {
                $sql = "CALL add_film_has_dubbing(?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$filmId, $languageId]);
            }
            foreach ($subtitlesIds as $languageId) {
                $sql = "CALL add_film_has_dubbing(?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$filmId, $languageId]);
            }

            header("Location: film_administration.php");
            exit();
        } else
            echo "<p style='color: red;'>Nepodařilo se nahrát obrázek</p>";
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
<?php include "layout/admin_nav.php"?>

<div class="container mt-4">
    <h2></h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Název filmu</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Délka (minuty)</label>
            <input type="number" name="length" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Datum vydání</label>
            <input type="date" name="releaseDate" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Popis</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Obrázek</label>
            <div>
                <img id="frame" src="" style="max-height: 150px; max-width: 400px; display: block; margin-bottom: 10px;">
                <input type="file" name="image" class="form-control" accept="image/*" onchange="preview()">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Žánr</label>
            <select name="genre" class="form-control">
                <?php
                $genres = $conn->query("SELECT * FROM genre");
                $genres->execute();
                while ($row = $genres->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'" . ">{$row['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Dabing</label><br>
            <?php 
                for ($i = 0; $i < count($languages); $i++) {
                    echo "<div class='form-check form-check-inline'>
                        <input class='form-check-input' type='checkbox' name='dubbing[]' value='{$languages[$i]['id']}' id='dub{$i}'>
                        <label class='form-check-label' for='dub{$i}'>{$languages[$i]['language']}</label>
                        </div>";
                }
            ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Titulky</label><br>
            <?php 
                for ($i = 0; $i < count($languages); $i++) {
                    echo "<div class='form-check form-check-inline'>
                        <input class='form-check-input' type='checkbox' name='subtitles[]' value='{$languages[$i]['id']}' id='sub{$i}'>
                        <label class='form-check-label' for='sub{$i}'>{$languages[$i]['language']}</label>
                        </div>";
                }
            ?>
        </div>
        <button type="submit" class="btn btn-primary" name="add">Uložit</button>
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