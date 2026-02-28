<?php

$conn = new mysqli('localhost','root','','bookdb');
if($conn->connect_errno)
    die("Błąd połączenia".$conn->connect_error);

if($_SERVER['REQUEST_METHOD']=='POST'){
    $email=$_POST["email"];
    $pass=password_hash(trim($_POST["pass"],''),PASSWORD_DEFAULT);
    $fName=$_POST["fName"];
    $lName=$_POST["lName"];
    $sql="INSERT INTO customers(email,password_hash,name,surname) VALUES (?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('ssss',$email,$pass,$fName,$lName);
    $stmt->execute();
    header("location:flogin.php");
    $stmt->close();
    $conn->close();
}
?>