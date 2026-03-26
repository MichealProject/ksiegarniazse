<?php
// Pobieranie danych użytkownika z sesji
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=userLogin');
    exit;
}

$userId = (int)$_SESSION['user_id'];

// Pobieramy dane użytkownika
$stmtUser = mysqli_prepare($mysql, "SELECT name FROM customers WHERE id_customer = ?");
mysqli_stmt_bind_param($stmtUser, "i", $userId);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);
mysqli_stmt_close($stmtUser);

// Pobieramy ulubione produkty użytkownika
$query = "
    SELECT 
        f.id_book,
        f.quantity,
        b.title,
        b.price,
        b.stock
    FROM favorites f
    JOIN books b ON b.id_book = f.id_book
    WHERE f.id_customer = ?
    ORDER BY f.added_at DESC
";

$stmt = mysqli_prepare($mysql, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$favorites = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Ilość produktów w ulubionych
$favCount = array_sum(array_column($favorites, 'quantity'));

// Przekazujemy dane do frontendu
$template->setTitle("Ulubione");
ob_start();
include 'front/Ffavorites.php';
$content = ob_get_clean();
$display->toDisplay($content);