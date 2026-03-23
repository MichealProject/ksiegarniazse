<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "bookdb");

// Format json
header('Content-Type: application/json');

// Odczytujemy dane z zapytania
$data = json_decode(file_get_contents("php://input"), true);

$product_id = intval($data['product_id'] ?? 0);

if ($product_id <= 0) {
    echo json_encode(["status" => "error"]);
    exit;
}

$quantity = 1;

// Dodawanie do koszyka dla zalogowanego użytkownika
if (isset($_SESSION['user_id'])) {

    $stmt = mysqli_prepare($conn,
        "INSERT INTO cart (customer_id, book_id, ilosc)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE ilosc = LEAST(ilosc + VALUES(ilosc), 100)"
    );

    mysqli_stmt_bind_param($stmt, "iii", $_SESSION['user_id'], $product_id, $quantity);
    mysqli_stmt_execute($stmt);

    echo json_encode(["status" => "ok"]);
    exit;
}

// Dodawanie do koszyka dla niezalogowanego użytkownika
$cart = json_decode($_COOKIE['cart'] ?? '[]', true);
if (!is_array($cart)) $cart = [];

$found = false;

// Sprawdzenie produktów w koszyku i ich aktualizacja
foreach ($cart as &$item) {
    if ($item['productId'] == $product_id) {
        $item['quantity'] = min($item['quantity'] + 1, 100);
        $found = true;
    }
}

// Dodanie nowego produktu do koszyka
if (!$found) {
    $cart[] = ["productId" => $product_id, "quantity" => 1];
}

setcookie("cart", json_encode($cart), time()+86400*30, "/");

echo json_encode(["status" => "ok"]);
exit;
