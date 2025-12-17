<?php
session_start();

// Om användaren redan är inloggad, skicka till admin.php
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

// Felmeddelanden
$error = isset($_GET['error']) ? $_GET['error'] : '';
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Logga in / Skapa konto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Logga in</h1>

    <!-- Felmeddelanden -->
    <?php if ($error == 1): ?>
        <p style="color:red;">Fel användarnamn eller lösenord.</p>
    <?php elseif ($error == 2): ?>
        <p style="color:red;">Du måste logga in först.</p>
    <?php endif; ?>

    <?php if ($message): ?>
        <p style="color:green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Inloggningsformulär -->
    <h2>Logga in</h2>
    <form method="post" action="check_login.php">
        <p>
            <label for="username">Användarnamn:</label><br>
            <input type="text" name="username" id="username" required>
        </p>

        <p>
            <label for="password">Lösenord:</label><br>
            <input type="password" name="password" id="password" required>
        </p>

        <p>
            <input type="checkbox" name="keepLoggedIn"> Håll mig inloggad
        </p>

        <p>
            <button type="submit">Logga in</button>
        </p>
    </form>

    <!-- Skapa nytt konto -->
    <h2>Skapa nytt konto</h2>
    <form method="post" action="check_login.php">
        <p>
            <label for="newUsername">Användarnamn:</label><br>
            <input type="text" name="newUsername" id="newUsername" required>
        </p>

        <p>
            <label for="newPassword">Lösenord:</label><br>
            <input type="password" name="newPassword" id="newPassword" required>
        </p>

        <p>
            <label for="newEmail">E-post:</label><br>
            <input type="email" name="newEmail" id="newEmail" required>
        </p>

        <p>
            <label for="kontotyp">Kontotyp:</label><br>
            <select name="kontotyp" id="kontotyp" required>
                <option value="arbetstagare">Arbetstagare</option>
                <option value="arbetsgivare">Arbetsgivare</option>
            </select>
        </p>

        <p>
            <button type="submit">Skapa konto</button>
        </p>
    </form>
</body>
</html>
