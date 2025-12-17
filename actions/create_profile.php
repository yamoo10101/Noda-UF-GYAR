<?php
session_start();
require "db.php;"  // databas anslutning

//säkerställ att användaren är inloggad
if (!isset($_SESSION['user_id'])){
    die("Du måste vara inloggad")
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST"){

    // hämta data från formuläret
    $stad = trim($_POST['stad']);
    $biografi = trim($_POST['biografi']);

    //felmeddelande
    $errors = [];


    //validera stad
    if (empty($stad) ){
        $errors[] = "Stad måste fyllas i";
    }

    // Validera biografi
    if(empty($biografi)){
        $errors[] = "Biografi måste fyllas i.";
    } elseif (strlen($biografi) > 700){
        $errors[]= "Biografi får inte överstiga 700 tecken"
    }

    // validera kontotyp
    if($kontotyp !== 'arbetstagare' && $kontotyp !== 'företag'){
        $errors[]= "Kontotyp är ogiltlig"
    }

    //validera 
    }