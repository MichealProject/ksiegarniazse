<?php
session_start();
header('Content-Type: application/json');

// Zabezpieczenie przed niezalogowanym użytkownikiem
if(!isset($_SESSION['user_id'])){
    echo json_encode(['status'=>'error', 'message'=>'Nie zalogowany']);
    exit;
}

// Wprowadzenie nowej ilości do bazy po jej zmianie przez input
$data = json_decode(file_get_contents('php://input'), true);
$id_book = intval($data['id_book'] ?? 0);
$quantity = max(1, min(100, intval($data['quantity'] ?? 1)));

$conn = mysqli_connect("localhost", "root", "", "bookdb");
if(!$conn){
    echo json_encode(['status'=>'error', 'message'=>'Brak połączenia z bazą']);
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE favorites SET quantity=? WHERE id_customer=? AND id_book=?");
mysqli_stmt_bind_param($stmt, "iii", $quantity, $_SESSION['user_id'], $id_book);
mysqli_stmt_execute($stmt);

if(mysqli_stmt_affected_rows($stmt) > 0){
    echo json_encode(['status'=>'ok']);
}else{
    echo json_encode(['status'=>'ok']);
}

exit;