<?php  
$conn = mysqli_connect("localhost", "root", "", "user_master");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
