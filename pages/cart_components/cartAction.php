<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "bookdb");

// Format json
header('Content-Type: application/json');

// Odczytujemy dane z zapytania
$data = json_decode(file_get_contents("php://input"), true);

$action = $data['action'] ?? null;
$product_id = intval($data['product_id'] ?? 0);
$quantity = intval($data['quantity'] ?? 1);

if (!$action || !$product_id) {
    echo json_encode(["status"=>"error"]);
    exit;
}

$isUser = isset($_SESSION['user_id']);

// Pobiera koszyk z cookie
function getCart() {
    $cart = json_decode($_COOKIE['cart'] ?? '[]', true);
    return is_array($cart) ? $cart : [];
}

// Zapis koszyka do cookie
function saveCart($cart) {
    setcookie("cart", json_encode(array_values($cart)), time()+86400*30, "/");
}

switch ($action) {

    // Aktualizacja ilości produktu w koszyku
    case 'update_quantity':

        $quantity = max(1, min(100, $quantity));

        // Dla zalogowanego użytkownika
        if ($isUser) {
            $stmt = mysqli_prepare($conn,
                "UPDATE cart_items SET quantity=? WHERE user_id=? AND product_id=?");
            mysqli_stmt_bind_param($stmt, "iii", $quantity, $_SESSION['user_id'], $product_id);
            mysqli_stmt_execute($stmt);
        } 
        // Dla niezalogowanego użytkownika
        else {
            $cart = getCart();

            foreach ($cart as &$item) {
                if ($item['productId'] == $product_id) {
                    $item['quantity'] = $quantity;
                }
            }

            saveCart($cart);
        }

        break;

    // Usuwanie produktu z koszyka
    case 'remove':

        // Dla zalogowanego użytkownika
        if ($isUser) {
            
        }
        // Dla niezalogowanego użytkownika 
        else {
            $cart = array_filter(getCart(),
                fn($i) => $i['productId'] != $product_id);
            saveCart($cart);
        }

        break;
}

echo json_encode(["status"=>"ok"]);
exit;