<?php
declare(strict_types=1);

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    http_response_code(404);
    exit('Nieprawidłowy produkt.');
}

$productId = (int) $_GET['id'];

/* Pobieranie informacji o produktach */
$query = "
    SELECT 
        b.*,
        c.name AS category,
        s.name AS supplier
    FROM books b
    JOIN categories c ON c.id_category = b.id_category
    JOIN suppliers s ON s.id_supplier = b.id_supplier
    WHERE b.id_book = ?
    LIMIT 1
";

$stmt = mysqli_prepare($mysql, $query);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if(!$product) {
    http_response_code(404);
    exit('Produkt nie istnieje.');
}

/* Pobieranie informacji o autorach */
$authorsQuery = "
    SELECT a.id_author, a.name, a.surname
    FROM authors a
    JOIN book_authors ba ON ba.id_author = a.id_author
    WHERE ba.id_book = ?
";
$stmt = mysqli_prepare($mysql, $authorsQuery);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$authors = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Pobieranie ulubionych produktów zalogowanego użytkownika
$isFav = false;

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmtFav = mysqli_prepare($mysql, "SELECT 1 FROM favorites WHERE id_customer = ? AND id_book = ? LIMIT 1");
    mysqli_stmt_bind_param($stmtFav, "ii", $user_id, $productId);
    mysqli_stmt_execute($stmtFav);
    mysqli_stmt_store_result($stmtFav);
    
    $isFav = mysqli_stmt_num_rows($stmtFav) > 0;
    mysqli_stmt_close($stmtFav);
}

// Zmiana tytułu
$template->setTitle($product['title']);

// Przekazujemy dane do pliku frontendowego
ob_start();
require __DIR__ . '/front/Fproduct.php';
$content = ob_get_clean();
$display->toDisplay($content);
