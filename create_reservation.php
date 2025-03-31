<?php
include 'db/db_connect.php';
session_start();
if (!isset($_SESSION['loggedAccount'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accountId = $_SESSION['accountId'];
    $screeningId = $_GET['screening_id'];
    $price = $_POST['recap-price-form'];
    $seats = $_POST['recap-seats-form'];

    if (empty($accountId)) {
        $errors[] = "Uživatel není přihlášen!";
    }

    if (empty($screeningId)) {
        $errors[] = "Toto promítání není k dispozici.";
    }

    if (empty($screeningId)) {
        $errors[] = "Toto promítání není k dispozici.";
    }

    if (empty($price) || !filter_var($price, FILTER_VALIDATE_INT) || $price < 0) {
        $errors[] = "Špatně zadaná cena.";
    }

    if (empty($seats)) {
        $errors[] = "Špatný formát rezervovaných sedadel.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        $sql = "call reserve_seats(?, ?, ?, ?, @booking_id); select @booking_id as booking_id;";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$accountId, $screeningId, $price, $seats]);

        $firstResult = $stmt->fetchAll();
        $errorInfo = $stmt->errorInfo();
        $stmt->nextRowset();

        $secondResult = $stmt->fetchAll();

        var_dump($secondResult);

        if ($errorInfo[0] != '00000') {
            echo "Chyba: " . $errorInfo[2];
        } else {
            //header("Location: reservation_summary?booking_id=". $secondResult[0]["booking_id"] .".php");
            //exit();
        }
    }
}
?>

<form id="hiddenForm" action="reservation_summary.php?booking_id=<?= $secondResult[0]["booking_id"] ?>" method="POST" style="display:none;">
    <input type="text" name="seats" id="seats" value="<?= $seats ?>">
</form>

<script>
    document.getElementById('hiddenForm').submit();
</script>