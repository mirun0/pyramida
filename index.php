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
    <div id="filmCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="img/dan.jpg" class="card-img-top" alt="Film 1">
                            <div class="card-body">
                                <h5 class="card-title">Film 1</h5>
                                <p class="card-text">Krátký popis filmu.</p>
                                <a href="#" class="btn btn-primary">Více informací</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="img/dan.jpg" class="card-img-top" alt="Film 2">
                            <div class="card-body">
                                <h5 class="card-title">Film 2</h5>
                                <p class="card-text">Krátký popis filmu.</p>
                                <a href="#" class="btn btn-primary">Více informací</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="img/dan.jpg" class="card-img-top" alt="Film 3">
                            <div class="card-body">
                                <h5 class="card-title">Film 3</h5>
                                <p class="card-text">Krátký popis filmu.</p>
                                <a href="#" class="btn btn-primary">Více informací</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="img/dan.jpg" class="card-img-top" alt="Film 4">
                            <div class="card-body">
                                <h5 class="card-title">Film 4</h5>
                                <p class="card-text">Krátký popis filmu.</p>
                                <a href="#" class="btn btn-primary">Více informací</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="img/dan.jpg" class="card-img-top" alt="Film 5">
                            <div class="card-body">
                                <h5 class="card-title">Film 5</h5>
                                <p class="card-text">Krátký popis filmu.</p>
                                <a href="#" class="btn btn-primary">Více informací</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="img/dan.jpg" class="card-img-top" alt="Film 6">
                            <div class="card-body">
                                <h5 class="card-title">Film 6</h5>
                                <p class="card-text">Krátký popis filmu.</p>
                                <a href="#" class="btn btn-primary">Více informací</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
