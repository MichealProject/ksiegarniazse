<?php
$conn = new mysqli('localhost','root','','bookdb');
if($conn->connect_errno)
    die("Błąd połączenia".$conn->connect_error);

$email=$_POST["email"];
$sql="SELECT id_customer FROM customers WHERE email=?;";
$stmt=$conn->prepare($sql);
$stmt->bind_param('s',$email);
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows>0)
    echo "exists";
else
    echo "good";
$stmt->close();
$conn->close();
?>