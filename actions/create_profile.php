<?php
session_start();

require_once "../config/db.php"; // VÄLJ EN DB-FIL, inte två
// require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    die("Du måste vara inloggad");
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $stad     = trim($_POST['stad'] ?? '');
    $biografi = trim($_POST['biografi'] ?? '');
    $kontotyp = $_POST['kontotyp'] ?? '';

    $errors = [];

    if ($stad === '') {
        $errors[] = "Stad måste fyllas i.";
    }

    if ($biografi === '') {
        $errors[] = "Biografi måste fyllas i.";
    } elseif (mb_strlen($biografi, 'UTF-8') > 700) {
        $errors[] = "Biografi får inte överstiga 700 tecken.";
    }

    if ($kontotyp !== 'arbetstagare' && $kontotyp !== 'företag') {
        $errors[] = "Kontotyp är ogiltig.";
    }

    if (!empty($errors)) {
        print_r($errors);
        exit;
    }

    // här kommer INSERT/UPDATE i databasen
}
