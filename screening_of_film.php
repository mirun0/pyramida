<?php
include 'db/db_connect.php';
session_start();
$userRole = null;
if (!isset($_SESSION['loggedAccount'])) {

} else {
    $userId = $_SESSION['accountId'];

    $sql = "SELECT FK_role from user where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId]);
    $userRole = $stmt->fetch();
}

$filmId = $_GET['film_id'] ?? 0;

$sql = "SELECT film.id AS film_id, film.name AS film_name, film.description, film.image AS film_image, genre.name AS genre_name, 
               COALESCE(AVG(review.stars), 0) AS average_rating 
        FROM film
        JOIN genre ON film.FK_genre = genre.id
        LEFT JOIN review ON film.id = review.FK_film 
        WHERE film.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$filmId]);
$film = $stmt->fetch();

if (!$film) {
    echo "<h1>Film nenalezen</h1>";
    exit;
}

$sql = "CALL upcoming_screenings_for_film(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$filmId]);
$screenings = $stmt->fetchAll();


$sql = "CALL get_all_reviews(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$filmId]);
$reviews = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($film['film_name']) ?> | Kino Pyramida</title>
    <link rel="icon" type="image/x-icon" href="icons/pyramida.webp">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include "layout/nav.php" ?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <img src="img/<?= htmlspecialchars($film['film_image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($film['film_name']) ?>">
        </div>
        <div class="col-md-8">
            <h1><?= htmlspecialchars($film['film_name']) ?></h1>
            <p><strong>Žánr:</strong> <?= htmlspecialchars($film['genre_name']) ?></p>
            <div>
                <strong>Hodnocení:</strong>
                <span id="film-stars" class="text-warning" style="color: white">
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <span style="color: white"><?= number_format($film['average_rating'], 1) ?>/5</span>
                </span>
            </div>
            <p><?= nl2br(htmlspecialchars($film['description'])) ?></p>
        </div>
    </div>

    <h2 class="mt-5">Nadcházející promítání</h2>
    <table class="table table-dark table-striped mt-3">
        <thead>
        <tr>
            <th>Datum a čas</th>
            <th>Sál</th>
            <th>Jazyk</th>
            <th>Titulky</th>
            <th>Volná místa</th>
            <th>Akce</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($screenings as $screening): ?>
            <tr>
                <td><?= date('d.m.Y H:i', strtotime($screening['dateTime'])) ?></td>
                <td><?= htmlspecialchars($screening['hall_id']) ?></td>
                <td><?= htmlspecialchars($screening['dubbing_language']) ?></td>
                <td><?= htmlspecialchars($screening['subtitles_language']) ?></td>
                <td><?= htmlspecialchars($screening['available_seats']) ?> / <?= htmlspecialchars($screening['total_seats']) ?></td>
                <td>
                    <a href="reservation_tickets.php?screening_id=<?= $screening['screening_id'] ?>" class="btn btn-primary">Rezervovat</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="mt-5">Recenze</h2>
    
    <style>
  #rating-stars i {
    cursor: pointer;
    transition: transform 0.2s ease;
  }

  #rating-stars i:hover {
    transform: scale(1.2);
  }
</style>

  <div class="container mt-4" id="reviews-anchor">  

  <div class="card mb-4">
        <div class="card-body">
        <h5 class="mb-3">Přidat recenzi:</h5>
        <form action="add_review.php?film_id=<?= $_GET["film_id"] ?>" method="POST">

    <div class="mb-3">
    <span class="text-warning" id="rating-stars">
            <span style="color: white">Hodnocení: </span>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-regular fa-star"></i>
            <i class="fa-regular fa-star"></i>
            <span style="color: red">(vyberte počet hvězd)</span>
        </span>
    </div>
    <input type="hidden" name="stars" id="stars" value="10">

    <div class="mb-3">
        <?= !isset($userId) ? '<span class="px-2 ps-0" style="color: gray">Pro napsání recenze se přihlaste.</span>' : '' ?>
        <textarea <?= !isset($userId) ? 'disabled' : '' ?> class="form-control bg-dark" style="border: none; color:white;" id="text" name="text" rows="2" required></textarea>
    </div>

    <button type="submit" <?= !isset($userId) ? 'disabled' : '' ?> class="btn btn-primary">Odeslat</button>
  </form>
        </div>
    </div>

  <div class="d-flex flex-column gap-3">
    <?php if(count($reviews) == 0) { ?>
        <div class="p-3 text-center">
            <p>Film zatím nemá žádné recenze, buď první kdo zareaguje.</p>
        </div>
    <?php }?>

    <?php foreach ($reviews as $review): ?>
        <div class="p-3 rounded bg-dark">
        <div class="d-flex justify-content-between mb-2">
            <strong><?= htmlspecialchars($review['username']) ?></strong>
            <?php if(isset($userId) && ($userRole['FK_role'] < 2 || $review['user_id'] == $userId)) { ?>
                <div class="px-2 gap-2 d-flex">
                    <?php if($review['user_id'] == $userId) { ?>
                        <a href="" id="edit-review"><i class="fa-solid fa-pen btn-primary"></i></a>
                    <?php } ?>
                <a href="delete_review?film_id=<?= $_GET["film_id"] ?>&review_id=<?= $review['id'] ?>" onclick="return confirm('Opravdu chcete tuto recenzi smazat?');"><i class="fa-solid fa-trash" style="color: red;"></i></a>
                </div>
            <?php } ?>
        </div>
        <div class="mb-2"><small style="color: gray"><?= htmlspecialchars($review['datetime']) ?></small></div>
        <div class="mb-2">
            <span class="text-warning">
                <?php for($i = 1; $i <= $review['stars']; $i++) { ?>
                    <i class="fa-solid fa-star"></i>
                <?php } for($i = 1; $i <= 5 - $review['stars']; $i++) { ?>
                    <i class="fa-regular fa-star"></i>
                <?php } ?>
                <span style="color: white;"><?= htmlspecialchars($review['stars']) ?>/5</span>
            </span>
        </div>
        <p><?= htmlspecialchars($review['text']) ?></p>
        </div>
    <?php endforeach; ?>

  </div>
</div>

</div>

<script>
    const stars = document.querySelectorAll('#rating-stars i');
    const ratingInput = document.getElementById('stars');
    ratingInput.value = 3;              

    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
        const rating = index + 1;
        ratingInput.value = rating;

        stars.forEach((s, i) => {
            if (i < rating) {
                s.classList.remove('fa-regular');
                s.classList.add('fa-solid');
            } else {
                s.classList.remove('fa-solid');
                s.classList.add('fa-regular');
            }
        });
        });
    });

    const editReview = document.getElementById('edit-review');

    editReview.addEventListener('click', () => {
        // dodelat edit recenze
    });


    const filmStars = document.querySelectorAll('#film-stars i');          
    const avgRatingNum = <?= json_encode(number_format($film['average_rating'], 1)) ?> 
    const avgRating = Math.round(avgRatingNum * 2);
    console.log(avgRating);

    let pointer = 0;
    for(let i = 0; i <= 10; i += 2) {
        if(i < avgRating) {
            filmStars[pointer].classList.remove("fa-regular");
            filmStars[pointer].classList.add("fa-solid");
            
            if(i + 1 == avgRating) {
                filmStars[pointer].classList.remove("fa-star");
                filmStars[pointer].classList.add("fa-star-half-stroke");
                break;
            }
            pointer++;
        }
    }
</script>

<?php include "layout/footer.php" ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>