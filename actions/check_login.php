<?php
session_start();
require_once "../config/db.php";

// Kontrollera att formuläret skickats korrekt
if (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: ../frontend/login.html?error=1");
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// Hämta användare inkl. kontotyp
$stmt = $conn->prepare(
    "SELECT id, losenord, kontotyp 
     FROM users 
     WHERE anvandarnamn = ?"
);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Finns användaren?
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verifiera lösenord
    if (password_verify($password, $user['losenord'])) {

        // Sätt sessioner
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $username;
        $_SESSION['kontotyp']  = $user['kontotyp'];

        // Kom ihåg mig (valfritt)
        if (!empty($_POST['keepLoggedIn'])) {
            setcookie(
                "logged_in",
                "true",
                time() + (86400 * 30),
                "/",
                "",
                false,
                true
            );
        }

        // Redirect baserat på kontotyp
        if ($user['kontotyp'] === 'arbetsgivare') {
            header("Location: ../frontend/foretag-start.html");
        } else {
            header("Location: ../frontend/privat-start.html");
        }
        exit;
    }
}

// Fel inloggning
header("Location: ../frontend/login.html?error=1");
exit;