<?php
session_start();
require_once "db.php";

if (!isset($_POST['användarnamn']) || !isset($_POST['lösenord'])) {
    header("Location: index.php?error=1");
    exit;
}

$username = $_POST['användarnamn'];
$password = $_POST['lösenord'];

// Hämta användaren
$stmt = $conn->prepare("SELECT id, losenord FROM users WHERE användarnamn = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Kolla lösenord med password_verify
    if (password_verify($password, $user['losenord'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id'];

        if (!empty($_POST['keepLoggedIn'])) {
            setcookie("logged_in", "true", time() + 86400 * 30, "/");
        }

        header("Location: admin.php");
        exit;
    }
}

// Fel inloggningsuppgifter
header("Location: index.php?error=1");
exit;
?>
