<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulace rezervace</title>
    <link rel="icon" type="image/x-icon" href="icons/pyramida.webp">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/booking.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<?php
include 'db/db_connect.php';
session_start();
if (!isset($_SESSION['loggedAccount'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookingId = $_GET['booking_id'];
    $seats = $_POST["seats"];

    if (empty($bookingId  || !filter_var($bookingId, FILTER_VALIDATE_INT) || $bookingId < 0)) {
        $errors[] = "Není vybraná rezervace.";
    }

    if (empty($seats)) {
        $errors[] = "Není k dispozici informace o sedadlech.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        $sql = "call get_booking_information(?);";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$bookingId]);

        $result = $stmt->fetchAll();
        $errorInfo = $stmt->errorInfo();

        if ($errorInfo[0] != '00000') {
            echo "Chyba: " . $errorInfo[2];
        }

    }
}


?>
<?php include "layout/nav.php" ?>

<style>
    button.active {
        color: black !important;
    }

    .seat-grid {
        grid-template-columns: repeat(<?php echo $colCount; ?>, 1fr);
        font-size: <?php echo $fontsize; ?>vw;
    }
</style>


<div id="main-container" class="container mt-5">
            <h2 class="text-white text-center mb-5">
                Potvrzení o rezervaci
            </h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= $result[0]['name'] ?></h5>
                <p class="card-text"><strong>Datum a čas:</strong> <?= $result[0]['datetime'] ?></p>
                <p class="card-text"><strong>Cena:</strong> <?= $result[0]['price'] ?> Kč</p>
                <p class="card-text"><strong>Sál:</strong> <?= $result[0]['hall_id'] ?></p>
                <p class="card-text"><strong>Sedadla:</strong> <?= $_POST["seats"] ?></p>
                <p class="card-text"><strong>Dabing:</strong> <?= $result[0]['dubbing'] ?></p>
                <p class="card-text"><strong>Titulky:</strong> <?= $result[0]['subtitles'] ?></p>
            </div>
        </div>
        <a href="/pyramida" style="" class="btn btn-primary mt-3">Zpět na hlavní stránku</a>
    </div>

<style>
    html, body {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.container {
    flex: 1;
}

.footer {
    width: 100%;
}
</style>
<footer class="footer text-center py-4 mt-5 w-100 bg-dark" style="background-color: #1c1c1c;">
</footer>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>