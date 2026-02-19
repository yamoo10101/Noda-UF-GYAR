<?php
session_start();

$loggedIn = !empty($_SESSION['user_id']);
$kontotyp = $_SESSION['kontotyp'] ?? null;

if ($loggedIn) {
  // Skicka inte till index.html. Antingen stanna, eller skicka till rätt dashboard.
  if ($kontotyp === "arbetsgivare") {
    header("Location: foretag-start.php");
    exit;
  } elseif ($kontotyp === "arbetstagare") {
    header("Location: privat-start.php");
    exit;
  }
  header("Location: index.php");
  exit;
}

$error = $_GET['error'] ?? '';
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Logga in – Noda UF</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/index.css" />
</head>
<body>

<header class="toppmeny">
  <div class="behallare toppmeny__inre">
    <a class="varumarke" href="index.php">Noda UF</a>

    <nav class="toppmeny__lankar" id="toppmenyLankar">
      <a class="toppmeny__lank" href="index.php">Hem</a>
      <a class="toppmeny__lank" href="om-oss.html">Om oss</a>
      <a class="toppmeny__lank" href="faq.html">FAQ</a>
      <a class="toppmeny__lank" href="kontakt.html">Kontakt</a>
      <a class="toppmeny__lank" href="login.php">Logga in</a>
    </nav>

    <button class="menyknapp" id="menyknapp" aria-label="Öppna meny" aria-expanded="false" aria-controls="toppmenyLankar">
      <svg width="26" height="26" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M4 6h16M4 12h16M4 18h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
  </div>
</header>

<main class="behallare sida">
  <h1>Logga in</h1>

  <?php if ($error == '1'): ?>
    <p class="formular__info" style="color:#b00020;font-weight:600;">Fel användarnamn eller lösenord.</p>
  <?php elseif ($error == '2'): ?>
    <p class="formular__info" style="color:#b00020;font-weight:600;">Du måste logga in först.</p>
  <?php endif; ?>

  <?php if (!empty($message)): ?>
    <p class="formular__info" style="color:green;font-weight:600;">
      <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
    </p>
  <?php endif; ?>

  <form class="formular" action="actions/check_login.php" method="post">
    <div class="formular__rad">
      <label for="anvandarnamn">Användarnamn</label>
      <input id="anvandarnamn" name="username" type="text" autocomplete="username" required>
    </div>

    <div class="formular__rad">
      <label for="losenord">Lösenord</label>
      <input id="losenord" name="password" type="password" autocomplete="current-password" required>
    </div>

    <div class="formular__rad" style="gap:10px;">
      <label style="font-weight:600;">
        <input type="checkbox" name="keepLoggedIn" value="1">
        Håll mig inloggad
      </label>
      <button class="knapp--primar" type="submit">Logga in</button>
    </div>

    <p class="formular__info">
      Saknar du konto? <a href="register.php">Skapa konto här</a> eller kontakta oss:
      <a href="mailto:nodauf@gmail.com?subject=Konto%20Noda%20UF">nodauf@gmail.com</a>
    </p>
  </form>
</main>

<footer class="sidfot">
  <div class="behallare sidfot__inre">
    <span>© Noda UF</span>
    <a href="mailto:nodauf@gmail.com?subject=Kontakt%20Noda%20UF">nodauf@gmail.com</a>
  </div>
</footer>

<script src="assets/js/index.js"></script>
</body>
</html>
