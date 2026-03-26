<?php

// Przekierowanie jeśli użytkownik niezalogowany
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=userLogin');
    exit;
}

$user_id = $_SESSION['user_id'];

// Pobieranie historii zamówień
$orders = [];

$sql = "SELECT * FROM orders WHERE id_customer = ? ORDER BY order_date DESC";
$stmt = mysqli_prepare($mysql, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($order = mysqli_fetch_assoc($result)) {

        // Pobieranie produktów dla poszczególnych zamówień
        $items = [];

        $sql_items = "
        SELECT b.title, oi.quantity, oi.price
        FROM order_items oi
        JOIN books b ON oi.id_book = b.id_book
        WHERE oi.id_order = ? 
    ";

    $stmt_items = mysqli_prepare($mysql, $sql_items);
    mysqli_stmt_bind_param($stmt_items, "i", $order['id_order']);
    mysqli_stmt_execute($stmt_items);
    $items_result = mysqli_stmt_get_result($stmt_items);

    while ($item = mysqli_fetch_assoc($items_result)) {
        $items[] = $item;
    }

    $order['items'] = $items;
    $orders[] = $order;

}

// Przekazujemy dane do frontendu
$template->setTitle("Historia zamówień");

ob_start();
include 'front/Forders.php';
$content = ob_get_clean();

$display->toDisplay($content);