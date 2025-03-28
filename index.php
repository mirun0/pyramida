<?php
include 'db/db_connect.php';

$sql = "SELECT * FROM latest_films limit 6";
$stmt = $conn->prepare($sql);
$stmt->execute();
$newestFilms = $stmt->fetchAll();

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
    <h1>Vítejte v našem kině</h1>
    <p>Podívejte se na nejnovější filmy a užijte si skvělý filmový zážitek!</p>
</header>

<div class="container">
    <h2>Nejnovějsí filmy</h2><br>
    <div id="filmCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach (array_chunk($newestFilms, 3) as $index => $filmGroup): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <div class="row">
                        <?php foreach ($filmGroup as $film): ?>
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <img src="img/<?= htmlspecialchars($film['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($film['film_name']) ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($film['film_name']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($film['description']) ?></p>
                                        <a href="#" class="btn btn-primary">Více informací</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#filmCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#filmCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<?php include "layout/footer.php" ?>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
