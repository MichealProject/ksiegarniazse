<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "bookdb");

// Format json
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    // Przygotowanie zapytania
    $stmt = mysqli_prepare($conn,
        "SELECT SUM(ilosc) as total FROM cart WHERE customer_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Zwraca odpowiedź z ilością produktów w koszyku
    echo json_encode([
        "status" => "ok",
        "count" => (int)$row['total']
    ]);
    exit;
}

echo json_encode([
    "status" => "ok",
    "count" => 0
]);
exit;