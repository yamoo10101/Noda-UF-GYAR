<?php
session_start();

// Felmeddelanden
$error = isset($_GET["error"]) ? (int)$_GET["error"] : 0;

$loggedIn = !empty($_SESSION["user_id"]);
$kontotyp = $_SESSION["kontotyp"] ?? null;

// Välj en “Fortsätt”-länk om inloggad (men vi redirectar INTE)
$continueUrl = "login.php";
if ($loggedIn) {
  if ($kontotyp === "arbetsgivare") $continueUrl = "foretag-start.php";
  elseif ($kontotyp === "arbetstagare") $continueUrl = "privat-start.php";
  else $continueUrl = "privat-start.php"; // fallback
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Noda UF</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<header class="toppmeny">
  <div class="behallare toppmeny__inre">
    <a class="varumarke" href="index.php" aria-label="Noda UF, startsida">Noda UF</a>

    <nav class="toppmeny__lankar" id="toppmenyLankar" aria-label="Huvudmeny">
      <a class="toppmeny__lank" href="index.php">Hem</a>

      <!-- Byt till .html om ni inte konverterat dem än -->
      <a class="toppmeny__lank" href="om-oss.php">Om oss</a>
      <a class="toppmeny__lank" href="faq.php">FAQ</a>
      <a class="toppmeny__lank" href="kontakt.php">Kontakt</a>

      <?php if ($loggedIn): ?>
        <a class="toppmeny__lank" href="<?= htmlspecialchars($continueUrl, ENT_QUOTES, "UTF-8") ?>">Fortsätt</a>
        <a class="toppmeny__lank" href="logout.php">Logga ut</a>
      <?php else: ?>
        <a class="toppmeny__lank" href="login.php">Logga in</a>
      <?php endif; ?>
    </nav>

    <button class="menyknapp" id="menyknapp" aria-label="Öppna meny" aria-expanded="false" aria-controls="toppmenyLankar">
      <svg width="26" height="26" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M4 6h16M4 12h16M4 18h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
  </div>
</header>

<main>

  <?php if ($error === 1): ?>
    <div class="behallare" style="margin-top:16px;">
      <p style="color:#b00020;">Fel användarnamn eller lösenord.</p>
    </div>
  <?php elseif ($error === 2): ?>
    <div class="behallare" style="margin-top:16px;">
      <p style="color:#b00020;">Du måste vara inloggad för att se sidan.</p>
    </div>
  <?php endif; ?>

  <section class="hero">
    <div class="behallare">
      <h1>Välkommen till Noda UF</h1>
      <p>
        Vi gör det enklare för unga att ta sig in på arbetsmarknaden genom
        personlig matchning mellan arbetssökande och arbetsgivare.
      </p>

      <?php if ($loggedIn): ?>
        <div style="margin-top:14px;">
          <a class="knapp--primar" href="<?= htmlspecialchars($continueUrl, ENT_QUOTES, "UTF-8") ?>">Fortsätt</a>
        </div>
      <?php else: ?>
        <div style="margin-top:14px;">
          <a class="knapp--primar" href="login.php">Kom igång</a>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="val behallare" aria-label="Välj roll">
    <a class="valkort" href="login.php" aria-label="Jag söker talanger">
      <div class="valkort__ikon" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path d="M10 6h4a2 2 0 0 1 2 2v1H8V8a2 2 0 0 1 2-2Zm-4 5h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2Z"
            fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
      </div>
      <div>
        <h2>Jag söker talanger</h2>
        <p>Skapa din företagsprofil och hitta rätt person.</p>
      </div>
    </a>

    <a class="valkort" href="login.php" aria-label="Jag söker jobb">
      <div class="valkort__ikon" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-7 9a7 7 0 0 1 14 0"
            fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
      </div>
      <div>
        <h2>Jag söker jobb</h2>
        <p>Skapa din personliga profil och matcha med företag.</p>
      </div>
    </a>
  </section>

  <section class="sa-funkar-det behallare" id="sa-funkar-det">
    <h2>Så fungerar det</h2>

    <div class="steg-rutnat">
      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-7 9a7 7 0 0 1 14 0"
              fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">1</span>
            <h3>Skapa din profil</h3>
          </div>
          <p>Berätta om dig själv med bilder, taggar och biografi.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M10 7l5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4 12h11" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">2</span>
            <h3>Swipea och matcha</h3>
          </div>
          <p>Gilla eller hoppa över. Vid ömsesidigt intresse uppstår match.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"
              fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">3</span>
            <h3>Starta en konversation</h3>
          </div>
          <p>När ni matchar kan ni chatta och planera nästa steg.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M20 6 9 17l-5-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">4</span>
            <h3>Hitta en framtid</h3>
          </div>
          <p>En matchning som passar både arbetsgivare och arbetstagare.</p>
        </div>
      </div>
    </div>
  </section>

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
<?php
session_start();

// Felmeddelanden
$error = isset($_GET["error"]) ? (int)$_GET["error"] : 0;

$loggedIn = !empty($_SESSION["user_id"]);
$kontotyp = $_SESSION["kontotyp"] ?? null;

// Välj en “Fortsätt”-länk om inloggad (men vi redirectar INTE)
$continueUrl = "login.php";
if ($loggedIn) {
  if ($kontotyp === "arbetsgivare") $continueUrl = "foretag-start.php";
  elseif ($kontotyp === "arbetstagare") $continueUrl = "privat-start.php";
  else $continueUrl = "privat-start.php"; // fallback
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Noda UF</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<header class="toppmeny">
  <div class="behallare toppmeny__inre">
    <a class="varumarke" href="index.php" aria-label="Noda UF, startsida">Noda UF</a>

    <nav class="toppmeny__lankar" id="toppmenyLankar" aria-label="Huvudmeny">
      <a class="toppmeny__lank" href="index.php">Hem</a>

      <!-- Byt till .html om ni inte konverterat dem än -->
      <a class="toppmeny__lank" href="om-oss.php">Om oss</a>
      <a class="toppmeny__lank" href="faq.php">FAQ</a>
      <a class="toppmeny__lank" href="kontakt.php">Kontakt</a>

      <?php if ($loggedIn): ?>
        <a class="toppmeny__lank" href="<?= htmlspecialchars($continueUrl, ENT_QUOTES, "UTF-8") ?>">Fortsätt</a>
        <a class="toppmeny__lank" href="logout.php">Logga ut</a>
      <?php else: ?>
        <a class="toppmeny__lank" href="login.php">Logga in</a>
      <?php endif; ?>
    </nav>

    <button class="menyknapp" id="menyknapp" aria-label="Öppna meny" aria-expanded="false" aria-controls="toppmenyLankar">
      <svg width="26" height="26" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M4 6h16M4 12h16M4 18h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
  </div>
</header>

<main>

  <?php if ($error === 1): ?>
    <div class="behallare" style="margin-top:16px;">
      <p style="color:#b00020;">Fel användarnamn eller lösenord.</p>
    </div>
  <?php elseif ($error === 2): ?>
    <div class="behallare" style="margin-top:16px;">
      <p style="color:#b00020;">Du måste vara inloggad för att se sidan.</p>
    </div>
  <?php endif; ?>

  <section class="hero">
    <div class="behallare">
      <h1>Välkommen till Noda UF</h1>
      <p>
        Vi gör det enklare för unga att ta sig in på arbetsmarknaden genom
        personlig matchning mellan arbetssökande och arbetsgivare.
      </p>

      <?php if ($loggedIn): ?>
        <div style="margin-top:14px;">
          <a class="knapp--primar" href="<?= htmlspecialchars($continueUrl, ENT_QUOTES, "UTF-8") ?>">Fortsätt</a>
        </div>
      <?php else: ?>
        <div style="margin-top:14px;">
          <a class="knapp--primar" href="login.php">Kom igång</a>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="val behallare" aria-label="Välj roll">
    <a class="valkort" href="login.php" aria-label="Jag söker talanger">
      <div class="valkort__ikon" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path d="M10 6h4a2 2 0 0 1 2 2v1H8V8a2 2 0 0 1 2-2Zm-4 5h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2Z"
            fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
      </div>
      <div>
        <h2>Jag söker talanger</h2>
        <p>Skapa din företagsprofil och hitta rätt person.</p>
      </div>
    </a>

    <a class="valkort" href="login.php" aria-label="Jag söker jobb">
      <div class="valkort__ikon" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-7 9a7 7 0 0 1 14 0"
            fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
      </div>
      <div>
        <h2>Jag söker jobb</h2>
        <p>Skapa din personliga profil och matcha med företag.</p>
      </div>
    </a>
  </section>

  <section class="sa-funkar-det behallare" id="sa-funkar-det">
    <h2>Så fungerar det</h2>

    <div class="steg-rutnat">
      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-7 9a7 7 0 0 1 14 0"
              fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">1</span>
            <h3>Skapa din profil</h3>
          </div>
          <p>Berätta om dig själv med bilder, taggar och biografi.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M10 7l5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4 12h11" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">2</span>
            <h3>Swipea och matcha</h3>
          </div>
          <p>Gilla eller hoppa över. Vid ömsesidigt intresse uppstår match.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"
              fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">3</span>
            <h3>Starta en konversation</h3>
          </div>
          <p>När ni matchar kan ni chatta och planera nästa steg.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M20 6 9 17l-5-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">4</span>
            <h3>Hitta en framtid</h3>
          </div>
          <p>En matchning som passar både arbetsgivare och arbetstagare.</p>
        </div>
      </div>
    </div>
  </section>

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
<?php
session_start();

// Felmeddelanden
$error = isset($_GET["error"]) ? (int)$_GET["error"] : 0;

$loggedIn = !empty($_SESSION["user_id"]);
$kontotyp = $_SESSION["kontotyp"] ?? null;

// Välj en “Fortsätt”-länk om inloggad (men vi redirectar INTE)
$continueUrl = "login.php";
if ($loggedIn) {
  if ($kontotyp === "arbetsgivare") $continueUrl = "foretag-start.php";
  elseif ($kontotyp === "arbetstagare") $continueUrl = "privat-start.php";
  else $continueUrl = "privat-start.php"; // fallback
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Noda UF</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<header class="toppmeny">
  <div class="behallare toppmeny__inre">
    <a class="varumarke" href="index.php" aria-label="Noda UF, startsida">Noda UF</a>

    <nav class="toppmeny__lankar" id="toppmenyLankar" aria-label="Huvudmeny">
      <a class="toppmeny__lank" href="index.php">Hem</a>

      <!-- Byt till .html om ni inte konverterat dem än -->
      <a class="toppmeny__lank" href="om-oss.php">Om oss</a>
      <a class="toppmeny__lank" href="faq.php">FAQ</a>
      <a class="toppmeny__lank" href="kontakt.php">Kontakt</a>

      <?php if ($loggedIn): ?>
        <a class="toppmeny__lank" href="<?= htmlspecialchars($continueUrl, ENT_QUOTES, "UTF-8") ?>">Fortsätt</a>
        <a class="toppmeny__lank" href="logout.php">Logga ut</a>
      <?php else: ?>
        <a class="toppmeny__lank" href="login.php">Logga in</a>
      <?php endif; ?>
    </nav>

    <button class="menyknapp" id="menyknapp" aria-label="Öppna meny" aria-expanded="false" aria-controls="toppmenyLankar">
      <svg width="26" height="26" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M4 6h16M4 12h16M4 18h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
  </div>
</header>

<main>

  <?php if ($error === 1): ?>
    <div class="behallare" style="margin-top:16px;">
      <p style="color:#b00020;">Fel användarnamn eller lösenord.</p>
    </div>
  <?php elseif ($error === 2): ?>
    <div class="behallare" style="margin-top:16px;">
      <p style="color:#b00020;">Du måste vara inloggad för att se sidan.</p>
    </div>
  <?php endif; ?>

  <section class="hero">
    <div class="behallare">
      <h1>Välkommen till Noda UF</h1>
      <p>
        Vi gör det enklare för unga att ta sig in på arbetsmarknaden genom
        personlig matchning mellan arbetssökande och arbetsgivare.
      </p>

      <?php if ($loggedIn): ?>
        <div style="margin-top:14px;">
          <a class="knapp--primar" href="<?= htmlspecialchars($continueUrl, ENT_QUOTES, "UTF-8") ?>">Fortsätt</a>
        </div>
      <?php else: ?>
        <div style="margin-top:14px;">
          <a class="knapp--primar" href="login.php">Kom igång</a>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="val behallare" aria-label="Välj roll">
    <a class="valkort" href="login.php" aria-label="Jag söker talanger">
      <div class="valkort__ikon" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path d="M10 6h4a2 2 0 0 1 2 2v1H8V8a2 2 0 0 1 2-2Zm-4 5h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2Z"
            fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
      </div>
      <div>
        <h2>Jag söker talanger</h2>
        <p>Skapa din företagsprofil och hitta rätt person.</p>
      </div>
    </a>

    <a class="valkort" href="login.php" aria-label="Jag söker jobb">
      <div class="valkort__ikon" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-7 9a7 7 0 0 1 14 0"
            fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
      </div>
      <div>
        <h2>Jag söker jobb</h2>
        <p>Skapa din personliga profil och matcha med företag.</p>
      </div>
    </a>
  </section>

  <section class="sa-funkar-det behallare" id="sa-funkar-det">
    <h2>Så fungerar det</h2>

    <div class="steg-rutnat">
      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-7 9a7 7 0 0 1 14 0"
              fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">1</span>
            <h3>Skapa din profil</h3>
          </div>
          <p>Berätta om dig själv med bilder, taggar och biografi.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M10 7l5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4 12h11" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">2</span>
            <h3>Swipea och matcha</h3>
          </div>
          <p>Gilla eller hoppa över. Vid ömsesidigt intresse uppstår match.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"
              fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">3</span>
            <h3>Starta en konversation</h3>
          </div>
          <p>När ni matchar kan ni chatta och planera nästa steg.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M20 6 9 17l-5-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">4</span>
            <h3>Hitta en framtid</h3>
          </div>
          <p>En matchning som passar både arbetsgivare och arbetstagare.</p>
        </div>
      </div>
    </div>
  </section>

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
<?php
session_start();

// Felmeddelanden
$error = isset($_GET["error"]) ? (int)$_GET["error"] : 0;

$loggedIn = !empty($_SESSION["user_id"]);
$kontotyp = $_SESSION["kontotyp"] ?? null;

// Välj en “Fortsätt”-länk om inloggad (men vi redirectar INTE)
$continueUrl = "login.php";
if ($loggedIn) {
  if ($kontotyp === "arbetsgivare") $continueUrl = "foretag-start.php";
  elseif ($kontotyp === "arbetstagare") $continueUrl = "privat-start.php";
  else $continueUrl = "privat-start.php"; // fallback
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Noda UF</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<header class="toppmeny">
  <div class="behallare toppmeny__inre">
    <a class="varumarke" href="index.php" aria-label="Noda UF, startsida">Noda UF</a>

    <nav class="toppmeny__lankar" id="toppmenyLankar" aria-label="Huvudmeny">
      <a class="toppmeny__lank" href="index.php">Hem</a>

      <!-- Byt till .html om ni inte konverterat dem än -->
      <a class="toppmeny__lank" href="om-oss.html">Om oss</a>
      <a class="toppmeny__lank" href="faq.html">FAQ</a>
      <a class="toppmeny__lank" href="kontakt.html">Kontakt</a>

      <?php if ($loggedIn): ?>
        <a class="toppmeny__lank" href="<?= htmlspecialchars($continueUrl, ENT_QUOTES, "UTF-8") ?>">Fortsätt</a>
        <a class="toppmeny__lank" href="logout.php">Logga ut</a>
      <?php else: ?>
        <a class="toppmeny__lank" href="login.php">Logga in</a>
      <?php endif; ?>
    </nav>

    <button class="menyknapp" id="menyknapp" aria-label="Öppna meny" aria-expanded="false" aria-controls="toppmenyLankar">
      <svg width="26" height="26" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M4 6h16M4 12h16M4 18h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
  </div>
</header>

<main>

  <?php if ($error === 1): ?>
    <div class="behallare" style="margin-top:16px;">
      <p style="color:#b00020;">Fel användarnamn eller lösenord.</p>
    </div>
  <?php elseif ($error === 2): ?>
    <div class="behallare" style="margin-top:16px;">
      <p style="color:#b00020;">Du måste vara inloggad för att se sidan.</p>
    </div>
  <?php endif; ?>

  <section class="hero">
    <div class="behallare">
      <h1>Välkommen till Noda UF</h1>
      <p>
        Vi gör det enklare för unga att ta sig in på arbetsmarknaden genom
        personlig matchning mellan arbetssökande och arbetsgivare.
      </p>

      <?php if ($loggedIn): ?>
        <div style="margin-top:14px;">
          <a class="knapp--primar" href="<?= htmlspecialchars($continueUrl, ENT_QUOTES, "UTF-8") ?>">Fortsätt</a>
        </div>
      <?php else: ?>
        <div style="margin-top:14px;">
          <a class="knapp--primar" href="login.php">Kom igång</a>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="val behallare" aria-label="Välj roll">
    <a class="valkort" href="login.php" aria-label="Jag söker talanger">
      <div class="valkort__ikon" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path d="M10 6h4a2 2 0 0 1 2 2v1H8V8a2 2 0 0 1 2-2Zm-4 5h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2Z"
            fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
      </div>
      <div>
        <h2>Jag söker talanger</h2>
        <p>Skapa din företagsprofil och hitta rätt person.</p>
      </div>
    </a>

    <a class="valkort" href="login.php" aria-label="Jag söker jobb">
      <div class="valkort__ikon" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-7 9a7 7 0 0 1 14 0"
            fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
      </div>
      <div>
        <h2>Jag söker jobb</h2>
        <p>Skapa din personliga profil och matcha med företag.</p>
      </div>
    </a>
  </section>

  <section class="sa-funkar-det behallare" id="sa-funkar-det">
    <h2>Så fungerar det</h2>

    <div class="steg-rutnat">
      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-7 9a7 7 0 0 1 14 0"
              fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">1</span>
            <h3>Skapa din profil</h3>
          </div>
          <p>Berätta om dig själv med bilder, taggar och biografi.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M10 7l5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4 12h11" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">2</span>
            <h3>Swipea och matcha</h3>
          </div>
          <p>Gilla eller hoppa över. Vid ömsesidigt intresse uppstår match.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"
              fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">3</span>
            <h3>Starta en konversation</h3>
          </div>
          <p>När ni matchar kan ni chatta och planera nästa steg.</p>
        </div>
      </div>

      <div class="steg">
        <div class="steg__ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M20 6 9 17l-5-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div>
          <div class="steg__rubrikrad">
            <span class="steg__nummer">4</span>
            <h3>Hitta en framtid</h3>
          </div>
          <p>En matchning som passar både arbetsgivare och arbetstagare.</p>
        </div>
      </div>
    </div>
  </section>

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
