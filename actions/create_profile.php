<?php
session_start();
require "../config/db.php";
require "db.php";  // databasanslutning

// säkerställ att användaren är inloggad
if (!isset($_SESSION['user_id'])) {
    die("Du måste vara inloggad");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $stad = trim($_POST['stad'] ?? '');
    $biografi = trim($_POST['biografi'] ?? '');
    $kontotyp = $_POST['kontotyp'] ?? '';

    $errors = [];

    if ($stad === '') {
        $errors[] = "Stad måste fyllas i.";
    }

    if ($biografi === '') {
        $errors[] = "Biografi måste fyllas i.";
    } elseif (strlen($biografi) > 700) {
        $errors[] = "Biografi får inte överstiga 700 tecken.";
    }

    if ($kontotyp !== 'arbetstagare' && $kontotyp !== 'företag') {
        $errors[] = "Kontotyp är ogiltig.";
    }

    if (!empty($errors)) {
        print_r($errors);
        exit;

    // här kommer INSERT/UPDATE i databasen
