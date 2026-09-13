<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">

    <h2>Admin Dashboard</h2>

    <div class="menu">
        <a href="companies.php">Manage Companies</a>
        <a href="products.php">Manage Products</a>
        <a class="logout" href="logout.php">Logout</a>
    </div>

</div>

</body>
</html>