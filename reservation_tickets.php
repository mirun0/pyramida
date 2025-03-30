<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vyberte vstupenky</title>
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

// TODO: nejaka podminka pro check toho $_GET["screening_id"]

$sql = "call get_ticket_information(?);";
$stmt = $conn->prepare($sql);
$stmt->execute([$_GET["screening_id"]]);
$info = $stmt->fetch();

$dateObj = new DateTime($info["datetime"]);
$formattedDate = $dateObj->format("d. F Y, H:i");

$filename = "halls/" . $info["hall_id"] . ".txt";

if (!file_exists($filename)) {
    die("Soubor nenalezen!");
}

$file = fopen($filename, "r");
if (!$file) {
    die("Nepodařilo se otevřít soubor!");
}

$layout = [];
while (($line = fgets($file)) !== false) {
    $layout[] = str_split(rtrim($line, "\r\n"));
}

fclose($file);

$rowCount = count($layout);
$colCount = count($layout[0]);

$fontsize = 1 / $colCount * 20;



$sql = "call get_seat_information(?);";
$stmt = $conn->prepare($sql);
$stmt->execute([$_GET["screening_id"]]);
$reserved_seats = $stmt->fetchAll();

$reserved_seats_ids = array_column($reserved_seats, 'id');


?>
<?php include "layout/reservation_nav.php" ?>

<style>
    button.active {
        color: black !important;
    }

    .seat-grid {
        grid-template-columns: repeat(<?php echo $colCount; ?>, 1fr);
        font-size: <?php echo $fontsize; ?>vw;
    }
</style>


<div id="main-container" class="container mt-4">
        
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation" id="nav-links">
                <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" role="tab" disabled style="color: white;">Počet míst</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2"  role="tab" disabled style="color: white;">Výběr míst</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" role="tab" disabled style="color: white;">Platba</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="myTabContent">
            <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                <!-- Obsah první karty -->
                <div class="row mt-4 d-flex flex-column flex-md-row">
            <!-- Levá část s informacemi -->
                    <div class="col-md-4 mb-3">
                        <img class="mb-2" src="img/<?= $info['image']?>" alt="<?= $info['name'] ?>" style="width: 70%; border-radius: 7px;">
                        <p style="font-weight:bold;"><?= $info["name"] ?></p>
                        <p><?= $formattedDate ?></p>
                        <p>Sál <?= $info["hall_id"] ?></p>
                    </div>
            
            <!-- Pravá část s výběrem míst -->
                    <div class="col-md-8">
                        <h3 class="mb-5">Vyberte počet míst:</h3>
                        <div class="mb-3" style="display: flex; align-items: center; justify-content: space-between;">
                            <label for="dospeli" style="width: 20%;" class="form-label mb-0">Dospělý</label>
                            <span><?= $info["price"] ?> Kč / kus</span>
                            <input type="number" style="width: 15%;" class="form-control" id="dospeli" value=0 min=0 max=10>
                        </div>
                        <div class="mb-5" style="display: flex; align-items: center; justify-content: space-between;">
                            <label for="deti" style="width: 20%;" class="form-label mb-0">Dítě / Student do 18 let</label>
                            <span><?= $info["price"]*0.75 ?> Kč / kus</span>
                            <input type="number" style="width: 15%;" class="form-control" id="deti" value=0 min=0 max=10>
                        </div>
                        <hr class="separator">
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab2" role="tabpanel">
            <h2 class="mb-4">Vyberte místa</h2>
            <p id="remaining-seats" style="color: red;">Zbývající místa na výběr: 0</p>
            <div class="container" style="padding: 10px; display: flex; align-items: center;">
                <div class='box seat' style="width: 20px; height: 20px;"></div>
                <span class="px-2">Dostupné</span>
                <div class='box disabled-seat ms-2' style="width: 20px; height: 20px;"></div>
                <span class="px-2">Nedostupné</span>
                <div class='box selected-seat ms-2' style="width: 20px; height: 20px;"></div>
                <span class="px-2">Vybrané</span>
            </div>
            <div class="container bg-dark" style="border-radius: 8px; padding: 10px;">
        <h4 class="text-center" style="width: 100%; border: 1.5px solid white; padding-top: 0px; padding: 5px; border-radius: 5px;">Plátno</h4>
        <div id="seat-grid" class="seat-grid">
            <?php
            $seatId = 1;
            foreach ($layout as $row) {
                foreach ($row as $char) {
                    if ($char === 'o') {
                        echo "<div id='s-$seatId' class='box seat'>$seatId</div>";
                        $seatId++;
                    } elseif ($char === '.'){
                        echo "<div class='box empty'>&nbsp;</div>";
                    } elseif ($char === 'd'){
                        echo "<div id='s-$seatId' class='box disabled-seat'>$seatId</div>";
                        $seatId++;
                    }
                }
            }
            ?>
        </div>
        <h4 class="text-center"></h4>
            </div>
                </div>

                <div class="tab-pane fade" id="tab3" role="tabpanel">
                    <form id="create-reservation" method="POST" action="create_reservation.php?screening_id=<?= $_GET["screening_id"] ?>">
                    <div class="container mt-4">
            <!-- Rekapitulace objednávky -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Rekapitulace objednávky</div>
                <div class="card-body">
                    <p><strong>Počet dospělých:</strong> <span id="recap-adults"> </span></p>
                    <p><strong>Počet dětí:</strong> <span id="recap-children"> </span></p>
                    <p><strong>Celková cena:</strong> <span id="recap-price"> </span></p>
                    <input type="hidden" name="recap-price-form" id="recap-price-form">
                    <p><strong>Sedadla:</strong> <span id="recap-seats"> </span></p>
                    <input type="hidden" name="recap-seats-form" id="recap-seats-form">
                </div>
            </div>
            
            <!-- Výběr platby -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Možnosti platby</div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment" id="payment-card" checked value="card">
                        <label class="form-check-label" for="payment-card">Platební karta</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment" id="payment-transfer" value="transfer">
                        <label class="form-check-label" for="payment-transfer">Bankovní převod</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment" id="payment-cash" value="cash">
                        <label class="form-check-label" for="payment-cash">Hotově (na místě)</label>
                    </div>
                </div>
            </div>
            
            <!-- Zadání slevového kupónu -->
            <div class="card">
                <div class="card-header bg-dark text-white">Slevový kupón</div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" class="form-control" id="coupon-code" placeholder="Zadejte kód kupónu">
                        <button class="btn btn-primary" id="apply-coupon">Použít</button>
                    </div>
                    <small class="text-muted">Zadejte platný slevový kupón pro získání slevy.</small>
                </div>
            </div>
        </div>
            </div>
        </div>
        </form>
        <div class="d-flex justify-content-between mt-5">
            <button class="btn btn-primary" id="prevBtn">Zpět</button>
            <button class="btn btn-primary" id="nextBtn">Další</button>
        </div>

        </div>

    <script>
    let remaining_seats = 0;
    document.addEventListener("DOMContentLoaded", function () {
    const maxLimit = <?= $info["available_seats"] ?>;
    const input1 = document.getElementById("dospeli");
    const input2 = document.getElementById("deti");
    const warning = document.getElementById("warning");

    document.getElementById("nextBtn").disabled = true;
    function checkLimit() {
        const sum = parseInt(input1.value) + parseInt(input2.value);
        if (sum > maxLimit) {
            warning.textContent = `Maximální počet míst je ${maxLimit}!`;
            warning.style.display = "block";
        } else {
            warning.style.display = "none";
            remaining_seats = sum;
        }
        document.getElementById("remaining-seats").textContent = "Zbývající místa na výběr: " + remaining_seats;

        if(sum > 0) {
            document.getElementById("nextBtn").disabled = false;
        } else {
            document.getElementById("nextBtn").disabled = true;
        }
    }

    input1.addEventListener("change", checkLimit);
    input2.addEventListener("change", checkLimit);

    let seatDivs = document.querySelectorAll('div[id^="s-"]');
    let seatArray = Array.from(seatDivs);

    seatArray.forEach(seat => {
        seat.classList.remove("seat");
        seat.classList.add("disabled-seat")
    });

    let ids = <?= json_encode($reserved_seats_ids) ?>;

    for (let i = 0; i < ids.length; i++) {
        let seat = document.getElementById("s-" + ids[i]);
        seat.classList.remove("disabled-seat");
        seat.classList.add("seat");
        seat.addEventListener("click", function () {

            if(remaining_seats > 0 & seat.classList.contains("seat")) {
                seat.classList.add("selected-seat");
                seat.classList.remove("seat");
                remaining_seats--;
            } else if(seat.classList.contains("selected-seat")) {
                seat.classList.remove("selected-seat");
                seat.classList.add("seat");
                remaining_seats++;
            }

            if(remaining_seats > 0) {
                document.getElementById("nextBtn").disabled = true;
            } else {
                document.getElementById("nextBtn").disabled = false;
            }
            
            document.getElementById("remaining-seats").textContent = "Zbývající místa na výběr: " + remaining_seats;
        });
    }


    const tabs = ["tab1", "tab2", "tab3"];
            let currentIndex = 0;
            let submit = false;

            function showTab(index) {
                let tabButtons = document.querySelectorAll(".nav-link");
                let tabContents = document.querySelectorAll(".tab-pane");
                
                tabButtons.forEach(btn => btn.classList.remove("active"));
                tabContents.forEach(tab => tab.classList.remove("show", "active"));
                
                document.getElementById(`${tabs[index]}-tab`).classList.add("active");
                document.getElementById(tabs[index]).classList.add("show", "active");
            }

            document.getElementById("prevBtn").addEventListener("click", function () {
                if (currentIndex == 0) {
                    window.history.back();
                }

                if (currentIndex == 1) {
                    for (let i = 0; i < ids.length; i++) {
                        let seat = document.getElementById("s-" + ids[i]);
                        seat.classList.remove("selected-seat");
                        seat.classList.remove("disabled-seat");
                        seat.classList.add("seat");
                        remaining_seats = 0;
                    }
                }

                if (currentIndex > 0) {
                    currentIndex--;
                    showTab(currentIndex);
                }

                if(remaining_seats > 0) {
                    document.getElementById("nextBtn").disabled = true;
                } else {
                    document.getElementById("nextBtn").disabled = false;
                }
            });

            document.getElementById("nextBtn").addEventListener("click", function () {
                
                if (submit) {
                    document.getElementById("create-reservation").submit();
                }

                if (currentIndex < tabs.length - 1) {
                    currentIndex++;
                    showTab(currentIndex);
                }

                if(currentIndex == 1) {
                    checkLimit();
                }

                if(currentIndex == 2) {

                    let selSeats = document.querySelectorAll('div[id^="s-"].selected-seat');
                    let selSeatsArray = Array.from(selSeats);
                    console.log(selSeatsArray);

                    let seatsStr = "";
                    selSeatsArray.forEach(seat =>
                        seatsStr += seat.id.split("-")[1] + ","
                    );


                    document.getElementById("recap-adults").textContent = input1.value;
                    document.getElementById("recap-children").textContent = input2.value;
                    document.getElementById("recap-price").textContent = (parseInt(input1.value) * <?= $info["price"] ?>) + 
                                                                         (parseInt(input2.value) * <?= $info["price"]*0.75 ?>) + " Kč";
                    document.getElementById("recap-seats").textContent = seatsStr.slice(0, -1);

                    document.getElementById("recap-price-form").value = (parseInt(input1.value) * <?= $info["price"] ?>) + 
                    (parseInt(input2.value) * <?= $info["price"]*0.75 ?>);
                    document.getElementById("recap-seats-form").value = seatsStr.slice(0, -1);
                    submit = true;
                }


                if(remaining_seats > 0) {
                    document.getElementById("nextBtn").disabled = true;
                } else {
                    document.getElementById("nextBtn").disabled = false;
                }
            });

});
</script>

<!-- Varování -->
<div id="warning" style="display: none; position: fixed; bottom: 10px; left: 10px; background-color: red; color: white; padding: 10px; border-radius: 5px;">
    Maximální počet míst je 5!
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