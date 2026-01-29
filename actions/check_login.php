<?php
session_start();
require_once "../config/db.php";

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header("Location: ../frontend/login.html?error=1");
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];

// Hämta användaren
$stmt = $conn->prepare("SELECT id, losenord FROM users WHERE anvandarnamn = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['losenord'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id'];

        if (!empty($_POST['keepLoggedIn'])) {
            setcookie("logged_in", "true", time() + 86400 * 30, "/", "", false, true);
        }

        header("Location: ../frontend/foretag-start.html");
        exit;
    }
}

header("Location: ../frontend/login.html?error=1");
exit;
