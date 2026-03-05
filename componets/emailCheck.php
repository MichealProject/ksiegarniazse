<?php
$email=$_POST["email"];
$sql="SELECT id_customer FROM customers WHERE email=?;";
$stmt=$mysql->prepare($sql);
$stmt->bind_param('s',$email);
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows>0)
    echo "exists";
else
    echo "good";
$stmt->close();
?>
