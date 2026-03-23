<?php
if(LOGIN_ACCESS===true){
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $email=trim($_POST['email']);
        $pass=trim($_POST['pass']);
        $err='';

        // Zapytanie mysql
        $stmt = $mysql->prepare("SELECT id_customer,email,password_hash,banned FROM customers WHERE email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Sprawdzenie rezultatów
        if(empty($res) || !password_verify($pass,$res['password_hash']))
            $err="Błędny login lub hasło";
        elseif($res['banned']==true)
            $err="Konto zablokowane";
        else{
            $_SESSION['user_id']=$res['id_customer'];
            // Zostanie zalogowanym
            if($_POST['logout']=="stay")
                $_SESSION['stay']=true;

            // Przeniesienie koszyka z cookie do tabeli
            if (!empty($_COOKIE['cart'])) {

                $cart = json_decode($_COOKIE['cart'], true);

                if (is_array($cart)) {

                    $stmt = mysqli_prepare($mysql,
                        "INSERT INTO cart (customer_id, book_id, ilosc)
                        VALUES (?, ?, ?)
                        ON DUPLICATE KEY UPDATE ilosc = LEAST(ilosc + VALUES(ilosc), 100)"
                    );

                    foreach ($cart as $item) {

                        $product_id = (int)$item['productId'];
                        $quantity = max(1, min(100, (int)$item['quantity']));

                        mysqli_stmt_bind_param($stmt, "iii",
                            $_SESSION['user_id'],
                            $product_id,
                            $quantity
                        );

                        mysqli_stmt_execute($stmt);
                    }
                }

                // Usunięcie cookie po przeniesieniu koszyka do tabeli
                setcookie("cart", "", time() - 3600, "/");
            }


            // Ponowny hash hasła jeżeli jest potrzebny
            if (password_needs_rehash($res['password_hash'], PASSWORD_DEFAULT)) {
    			$new_hash = password_hash($pass, PASSWORD_DEFAULT);
    			$stmt=$mysql->query("UPDATE customers SET password_hash=".$new_hash." WHERE email=".$email);
    		}
            header("location:?page=home");
        }
    }
}
ob_start();
include 'front/FuserLogin.php';
$content = ob_get_clean();
$display->toDisplay($content);
?>
