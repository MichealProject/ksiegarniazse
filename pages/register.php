<?php
if($_SERVER['REQUEST_METHOD']=='POST'){
    $email=$_POST["email"];
    $pass=password_hash(trim($_POST["pass"],''),PASSWORD_DEFAULT);
    $fName=$_POST["fName"];
    $lName=$_POST["lName"];
    $sql="INSERT INTO customers(email,password_hash,name,surname) VALUES (?,?,?,?)";
    $stmt=$mysql->prepare($sql);
    $stmt->bind_param('ssss',$email,$pass,$fName,$lName);
    $stmt->execute();
    $stmt->close();
    header("location:?page=userLogin");
}

ob_start();
include 'front/Fregister.php';
$content = ob_get_clean();
$display->toDisplay($content);
?>
