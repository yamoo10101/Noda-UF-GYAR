<?php
session_start();
require __DIR__ . '/../config/db.php';

// Felmeddelanden
$error = isset($_GET['error']) ? $_GET['error'] : '';
$message = "";
$messageColor = "green"; // Standardfärg för lyckad registrering

// ---------------------------
// Skapa nytt konto (utan inloggning)
// ---------------------------
if (isset($_POST['newNamn'], $_POST['newUsername'], $_POST['newPasswordCreate'], $_POST['newEmail'])) {
    $newNamn = trim($_POST['newNamn']);
    $newUsername = trim($_POST['newUsername']);
    $newPassCreate = trim($_POST['newPasswordCreate']);
    $newEmail = trim($_POST['newEmail']);

    // Kolla om användarnamn finns
    $checkUser = $conn->query("SELECT id FROM users WHERE användarnamn = '$newUsername'");
    // Kolla om email finns
    $checkEmail = $conn->query("SELECT id FROM users WHERE email = '$newEmail'");

    if ($checkUser->num_rows > 0) {
        $message = "Användarnamnet finns redan, var vänlig och välj ett annat";
        $messageColor = "red";
    } elseif ($checkEmail->num_rows > 0) {
        $message = "E-postadressen används redan, testa att logga in";
        $messageColor = "red";
    } else {
        // Kryptera lösenordet
        $hashedPassword = password_hash($newPassCreate, PASSWORD_BCRYPT);

        // Skapa användare
        $insertQuery = "
            INSERT INTO users (namn, användarnamn, losenord, email, kontotyp)
            VALUES ('$newNamn', '$newUsername', '$hashedPassword', '$newEmail', 'arbetstagare')
        ";

        if ($conn->query($insertQuery) === TRUE) {
            $message = "Nytt konto skapades! Du kan nu logga in.";
            $messageColor = "green";
        } else {
            $message = "Fel vid skapande av konto: " . $conn->error;
            $messageColor = "red";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Logga in / Skapa konto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Inloggning</h1>

<!-- Felmeddelanden -->
<?php if ($error == 1): ?>
    <p style="color:red;">Fel användarnamn eller lösenord.</p>
<?php elseif ($error == 2): ?>
    <p style="color:red;">Du måste vara inloggad för att se sidan!</p>
<?php endif; ?>

<?php if ($message): ?>
    <p style="color:<?= $messageColor ?>;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- Inloggningsformulär -->
<h2>Logga in</h2>
<form method="post" action="check_login.php">
    <p>Användarnamn:<br>
        <input type="text" name="användarnamn" required>
    </p>

    <p>Lösenord:<br>
        <input type="password" name="lösenord" required>
    </p>

    <p>
        <input type="checkbox" name="keepLoggedIn"> Håll mig inloggad
    </p>

    <p>
        <input type="submit" value="Logga in">
    </p>
</form>

<!-- Skapa konto -->
<h2>Skapa nytt konto</h2>
<form method="post">
    <p>Namn:<br>
        <input type="text" name="newNamn" required>
    </p>

    <p>Användarnamn:<br>
        <input type="text" name="newUsername" required>
    </p>

    <p>Lösenord:<br>
        <input type="password" name="newPasswordCreate" required>
    </p>

    <p>E-post:<br>
        <input type="email" name="newEmail" required>
    </p>

    <p>
        <input type="submit" value="Skapa konto">
    </p>
</form>

</body>
</html>
