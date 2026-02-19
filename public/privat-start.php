<?php
session_start();
require "../config/db.php";

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: login.php");
    exit;
}

$successMsg = "";
$profilSparad = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stad = $_POST['stad'] ?? '';
    $biografi = $_POST['biografi'] ?? '';
    $taggarStr = $_POST['taggar'] ?? ''; // "Kundkontakt,Noggrann,Servering"

        // ===== PROFILBILD UPLOAD =====
    $nyProfilbildPath = null;

    if (!empty($_FILES['profilbild']['name'])) {

        $tmp  = $_FILES['profilbild']['tmp_name'];
        $err  = $_FILES['profilbild']['error'];
        $size = $_FILES['profilbild']['size'];

        if ($err === 0 && $size > 0 && $size <= 2 * 1024 * 1024) { // max 2MB

            $allowedExt = ['jpg','jpeg','png','webp'];
            $ext = strtolower(pathinfo($_FILES['profilbild']['name'], PATHINFO_EXTENSION));

            $imgInfo = @getimagesize($tmp);

            if (in_array($ext, $allowedExt, true) && $imgInfo !== false) {

                $uploadDir = __DIR__ . "/uploads/profilbilder/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $newName = "profil_" . $userId . "_" . uniqid() . "." . $ext;
                $targetPath = $uploadDir . $newName;

                if (move_uploaded_file($tmp, $targetPath)) {
                    $nyProfilbildPath = "uploads/profilbilder/" . $newName;
                }
            }
        }
    }

    // 1. Uppdatera users
   if ($nyProfilbildPath) {
    $stmt = $conn->prepare("UPDATE users SET stad = ?, biografi = ?, profilbild = ? WHERE id = ?");
    $stmt->bind_param("sssi", $stad, $biografi, $nyProfilbildPath, $userId);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("UPDATE users SET stad = ?, biografi = ? WHERE id = ?");
    $stmt->bind_param("ssi", $stad, $biografi, $userId);
    $stmt->execute();
}

    // 2. Rensa gamla taggar
    $stmt = $conn->prepare("DELETE FROM user_tags WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // 3. Lägg in nya taggar
    if ($taggarStr !== '') {
        $taggar = array_filter(array_map('trim', explode(",", $taggarStr)));
        $taggar = array_slice($taggar, 0, 8); // max 8

        $stmtFind = $conn->prepare("SELECT id FROM tags WHERE namn = ? LIMIT 1");
        $stmtInsert = $conn->prepare("INSERT INTO user_tags (user_id, tag_id) VALUES (?, ?)");

        foreach ($taggar as $namn) {
            $stmtFind->bind_param("s", $namn);
            $stmtFind->execute();
            $res = $stmtFind->get_result();

            if ($row = $res->fetch_assoc()) {
                $tagId = (int)$row['id'];
                $stmtInsert->bind_param("ii", $userId, $tagId);
                $stmtInsert->execute();
            }
        }
    }

    $successMsg = "Profil sparad!";
    $profilSparad = true;
}

// Hämta användardata
$stmt = $conn->prepare("SELECT namn, stad, biografi, profilbild FROM users WHERE id = ?");

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


// ✅ Hämta valda taggar (för att göra dem gröna på sidan)
$valdaTaggar = [];
$stmt = $conn->prepare("
  SELECT t.namn
  FROM user_tags ut
  JOIN tags t ON t.id = ut.tag_id
  WHERE ut.user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $valdaTaggar[] = $row['namn'];
}


// Hämta alla taggar från DB (unika namn)
$allaTaggar = [];
$resTags = $conn->query("SELECT DISTINCT namn FROM tags ORDER BY namn");
if ($resTags) {
  while ($row = $resTags->fetch_assoc()) {
    $allaTaggar[] = $row['namn'];
  }
}


?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Privat – Noda UF</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/assets/css/index.css" />
</head>
<body>

<header class="toppmeny">
  <div class="behallare toppmeny__inre">
  <a class="varumarke" href="index.php">Noda UF</a>
    <nav class="toppmeny__lankar" id="toppmenyLankar" aria-label="Inloggad meny">
      <a class="toppmeny__lank" href="#" data-flik="swipe">Swipe</a>
      <a class="toppmeny__lank" href="#" data-flik="matchningar">Matchningar</a>
      <a class="toppmeny__lank" href="#" data-flik="profil">Profil</a>
      <a class="toppmeny__lank" href="logout.php">Logga ut</a>
    </nav>

    <button class="menyknapp" id="menyknapp" aria-label="Öppna meny" aria-expanded="false" aria-controls="toppmenyLankar">
      <svg width="26" height="26" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M4 6h16M4 12h16M4 18h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
  </div>
</header>

<main class="behallare sida">

  <!-- FLIK: SWIPE -->
  <section class="flik flik--aktiv" id="flik-swipe" aria-label="Swipe">
    <h1>Swipe</h1>
    <p class="text-brod">Gilla eller hoppa över annonser.</p>

    <div class="kort stor-kort" id="annonsKort">
      <div class="kort__bild" id="annonsBild" aria-label="Annonsbild"></div>

      <div class="kort__innehall">
        <div class="kort__rubrikrad">
          <h2 class="kort__titel" id="annonsTitel">Butiksmedarbetare (Deltid)</h2>
          <span class="kort__foretag" id="annonsForetag">ICA Nära</span>
        </div>

        <div class="kort__rad">
          <span><strong>Stad:</strong> <span id="annonsStad">Alingsås</span></span>
          <span><strong>Anställningsform:</strong> <span id="annonsForm">Deltid</span></span>
        </div>

        <div class="kort__rad">
          <span><strong>Sökt tjänst:</strong> <span id="annonsTjanst">Butiksbiträde</span></span>
          <span><strong>Adress:</strong> <span id="annonsAdress">Centrumgatan 1</span></span>
        </div>

        <p class="kort__text" id="annonsText">
          Vi söker en driven person som gillar tempo och service. Erfarenhet är meriterande men inget krav.
        </p>

        <div class="taggar" id="annonsTaggar"></div>
        <input type="hidden" id="annonsTaggarInput" value="">

        <div class="kort__knappar">
          <button class="knapp--sekundar" id="knappNej" type="button" aria-label="Hoppa över">👎 Hoppa</button>
          <button class="knapp--primar" id="knappJa" type="button" aria-label="Gilla">👍 Gilla</button>
        </div>

        <p class="hint" id="swipeHint">
          (Prototyp) <code>swipes</code>.
        </p>
      </div>
    </div>
  </section>

  <!-- FLIK: MATCHNINGAR -->
  <section class="flik" id="flik-matchningar" aria-label="Matchningar">
    <h1>Matchningar</h1>
    <p class="text-brod">Här visas företag du matchat med. Klicka för att se chatten.</p>

    <div class="lista">
      <a class="lista__item" href="chatt.html">
        <div class="lista__bild"></div>
        <div class="lista__info">
          <div class="lista__titel">Bageri Solros</div>
          <div class="lista__sub">Matchad • 2026-01-20</div>
        </div>
      </a>

      <a class="lista__item" href="chatt.html">
        <div class="lista__bild"></div>
        <div class="lista__info">
          <div class="lista__titel">Café Hörnet</div>
          <div class="lista__sub">Matchad • 2026-01-18</div>
        </div>
      </a>

      <a class="lista__item" href="chatt.html">
        <div class="lista__bild"></div>
        <div class="lista__info">
          <div class="lista__titel">Gym Arena</div>
          <div class="lista__sub">Matchad • 2026-01-15</div>
        </div>
      </a>
    </div>

    <p class="hint">
      (Prototyp) Sen kopplas detta mot <code>matches</code> och <code>messages</code>.
    </p>
  </section>

  <!-- FLIK: PROFIL -->
  <section class="flik" id="flik-profil" aria-label="Profil">
    <h1>Min profil</h1>
    <p class="text-brod">Här kan du redigera bio, stad och taggar (i prototypen).</p>

    <div class="profilkort">
      <div class="profilkort__topp">
        <div class="profilkort__bild">
        <?php if (!empty($user['profilbild'])): ?>
        <img
          src="<?= htmlspecialchars($user['profilbild']) ?>"
          alt="Profilbild"
          style="width:100%; height:100%; object-fit:cover; border-radius:12px;"
    >
  <?php endif; ?>
</div>

        <div>
          <div class="profilkort__namn">
            <?= htmlspecialchars($user['namn'] ?? '') ?>
          </div>
          <div class="profilkort__rad">
            Stad: <span class="muted"><?= htmlspecialchars($user['stad'] ?? '') ?></span>
          </div>
        </div>
      </div>

      <form class="formular formular--kompakt" action="privat-start.php" method="post" enctype="multipart/form-data">
        <div class="formular__rad">
          <label for="stad">Stad</label>
          <input id="stad" name="stad" type="text" value="<?= htmlspecialchars($user['stad'] ?? '') ?>" />
        </div>

        <div class="formular__rad">
          <label for="bio">Biografi (max ~700 tecken)</label>
          <textarea id="bio" name="biografi" rows="6"><?= htmlspecialchars($user['biografi'] ?? '') ?></textarea>
        </div>

        <div class="formular__rad">
          <label for="profilbild">Profilbild</label>
          <input id="profilbild" name="profilbild" type="file" accept="image/jpeg,image/png,image/webp">
          <div class="muted" style="margin-top:6px;">Max 2MB. JPG/PNG/WebP.</div>
        </div>


        <div class="formular__rad">
          <label>Taggar (max 8)</label>

          <div class="taggar" id="profilTaggar">
            <?php foreach ($allaTaggar as $t):
              $vald = in_array($t, $valdaTaggar, true);
            ?>
              <span class="tagg <?= $vald ? 'tagg--vald' : '' ?>"><?= htmlspecialchars($t) ?></span>
            <?php endforeach; ?>
          </div>

          <input type="hidden" name="taggar" id="taggarInput" value="<?= htmlspecialchars(implode(',', $valdaTaggar)) ?>">

          <div class="muted" style="margin-top:6px;">
            (Prototyp).
          </div>
        </div>

        <button class="knapp--primar" type="submit">Spara ändringar</button>

        <?php if (!empty($successMsg)): ?>
          <p class="hint" style="color: green; margin-top: 8px;">
            <?= htmlspecialchars($successMsg) ?>
          </p>
        <?php endif; ?>
      </form>
    </div>
  </section>

</main>

<footer class="sidfot">
  <div class="behallare sidfot__inre">
    <span>© Noda UF</span>
    <a href="mailto:nodauf@gmail.com?subject=Kontakt%20Noda%20UF">nodauf@gmail.com</a>
  </div>
</footer>



<script src="../assets/js/index.js"></script>
<script src="../assets/js/privat-start.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  <?php if ($profilSparad): ?>
    if (typeof visaFlik === "function") visaFlik("profil");
  <?php endif; ?>
});
</script>

</body>
</html>
