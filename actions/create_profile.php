<?php
session_start();
require "db.php";  // databasanslutning

// säkerställ att användaren är inloggad
if (!isset($_SESSION['user_id'])) {
    die("Du måste vara inloggad");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // hämta data från formuläret
    $stad = trim($_POST['stad'] ?? '');
    $biografi = trim($_POST['biografi'] ?? '');
    $kontotyp = trim($_POST['kontotyp'] ?? '');

    // felmeddelanden
    $errors = [];

    // validera stad
    if ($stad === '') {
        $errors[] = "Stad måste fyllas i.";
    }

    // validera biografi
    if ($biografi === '') {
        $errors[] = "Biografi måste fyllas i.";
    } elseif (strlen($biografi) > 700) {
        $errors[] = "Biografi får inte överstiga 700 tecken.";
    }

    // validera kontotyp
    if ($kontotyp !== 'arbetstagare' && $kontotyp !== 'företag') {
        $errors[] = "Kontotyp är ogiltig.";
    }

    // här kan du fortsätta: om inga errors -> spara i DB
    // if (empty($errors)) { ... }
}
