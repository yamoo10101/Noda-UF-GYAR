<?php
$server = $_SERVER['SERVER_NAME'];


$server = 'localhost';
$servername = "localhost";
$username = "root";
$password = "Handboll30!";
$dbname   = "gyar_db2";


/*
if ($server === 'localhost') {
    $servername = "localhost";
    $username = "root";
    $password = "Handboll30!";
    $dbname   = "gyar_db2";
} else {
    $servername = "server1.serverdrift.com"; 
    $username   = "als071220al_inloggdb"; 
    $password   = "nDAWl9gJxSDY"; 
    $dbname     = "als071220al_inloggdb";   
}
*/


$conn = new mysqli($servername, $username, $password, $dbname);

// Kontrollera anslutningen
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
