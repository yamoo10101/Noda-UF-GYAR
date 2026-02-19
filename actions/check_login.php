<?php
session_start();
require __DIR__ . "/../../config/db.php";

// Basic validering
if (empty($_POST["username"]) || empty($_POST["password"])) {
  header("Location: ../login.php?error=1");
  exit;
}

$username = trim($_POST["username"]);
$password = (string)$_POST["password"];

// Hämta användare
$stmt = $conn->prepare("
  SELECT id, losenord, kontotyp, anvandarnamn
  FROM users
  WHERE anvandarnamn = ?
  LIMIT 1
");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {
  $user = $res->fetch_assoc();

  if (password_verify($password, $user["losenord"])) {
    // Session
    $_SESSION["logged_in"] = true;
    $_SESSION["user_id"]   = (int)$user["id"];
    $_SESSION["username"]  = $user["anvandarnamn"] ?? $username;
    $_SESSION["kontotyp"]  = $user["kontotyp"];

    // “Keep me logged in” (OBS: detta är bara kosmetiskt om ni inte bygger riktig remember-token)
    if (!empty($_POST["keepLoggedIn"])) {
      setcookie("logged_in", "true", [
        "expires" => time() + (86400 * 30),
        "path" => "/",
        "secure" => false, // sätt true på https i produktion
        "httponly" => true,
        "samesite" => "Lax"
      ]);
    }

    // Redirect efter kontotyp
    if ($user["kontotyp"] === "arbetsgivare") {
      header("Location: ../foretag-start.php");
      exit;
    }

    header("Location: ../privat-start.php");
    exit;
  }
}

// Fail
header("Location: ../login.php?error=1");
exit;
