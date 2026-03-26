<?php

$conn = $mysql;

if (!function_exists('cartFetchFavIds')) {
    function cartFetchFavIds(mysqli $conn, int $userId): array
    {
        $favIds = [];

        $stmt = mysqli_prepare($conn, "SELECT id_book FROM favorites WHERE id_customer = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $favIds[] = (int)$row['id_book'];
        }

        mysqli_stmt_close($stmt);

        return $favIds;
    }
}

if (!function_exists('cartFetchItems')) {
    function cartFetchItems(mysqli $conn, ?int $userId): array
    {
        $cartItems = [];
        $total = 0.0;

        if ($userId !== null) {
            $sql = "SELECT c.book_id, c.ilosc, b.title, b.price
                    FROM cart c
                    JOIN books b ON b.id_book = c.book_id
                    WHERE c.customer_id = ?";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                $quantity = max(1, min(100, (int)$row['ilosc']));
                $price = (float)$row['price'];
                $sum = $price * $quantity;

                $total += $sum;
                $cartItems[] = [
                    'id' => (int)$row['book_id'],
                    'title' => $row['title'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'sum' => $sum,
                ];
            }

            mysqli_stmt_close($stmt);

            return [$cartItems, $total];
        }

        $cookieCart = json_decode($_COOKIE['cart'] ?? '[]', true);

        if (!is_array($cookieCart) || count($cookieCart) === 0) {
            return [$cartItems, $total];
        }

        $ids = [];

        foreach ($cookieCart as $item) {
            if (!isset($item['productId'], $item['quantity'])) {
                continue;
            }

            $id = (int)$item['productId'];
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        if (empty($ids)) {
            return [$cartItems, $total];
        }

        $ids = array_values(array_unique($ids));
        $idsString = implode(',', $ids);

        $result = mysqli_query($conn, "SELECT id_book, title, price FROM books WHERE id_book IN ($idsString)");
        $products = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $products[(int)$row['id_book']] = $row;
        }

        foreach ($cookieCart as $item) {
            $id = (int)($item['productId'] ?? 0);
            $quantity = max(1, min(100, (int)($item['quantity'] ?? 1)));

            if (!isset($products[$id])) {
                continue;
            }

            $price = (float)$products[$id]['price'];
            $sum = $price * $quantity;

            $total += $sum;
            $cartItems[] = [
                'id' => $id,
                'title' => $products[$id]['title'],
                'price' => $price,
                'quantity' => $quantity,
                'sum' => $sum,
            ];
        }

        return [$cartItems, $total];
    }
}

if (!function_exists('cartFetchCheckoutDefaults')) {
    function cartFetchCheckoutDefaults(mysqli $conn, int $userId): array
    {
        $defaults = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'street' => '',
            'postal_code' => '',
            'city' => '',
            'country' => 'Polska',
            'delivery_method' => 'courier',
        ];

        $stmtUser = mysqli_prepare($conn, "SELECT name, surname, email FROM customers WHERE id_customer = ? LIMIT 1");
        mysqli_stmt_bind_param($stmtUser, "i", $userId);
        mysqli_stmt_execute($stmtUser);

        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtUser));
        mysqli_stmt_close($stmtUser);

        if ($user) {
            $defaults['first_name'] = $user['name'] ?? '';
            $defaults['last_name'] = $user['surname'] ?? '';
            $defaults['email'] = $user['email'] ?? '';
        }

        $stmtAddress = mysqli_prepare(
            $conn,
            "SELECT street, postal_code, city, country
             FROM addresses
             WHERE id_customer = ?
             ORDER BY id_address DESC
             LIMIT 1"
        );
        mysqli_stmt_bind_param($stmtAddress, "i", $userId);
        mysqli_stmt_execute($stmtAddress);

        $address = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtAddress));
        mysqli_stmt_close($stmtAddress);

        if ($address) {
            $defaults['street'] = $address['street'] ?? '';
            $defaults['postal_code'] = $address['postal_code'] ?? '';
            $defaults['city'] = $address['city'] ?? '';
            $defaults['country'] = $address['country'] ?: 'Polska';
        }

        return $defaults;
    }
}

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? (int)$_SESSION['user_id'] : null;

$favIds = [];
$cartItems = [];
$total = 0.0;
$orderError = '';
$orderSuccess = $_SESSION['order_success'] ?? '';
$showDeliveryForm = (filter_input(INPUT_GET, 'delivery') === '1');
$currentStep = $showDeliveryForm ? 'delivery' : 'cart';

unset($_SESSION['order_success']);

$checkoutData = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'street' => '',
    'postal_code' => '',
    'city' => '',
    'country' => 'Polska',
    'delivery_method' => 'courier',
];

mysqli_query(
    $conn,
    "CREATE TABLE IF NOT EXISTS order_delivery_details (
        id_order_delivery INT NOT NULL AUTO_INCREMENT,
        id_order INT NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        street VARCHAR(255) NOT NULL,
        postal_code VARCHAR(20) NOT NULL,
        city VARCHAR(100) NOT NULL,
        country VARCHAR(100) NOT NULL DEFAULT 'Polska',
        delivery_method VARCHAR(50) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id_order_delivery),
        UNIQUE KEY uniq_order_delivery (id_order),
        CONSTRAINT fk_order_delivery_order FOREIGN KEY (id_order) REFERENCES orders(id_order) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci"
);

if ($isLoggedIn) {
    $favIds = cartFetchFavIds($conn, $userId);
    $checkoutData = cartFetchCheckoutDefaults($conn, $userId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'place_order') {
    $showDeliveryForm = true;

    foreach ($checkoutData as $field => $defaultValue) {
        if (isset($_POST[$field])) {
            $checkoutData[$field] = trim((string)$_POST[$field]);
        }
    }

    $requiredFields = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'street',
        'postal_code',
        'city',
        'country',
        'delivery_method',
    ];

    foreach ($requiredFields as $field) {
        if ($checkoutData[$field] === '') {
            $orderError = 'Uzupełnij wszystkie dane dostawy.';
            break;
        }
    }

    if ($orderError === '' && !filter_var($checkoutData['email'], FILTER_VALIDATE_EMAIL)) {
        $orderError = 'Podaj poprawny adres e-mail.';
    }

    if ($orderError === '' && !preg_match('/^[0-9+ ]{7,20}$/', $checkoutData['phone'])) {
        $orderError = 'Podaj poprawny numer telefonu.';
    }

    if ($orderError === '' && !preg_match('/^[0-9]{2}-[0-9]{3}$/', $checkoutData['postal_code'])) {
        $orderError = 'Kod pocztowy musi mieć format 00-000.';
    }

    if ($orderError === '' && !in_array($checkoutData['delivery_method'], ['courier', 'pickup_point', 'store_pickup'], true)) {
        $orderError = 'Wybierz poprawny sposób dostawy.';
    }

    if ($orderError === '') {
        [$itemsForOrder] = cartFetchItems($conn, $userId);

        if (empty($itemsForOrder)) {
            $orderError = 'Koszyk jest pusty. Dodaj produkty przed złożeniem zamówienia.';
        } else {
            mysqli_begin_transaction($conn);

            try {
                if ($isLoggedIn) {
                    $stmtOrder = mysqli_prepare(
                        $conn,
                        "INSERT INTO orders (id_customer, id_employee, status, total_price)
                         VALUES (?, NULL, 'new', 0)"
                    );
                    mysqli_stmt_bind_param($stmtOrder, "i", $userId);
                    mysqli_stmt_execute($stmtOrder);
                    mysqli_stmt_close($stmtOrder);
                } else {
                    mysqli_query(
                        $conn,
                        "INSERT INTO orders (id_customer, id_employee, status, total_price)
                         VALUES (NULL, NULL, 'new', 0)"
                    );
                }

                $orderId = (int)mysqli_insert_id($conn);

                $stmtItem = mysqli_prepare(
                    $conn,
                    "INSERT INTO order_items (id_order, id_book, quantity, price)
                     VALUES (?, ?, ?, ?)"
                );

                foreach ($itemsForOrder as $item) {
                    $bookId = (int)$item['id'];
                    $quantity = (int)$item['quantity'];
                    $price = (float)$item['price'];

                    mysqli_stmt_bind_param($stmtItem, "iiid", $orderId, $bookId, $quantity, $price);
                    mysqli_stmt_execute($stmtItem);
                }

                mysqli_stmt_close($stmtItem);

                $stmtDelivery = mysqli_prepare(
                    $conn,
                    "INSERT INTO order_delivery_details
                    (id_order, first_name, last_name, email, phone, street, postal_code, city, country, delivery_method)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                mysqli_stmt_bind_param(
                    $stmtDelivery,
                    "isssssssss",
                    $orderId,
                    $checkoutData['first_name'],
                    $checkoutData['last_name'],
                    $checkoutData['email'],
                    $checkoutData['phone'],
                    $checkoutData['street'],
                    $checkoutData['postal_code'],
                    $checkoutData['city'],
                    $checkoutData['country'],
                    $checkoutData['delivery_method']
                );
                mysqli_stmt_execute($stmtDelivery);
                mysqli_stmt_close($stmtDelivery);

                if ($isLoggedIn) {
                    $stmtAddress = mysqli_prepare(
                        $conn,
                        "INSERT INTO addresses (id_customer, street, city, postal_code, country)
                         VALUES (?, ?, ?, ?, ?)"
                    );
                    mysqli_stmt_bind_param(
                        $stmtAddress,
                        "issss",
                        $userId,
                        $checkoutData['street'],
                        $checkoutData['city'],
                        $checkoutData['postal_code'],
                        $checkoutData['country']
                    );
                    mysqli_stmt_execute($stmtAddress);
                    mysqli_stmt_close($stmtAddress);

                    $stmtClearCart = mysqli_prepare($conn, "DELETE FROM cart WHERE customer_id = ?");
                    mysqli_stmt_bind_param($stmtClearCart, "i", $userId);
                    mysqli_stmt_execute($stmtClearCart);
                    mysqli_stmt_close($stmtClearCart);
                } else {
                    setcookie('cart', '', time() - 3600, '/');
                    unset($_COOKIE['cart']);
                }

                mysqli_commit($conn);

                $_SESSION['order_success'] = 'Zamówienie nr ' . $orderId . ' zostało zapisane.';
                header('Location: ?page=cart');
                exit;
            } catch (mysqli_sql_exception $e) {
                mysqli_rollback($conn);

                if (str_contains($e->getMessage(), 'Brak wystarczającej ilości książek na magazynie')) {
                    $orderError = 'Nie udało się złożyć zamówienia, ponieważ jednej z książek brakuje na magazynie.';
                } else {
                    $orderError = 'Nie udało się zapisać zamówienia. Spróbuj ponownie.';
                }
            }
        }
    }
}

[$cartItems, $total] = cartFetchItems($conn, $userId);

$template->setTitle('Koszyk');

ob_start();
include 'front/Fcart.php';
$content = ob_get_clean();

$display->toDisplay($content);
