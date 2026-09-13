<?php
$host="localhost";
$user="root";
$password="";
$databasae="users_db";

$conn= new mysqli($host, $users, $password, $database);

if($conn->connect_error){

die("Connection failed: ", $conn->connect_error);
}



?>