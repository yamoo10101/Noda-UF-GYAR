<?php
declare(strict_types=1);

session_start();

// Töm session-array
$_SESSION = [];

// DÖDA session-cookie korrekt (path måste matcha den som skapades)
if (ini_get("session.use_cookies")) {
  $p = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool)$p['secure'], (bool)$p['httponly']);
}

// Döda sessionen
session_destroy();

// Om ni har egen cookie
setcookie("logged_in", "", time() - 3600, "/");

// Redirect
header("Location: index.php");
exit;
