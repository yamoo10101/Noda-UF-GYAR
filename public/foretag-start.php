<?php
session_start();
require __DIR__ . '/../config/db.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
  header("Location: login.php");
  exit;
}

$successMsg = "";
$profilSparad = false;
$openFlik = "";

/* =========================
   HÄMTA ANVÄNDARE
========================= */
$stmt = $conn->prepare("SELECT namn, stad, biografi, email, telefon FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* =========================
   HÄMTA TAGGAR
========================= */
$allaTaggar = [];
$resTags = $conn->query("SELECT MIN(id) AS id, namn FROM tags GROUP BY namn ORDER BY namn");
if ($resTags) {
  while ($row = $resTags->fetch_assoc()) {
    $allaTaggar[] = $row;
  }
}

/* =========================
   SKAPA ANNONS
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['skapa_annons'])) {

  $foretagsnamn = trim($_POST['foretagsnamn'] ?? '');
  $adress = trim($_POST['adress'] ?? '');
  $stad = trim($_POST['stad'] ?? '');
  $sokt_tjanst = trim($_POST['sokt_tjanst'] ?? '');
  $anstallningsform = trim($_POST['anstallningsform'] ?? '');
  $titel = trim($_POST['titel'] ?? '');
  $beskrivning = trim($_POST['beskrivning'] ?? '');

  $stmt = $conn->prepare("
    INSERT INTO annons (user_id, titel, beskrivning, foretagsnamn, adress, stad, sokt_tjanst, anstallningsform)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
  ");
  $stmt->bind_param("isssssss", $userId, $titel, $beskrivning, $foretagsnamn, $adress, $stad, $sokt_tjanst, $anstallningsform);
  $stmt->execute();
  $annonsId = $stmt->insert_id;
  $stmt->close();

  $successMsg = "Annons sparad!";
  $profilSparad = true;
  $openFlik = "annonser";
}

/* =========================
   HÄMTA ANNONSER
========================= */
$minaAnnonser = [];
$stmt = $conn->prepare("
  SELECT id, titel, stad, skapad_datum
  FROM annons
  WHERE user_id = ?
  ORDER BY skapad_datum DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
  $minaAnnonser[] = $r;
}
$stmt->close();

/* =========================
   HÄMTA MATCHNINGAR
========================= */
$minaMatchningar = [];

$sql = "
  SELECT
    m.id AS match_id,
    m.datum,
    CASE WHEN m.user1_id = ? THEN u2.namn ELSE u1.namn END AS other_name,
    CASE WHEN m.user1_id = ? THEN u2.stad ELSE u1.stad END AS other_city
  FROM matches m
  JOIN users u1 ON u1.id = m.user1_id
  JOIN users u2 ON u2.id = m.user2_id
  WHERE m.user1_id = ? OR m.user2_id = ?
  ORDER BY m.id DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $userId, $userId, $userId, $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
  $minaMatchningar[] = $r;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="sv">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Företag – Noda UF</title>
<link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<header class="toppmeny">
<div class="behallare toppmeny__inre">
<a class="varumarke" href="index.php">Noda UF</a>
<a href="logout.php">Logga ut</a>
</div>
</header>

<main class="behallare sida">

<h1>Mina annonser</h1>

<div class="lista">
<?php if (empty($minaAnnonser)): ?>
<div class="muted">Du har inga annonser än.</div>
<?php else: ?>
<?php foreach ($minaAnnonser as $a): ?>
<div class="lista__item">
<div class="lista__titel">
<?= htmlspecialchars($a['titel'] ?? '', ENT_QUOTES, 'UTF-8') ?>
</div>
<div class="lista__sub">
<?= htmlspecialchars($a['stad'] ?? '', ENT_QUOTES, 'UTF-8') ?>
•
<?= htmlspecialchars($a['skapad_datum'] ?? '', ENT_QUOTES, 'UTF-8') ?>
</div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>

<h1>Matchningar</h1>

<div class="lista">
<?php if (empty($minaMatchningar)): ?>
<div class="muted">Inga matchningar än.</div>
<?php else: ?>
<?php foreach ($minaMatchningar as $m): ?>
<a class="lista__item" href="chatt.php?match_id=<?= (int)$m['match_id'] ?>">
<div class="lista__titel">
<?= htmlspecialchars($m['other_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
</div>
<div class="lista__sub">
Matchad • <?= htmlspecialchars($m['datum'] ?? '', ENT_QUOTES, 'UTF-8') ?>
<?php if (!empty($m['other_city'])): ?>
• <?= htmlspecialchars($m['other_city'], ENT_QUOTES, 'UTF-8') ?>
<?php endif; ?>
</div>
</a>
<?php endforeach; ?>
<?php endif; ?>
</div>

</main>
</body>
</html>
