<?php
$conn = new mysqli('localhost','root','','bookdb');
$email=$_POST["email"];
$stmt=$conn->prepare("SELECT id_customer FROM customers WHERE email=?;");
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
