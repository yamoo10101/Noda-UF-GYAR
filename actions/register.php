<?php
session_start();
require_once "../config/db.php"; 
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($username) || empty($password) || empty($email)) {
        $message = "Fyll i alla fält.";
    } else {
        // Kontrollera om användarnamnet redan finns
        $stmt = $conn->prepare("SELECT id FROM users WHERE anvandarnamn = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Användarnamnet är redan upptaget.";
        } else {
            // Hasha lösenordet
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Fyll kolumnen 'namn' med samma som anvandarnamn
            $namn = $username;

            // Lägg till användaren i users-tabellen
            $stmt = $conn->prepare("INSERT INTO users (anvandarnamn, losenord, namn, email) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $hashedPassword, $namn, $email);

            if ($stmt->execute()) {
                $message = "Konto skapat! Du kan nu logga in.";
            } else {
                $message = "Ett fel uppstod, försök igen.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Registrera konto</title>
    <link rel="stylesheet" href="../assets/css/index.css">
</head>
<body>
    <div class="container">
        <h1>Skapa konto</h1>

        <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>

        <form method="post" action="register.php">
            <p>
                Användarnamn<br>
                <input type="text" name="username" required>
            </p>
            <p>
                Lösenord<br>
                <input type="password" name="password" required>
            </p>
            <p>
                E-post<br>
                <input type="email" name="email" required>
            </p>
            <p>
                <input type="submit" value="Skapa konto">
            </p>
        </form>

        <p><a href="../frontend/login.html">Tillbaka till inloggning</a></p>
    </div>
</body>
</html>
