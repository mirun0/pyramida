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
include 'db/db_connect.php';
session_start();
if (!isset($_SESSION['loggedAccount'])) {
    header("Location: login.php");
    exit;
}

// TODO: nejaka podminka pro check toho $_GET["screening_id"]

$sql = "call get_ticket_information(?);";
$stmt = $conn->prepare($sql);
$stmt->execute([$_GET["screening_id"]]);
$info = $stmt->fetch();

$dateObj = new DateTime($info["datetime"]);
$formattedDate = $dateObj->format("d. F Y, H:i");

?>
<?php include "layout/reservation_nav.php" ?>


<div class="container mt-4" style="max-width: 50%;">
        
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab">Počet míst</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab">Výběr míst</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab">Rekapitulace</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="myTabContent">
            <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                <!-- Obsah první karty -->
                <div class="row mt-4 d-flex flex-column flex-md-row">
            <!-- Levá část s informacemi -->
                    <div class="col-md-4 mb-3">
                        <p style="font-weight:bold;"><?= $info["name"] ?></p>
                        <p><?= $formattedDate ?></p>
                        <p>Sál <?= $info["hall_id"] ?></p>
                    </div>
            
            <!-- Pravá část s výběrem míst -->
                    <div class="col-md-8">
                        <h4>Vyberte počet míst</h4>
                        <div class="mb-3">
                            <label for="dospeli" class="form-label">Dospělí</label>
                            <input type="number" class="form-control" id="dospeli" value=0 min=0 max=15>
                        </div>
                        <div class="mb-3">
                            <label for="deti" class="form-label">Děti</label>
                            <input type="number" class="form-control" id="deti" value=0 min=0 max=10>
                        </div>

                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab2" role="tabpanel">
                <h3>Tab 2</h3>
                <p>Obsah druhého tabu.</p>
            </div>

            <div class="tab-pane fade" id="tab3" role="tabpanel">
                <h3>Tab 3</h3>
                <p>Obsah třetího tabu.</p>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-primary" id="prevBtn">Zpět</button>
            <button class="btn btn-primary" id="nextBtn">Další</button>
        </div>

    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
    const maxLimit = <?= $info["available_seats"] ?>;
    const input1 = document.getElementById("dospeli");
    const input2 = document.getElementById("deti");
    const warning = document.getElementById("warning");

    function checkLimit() {
        const sum = parseInt(input1.value) + parseInt(input2.value);
        if (sum > maxLimit) {
            warning.textContent = `Maximální počet míst je ${maxLimit}!`;
            warning.style.display = "block";
        } else {
            warning.style.display = "none";
        }
    }

    input1.addEventListener("change", checkLimit);
    input2.addEventListener("change", checkLimit);
});



document.addEventListener("DOMContentLoaded", function () {
            const tabs = ["tab1", "tab2", "tab3"];
            let currentIndex = 0;

            function showTab(index) {
                let tabButtons = document.querySelectorAll(".nav-link");
                let tabContents = document.querySelectorAll(".tab-pane");
                
                tabButtons.forEach(btn => btn.classList.remove("active"));
                tabContents.forEach(tab => tab.classList.remove("show", "active"));
                
                document.getElementById(`${tabs[index]}-tab`).classList.add("active");
                document.getElementById(tabs[index]).classList.add("show", "active");
            }

            document.getElementById("prevBtn").addEventListener("click", function () {
                if (currentIndex > 0) {
                    currentIndex--;
                    showTab(currentIndex);
                }
            });

            document.getElementById("nextBtn").addEventListener("click", function () {
                if (currentIndex < tabs.length - 1) {
                    currentIndex++;
                    showTab(currentIndex);
                }
            });
        });
</script>

<!-- Varování -->
<div id="warning" style="display: none; position: fixed; bottom: 10px; left: 10px; background-color: red; color: white; padding: 10px; border-radius: 5px;">
    Maximální počet míst je 5!
</div>


<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>