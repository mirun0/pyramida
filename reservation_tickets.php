<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vyberte vstupenky</title>
    <link rel="icon" type="image/x-icon" href="icons/pyramida.webp">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php 
session_start();
if (!isset($_SESSION['loggedAccount'])) {
    header("Location: login.php");
    exit;
}
?>
<?php include "layout/reservation_nav.php" ?>


<div class="container mt-4" style="max-width: 70%;">
        
        <!-- Tabs -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#">Počet míst</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">Výběr míst</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">Rekapitulace</a>
            </li>
        </ul>

        <!-- Obsah první karty -->
        <div class="row mt-4 d-flex flex-column flex-md-row">
            <!-- Levá část s informacemi -->
            <div class="col-md-4 mb-3">
                <h4>Název filmu</h4>
                <p>Avengers: Endgame</p>
                <h4>Datum a čas</h4>
                <p>12. dubna 2025, 18:00</p>
                <h4>Číslo sálu</h4>
                <p>Sál 5</p>
                <h4>Jazyk a titulky</h4>
                <p>Český dabing + anglické titulky</p>
            </div>
            
            <!-- Pravá část s výběrem míst -->
            <div class="col-md-8">
                <h4>Vyberte počet míst</h4>
                <div class="mb-3">
                    <label for="dospeli" class="form-label">Dospělí</label>
                    <select class="form-select" id="dospeli">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2" selected>2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="deti" class="form-label">Děti</label>
                    <select class="form-select" id="deti">
                        <option value="0" selected>0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </div>
                
                <!-- Navigační tlačítka -->
                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-secondary">Zpět</button>
                    <button class="btn btn-primary">Dále</button>
                </div>
            </div>
        </div>
    </div>



<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>