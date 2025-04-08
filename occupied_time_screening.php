<?php
    include 'db/db_connect.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date = $_POST['date'];
        $hallId = $_POST['hallId'];

        if (isset($date) && isset($hallId) && filter_var($hallId, FILTER_VALIDATE_INT)) {
            $sql = "SELECT 
                        DATE_FORMAT(film_screening.dateTime, '%H:%i') AS startTime,
                        DATE_FORMAT(DATE_ADD(film_screening.dateTime, INTERVAL film.length MINUTE), '%H:%i') AS endTime,
                        film_screening.id AS screeningId
                    FROM hall
                    JOIN film_screening ON film_screening.FK_hall = hall.id
                    JOIN film ON film.id = film_screening.FK_film
                    WHERE hall.id = ? AND DATE_FORMAT(film_screening.dateTime, '%Y-%m-%d') = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$hallId, $date]);
            $times = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['ok' => TRUE, 'times' => $times]);
        } else
            echo json_encode(['ok' => FALSE, 'error' => "Neplatné hodnoty"]);
    }
?>