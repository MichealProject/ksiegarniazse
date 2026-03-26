<?php
session_start();
$conn = mysqli_connect("localhost","root","","bookdb");
header('Content-Type: application/json');

if (!$conn) {
    echo json_encode(["status"=>"error","message"=>"Brak połączenia z bazą"]);
    exit;
}

// Zabezpieczenie przed niezalogowanym użytkownikiem
if(!isset($_SESSION['user_id'])){
    echo json_encode(["status"=>"redirect","redirect"=>"index.php?page=userLogin"]);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? null;

if(!$action){
    echo json_encode(["status"=>"error","message"=>"Brak akcji"]);
    exit;
}

switch($action){

    case 'toggle':
        $product_id = intval($data['product_id'] ?? 0);

        // Sprawdzenie czy produkt już jest w ulubionych
        $stmt = mysqli_prepare($conn,"SELECT * FROM favorites WHERE id_customer=? AND id_book=?");
        mysqli_stmt_bind_param($stmt,"ii",$userId,$product_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if(mysqli_stmt_num_rows($stmt) > 0){
            // Usuwanie produktu
            $stmtDel = mysqli_prepare($conn,"DELETE FROM favorites WHERE id_customer=? AND id_book=?");
            mysqli_stmt_bind_param($stmtDel,"ii",$userId,$product_id);
            mysqli_stmt_execute($stmtDel);
        } else {
            // Dodawanie produktu
            $stmtAdd = mysqli_prepare($conn,"INSERT INTO favorites (id_customer,id_book,quantity) VALUES (?,?,1)");
            mysqli_stmt_bind_param($stmtAdd,"ii",$userId,$product_id);
            mysqli_stmt_execute($stmtAdd);
        }

        echo json_encode(["status"=>"ok"]);
        break;

    // Aktualizacja ilości produktów na liście ulubionych
    case 'update_quantity':
        $product_id = intval($data['product_id'] ?? 0);
        $quantity = max(1, min(100, intval($data['quantity'] ?? 1)));

        $stmt = mysqli_prepare($conn,"UPDATE favorites SET quantity=? WHERE id_customer=? AND id_book=?");
        mysqli_stmt_bind_param($stmt,"iii",$quantity,$userId,$product_id);
        mysqli_stmt_execute($stmt);

        echo json_encode(["status"=>"ok"]);
        break;
    
    // Przenoszenie listy ulubionych do koszyka
    case 'move_to_cart':
        $stmt = mysqli_prepare($conn,"SELECT id_book, quantity FROM favorites WHERE id_customer=?");
        mysqli_stmt_bind_param($stmt,"i",$userId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $favorites = mysqli_fetch_all($res,MYSQLI_ASSOC);

        foreach($favorites as $fav){
            $stmtCart = mysqli_prepare($conn,
                "INSERT INTO cart (customer_id, book_id, ilosc)
                 VALUES (?,?,?)
                 ON DUPLICATE KEY UPDATE ilosc = ilosc + VALUES(ilosc)");
            mysqli_stmt_bind_param($stmtCart,"iii",$userId,$fav['id_book'],$fav['quantity']);
            mysqli_stmt_execute($stmtCart);
        }

        echo json_encode(["status"=>"ok"]);
        break;

    // Czyszczenie listy
    case 'clear_all':
        $stmt = mysqli_prepare($conn,"DELETE FROM favorites WHERE id_customer=?");
        mysqli_stmt_bind_param($stmt,"i",$userId);
        mysqli_stmt_execute($stmt);

        echo json_encode(["status"=>"ok"]);
        break;

    default:
        echo json_encode(["status"=>"error","message"=>"Nieznana akcja"]);
        break;
}

exit;