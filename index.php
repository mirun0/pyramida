<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kino svět</title>
    <link rel="icon" type="image/x-icon" href="icons/pyramida.webp">
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="fonts/6.0/css/all.min.css" type="text/css" rel="stylesheet">
    
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
        }
        .navbar {
            background-color: #1c1c1c;
        }
        .card {
            background-color: #1e1e1e;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Kino</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Program</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Ceník</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Kontakt</a></li>
                </ul>
            </div>
        </div>
    </nav>

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

    <footer class="text-center py-4 mt-5" style="background-color: #1c1c1c;">
        <p>&copy; 2025 Kino. Všechna práva vyhrazena.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script type="module" src="js/bootstrap.bundle.min.js"></script>
</body>
</html>