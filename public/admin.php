<?php
session_start();
require_once "db.php";

// Kontrollera om användaren är inloggad
if ((!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) &&
    (!isset($_COOKIE['logged_in']) || $_COOKIE['logged_in'] !== "true")) {
    header("Location: index.php?error=2");
    exit;
}

$message = "";
$userId = $_SESSION['user_id']; // Definiera användarens ID

// -----------------------------------------
// Hantera POST – Byt lösenord / Byt användarnamn / Ta bort konto
// -----------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Byta lösenord (klartext)
    if (isset($_POST['userIdChange'], $_POST['newPassword'])) {
        $newPass = $_POST['newPassword'];

        // Uppdatera lösenord
        $stmt = $conn->prepare("UPDATE användare SET lösenord = ? WHERE id = ?");
        $stmt->bind_param("si", $newPass, $userId);
        $stmt->execute();
        $stmt->close();

        // Logga i lösenords_historik
        $log = $conn->prepare("INSERT INTO lösenords_historik (användar_id, lösenord, ändrad) VALUES (?, ?, NOW())");
        $log->bind_param("is", $userId, $newPass);
        $log->execute();
        $log->close();

        $message = "Lösenordet har uppdaterats";
    }

    // Byt användarnamn
    if (isset($_POST['newUsername'])) {
        $newUsername = $_POST['newUsername'];

        // Kontrollera om användarnamnet redan finns
        $check = $conn->prepare("SELECT id FROM användare WHERE användarnamn = ? AND id != ?");
        $check->bind_param("si", $newUsername, $userId);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "Användarnamnet finns redan!";
        } else {
            $stmt = $conn->prepare("UPDATE användare SET användarnamn = ? WHERE id = ?");
            $stmt->bind_param("si", $newUsername, $userId);
            $stmt->execute();
            $stmt->close();

            $_SESSION['username'] = $newUsername;
            $message = "Användarnamnet har uppdaterats!";
        }
        $check->close();
    }

    // Ta bort konto
    if (isset($_POST['userIdDelete'])) {

        // Radera all historik för användaren först
        $delLog = $conn->prepare("DELETE FROM lösenords_historik WHERE användar_id = ?");
        $delLog->bind_param("i", $userId);
        $delLog->execute();
        $delLog->close();

        // Radera användaren
        $stmt = $conn->prepare("DELETE FROM användare WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();

        // Logga ut användaren
        session_destroy();
        setcookie("logged_in", "", time() - 3600, "/");

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<p>Du är inloggad som <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
<p><a href="logout.php">Logga ut</a></p>

<?php if ($message): ?>
    <p style="color:green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- Byt lösenord -->
<h2>Byt lösenord</h2>
<form method="post">
    <input type="hidden" name="userIdChange" value="<?= $userId ?>">
    Nytt lösenord: <input type="password" name="newPassword" required>
    <br>
    <input type="submit" value="Byt lösenord">
</form>

<!-- Byt användarnamn -->
<h2>Byt användarnamn</h2>
<form method="post">
    Nytt användarnamn: <input type="text" name="newUsername" required>
    <br>
    <input type="submit" value="Byt användarnamn">
</form>

<!-- Ta bort konto -->
<h2>Ta bort mitt konto</h2>
<form method="post">
    <input type="hidden" name="userIdDelete" value="<?= $userId ?>">
    <p>OBS: Detta tar bort ditt konto permanent!</p>
    <input type="submit" value="Ta bort mitt konto">
</form>

</body>
</html>
