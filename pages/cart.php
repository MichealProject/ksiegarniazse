<?php

$conn = mysqli_connect("localhost", "root", "", "bookdb");

if(!$conn) {
    die("Błąd połączenia z bazą");
}

$cartItems = [];
$total = 0;

// Pobieranie ulubionych produktów zalogowanego użytkownika
$favIds = [];
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $resultFav = mysqli_query($conn, "SELECT id_book FROM favorites WHERE id_customer = $user_id");
    while($row = mysqli_fetch_assoc($resultFav)){
        $favIds[] = (int)$row['id_book'];
    }
}

// Pobieranie produktów dla zalogowanych użytkowników
if(isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    // Pobieranie danych produktów z koszyka użytkownika
    $sql = "SELECT c.book_id, c.ilosc, b.title, b.price
            FROM cart c
            JOIN books b ON b.id_book = c.book_id
            WHERE c.customer_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    //Tworzenie koszyka
    while ($row = mysqli_fetch_assoc($result)) {

        $quantity = max(1, min(100, (int)$row['ilosc']));
        $price = (float)$row['price'];

        $sum = $price * $quantity;
        $total += $sum;

        $cartItems[] = [
            "id" => (int)$row['book_id'],
            "title" => $row['title'],
            "price" => $price,
            "quantity" => $quantity,
            "sum" => $sum
        ];
    }
}

// Pobieranie produktów dla niezalogowanych użytkowników
else {

    $cookieCart = json_decode($_COOKIE['cart'] ?? '[]', true);

    if(is_array($cookieCart) && count($cookieCart) > 0) {

        // Wyciągnięcie wyszstkich id produktów z cookie
        $ids = [];

        foreach ($cookieCart as $item) {
            if (!isset($item['productId'], $item['quantity'])) continue;

            $id = (int)$item['productId'];
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        if(!empty($ids)) {

            // Pobranie danych wszystkich produktów
            $idsString = implode(',', $ids);

            $result = mysqli_query($conn,
                "SELECT id_book, title, price FROM books WHERE id_book IN ($idsString)"
            );

            $products = [];

            while($row = mysqli_fetch_assoc($result)) {
                $products[$row['id_book']] = $row;
            }

            // Tworzenie koszyka łącząc ilość z cookie i dane z bazy
            foreach($cookieCart as $item) {

                $id = (int)$item['productId'];
                $quantity = max(1, min(100, (int)$item['quantity']));

                if (!isset($products[$id])) continue;

                $price = (float)$products[$id]['price'];

                $sum = $price * $quantity;
                $total += $sum;

                $cartItems[] = [
                    "id" => $id,
                    "title" => $products[$id]['title'],
                    "price" => $price,
                    "quantity" => $quantity,
                    "sum" => $sum
                ];
            }
        }
    }
}

// Przekazujemy dane do pliku frontendowego
$template->setTitle("Koszyk");

ob_start();
include 'front/Fcart.php';
$content = ob_get_clean();

$display->toDisplay($content);
