<?php include 'db/db_connect.php'; ?>

<?php
session_start();
if (!isset($_SESSION['loggedAccount']) || $_SESSION['accountId'] !== 1 && $_SESSION['accountId'] !== 2) {
    header("Location: login.php");
    exit;
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
<?php include "layout/nav.php" ?>

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
            <input type="url" name="image" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Žánr</label>
            <select name="genre" class="form-control">
                <?php
                    $genres = $conn->query("SELECT * FROM genre");
                    while ($genre = $genres.fetch_assoc()) {
                        echo "<option value='{$genre['id']}'" . ($genre['id'] == $genre ? " selected" : "") . ">{$genre['name']}</option>";
                    }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Uložit</button>
        <a href="admin.php" class="btn btn-secondary">Zpět</a>
    </form>
</div>

<?php include "layout/footer.php" ?>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
