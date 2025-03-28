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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide/dist/css/splide.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide/dist/js/splide.min.js"></script>
</head>
<body>
<?php include "layout/nav.php" ?>

<header class="text-center py-5">
    <h1>Vítejte v našem kině</h1>
    <p>Podívejte se na nejnovější filmy a užijte si skvělý filmový zážitek!</p>
</header>

<div class="container">
    <h2>Nejnovější filmy</h2><br>
    <div class="splide" id="filmSlider">
        <div class="splide__track">
            <ul class="splide__list">
                <!-- Dynamicky generované snímky -->
                <?php foreach ($newestFilms as $film): ?>
                    <li class="splide__slide">
                        <div class="film-card">
                            <img src="img/<?= htmlspecialchars($film['image']) ?>" alt="<?= htmlspecialchars($film['film_name']) ?>">
                            <h5><?= htmlspecialchars($film['film_name']) ?></h5>
                            <p><?= htmlspecialchars($film['description']) ?></p>
                            <a href="#" class="btn btn-primary">Více informací</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <!-- Navigační tlačítka -->
        <div class="splide__arrows">
            <button class="splide__arrow splide__arrow--prev">‹</button>
            <button class="splide__arrow splide__arrow--next">›</button>
        </div>
    </div>
</div>

<?php include "layout/footer.php" ?>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
    new Splide('#filmSlider', {
        type       : 'loop',
        perPage    : 3,
        gap        : '1rem',
        pagination: false,
        arrows     : true,
        breakpoints: {
            768: {
                perPage: 1,
            },
            1024: {
                perPage: 2,
            },
        },
    }).mount();
</script>

</body>
</html>
