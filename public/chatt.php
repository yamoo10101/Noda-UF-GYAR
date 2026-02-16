<?php
session_start();
require __DIR__ . '/../config/db.php';

/**
 * PROTOTYP: om ni inte har riktig login ännu
 * sätt en test-user. Ta bort när ni har inloggning.
 */
if (!isset($_SESSION["user_id"])) {
  $_SESSION["user_id"] = 6; // byt vid behov
}

$userId  = (int)$_SESSION["user_id"];
$matchId = isset($_GET["match_id"]) ? (int)$_GET["match_id"] : 0;

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }
function typeLabel($t) {
  return $t === "arbetsgivare" ? "Företag" : "Privatperson";
}

if ($matchId <= 0) {
  http_response_code(400);
  exit("Saknar match_id i URL. Ex: chatt.php?match_id=1");
}

// Hämta match + båda användarna
$sql = "
  SELECT m.id, m.user1_id, m.user2_id, m.datum,
         u1.namn AS user1_name, u1.kontotyp AS user1_type, u1.stad AS user1_city,
         u2.namn AS user2_name, u2.kontotyp AS user2_type, u2.stad AS user2_city
  FROM matches m
  JOIN users u1 ON u1.id = m.user1_id
  JOIN users u2 ON u2.id = m.user2_id
  WHERE m.id = ?
  LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $matchId);
$stmt->execute();
$match = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$match) {
  http_response_code(404);
  exit("Match hittades inte.");
}

if ((int)$match["user1_id"] !== $userId && (int)$match["user2_id"] !== $userId) {
  http_response_code(403);
  exit("Du har inte tillgång till denna chatt.");
}

// För UI-korten (företag + privat)
$users = [
  [
    "id" => (int)$match["user1_id"],
    "name" => $match["user1_name"],
    "type" => $match["user1_type"],
    "city" => $match["user1_city"]
  ],
  [
    "id" => (int)$match["user2_id"],
    "name" => $match["user2_name"],
    "type" => $match["user2_type"],
    "city" => $match["user2_city"]
  ],
];

// sortera så arbetsgivare visas först (om finns)
usort($users, fn($a,$b) => ($a["type"]==="arbetsgivare" ? -1:1) <=> ($b["type"]==="arbetsgivare" ? -1:1));

// Hämta historik
$msgSql = "
  SELECT msg.id, msg.sender_id, msg.text, msg.skickat, u.namn AS sender_name
  FROM messages msg
  JOIN users u ON u.id = msg.sender_id
  WHERE msg.match_id = ?
  ORDER BY msg.id ASC
  LIMIT 200
";
$msgStmt = $conn->prepare($msgSql);
$msgStmt->bind_param("i", $matchId);
$msgStmt->execute();
$messages = $msgStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$msgStmt->close();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chatt – Noda UF</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/index.css" />
</head>
<body>

<header class="toppmeny">
  <div class="behallare toppmeny__inre">
    <a class="varumarke" href="index.html">Noda UF</a>

    <nav class="toppmeny__lankar" id="toppmenyLankar" aria-label="Chattmeny">
      <a class="toppmeny__lank" href="privat-start.html">Privat</a>
      <a class="toppmeny__lank" href="foretag-start.html">Företag</a>
      <a class="toppmeny__lank" href="kontakt.html">Kontakt</a>
    </nav>

    <button class="menyknapp" id="menyknapp" aria-label="Öppna meny" aria-expanded="false" aria-controls="toppmenyLankar">
      <svg width="26" height="26" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M4 6h16M4 12h16M4 18h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
  </div>
</header>

<main class="behallare sida">
  <section class="chatt" aria-label="Chatt mellan matchade parter">
    <div class="chatt__header">
      <div>
        <h1>Chatt</h1>
        <p class="text-brod">Gemensam chatt för företag och privatpersoner efter matchning.</p>
      </div>
      <a class="knapp--sekundar" href="privat-start.html">Till matchningar</a>
    </div>

    <div class="chatt__grid">
      <aside class="chatt__panel" aria-label="Matchinfo">

        <div class="chatt__kort">
          <div class="chatt__avatar"></div>
          <div>
            <div class="chatt__namn"><?= e($users[0]["name"]) ?></div>
            <div class="chatt__meta">
              <?= e(typeLabel($users[0]["type"])) ?><?= $users[0]["city"] ? " • " . e($users[0]["city"]) : "" ?>
            </div>
          </div>
        </div>

        <div class="chatt__kort">
          <div class="chatt__avatar chatt__avatar--person"></div>
          <div>
            <div class="chatt__namn"><?= e($users[1]["name"]) ?></div>
            <div class="chatt__meta">
              <?= e(typeLabel($users[1]["type"])) ?><?= $users[1]["city"] ? " • " . e($users[1]["city"]) : "" ?>
            </div>
          </div>
        </div>

        <div class="chatt__rad">
          <span class="chatt__meta">Matchad</span>
          <span class="chatt__meta"><?= e($match["datum"]) ?></span>
        </div>

        <p class="hint">(Live) Hämtar från <code>matches</code> och <code>messages</code>.</p>
      </aside>

      <section class="chatt__panel" aria-label="Meddelanden">
        <div class="chatt__flode" id="chatFlow" data-match-id="<?= (int)$matchId ?>">
          <?php foreach ($messages as $m):
            $own = ((int)$m["sender_id"] === $userId);
            $time = date("H:i", strtotime($m["skickat"]));
          ?>
            <div class="chatt__meddelande <?= $own ? "chatt__meddelande--egen" : "" ?>" data-msg-id="<?= (int)$m["id"] ?>">
              <div class="chatt__bubbla"><?= e($m["text"]) ?></div>
              <span class="chatt__meta"><?= e($own ? "Du" : $m["sender_name"]) ?> • <?= e($time) ?></span>
            </div>
          <?php endforeach; ?>
        </div>

        <form class="chatt__skriv" id="chatForm" autocomplete="off">
          <input class="chatt__input" id="chatInput" type="text" placeholder="Skriv ett meddelande..." aria-label="Skriv ett meddelande" />
          <button class="knapp--primar" type="submit">Skicka</button>
        </form>

        <p class="hint">(Live) WebSocket på <code>ws://localhost:8080</code></p>
      </section>
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

<script>
(() => {
  const flow = document.getElementById("chatFlow");
  const form = document.getElementById("chatForm");
  const input = document.getElementById("chatInput");
  const matchId = parseInt(flow.dataset.matchId, 10);
  const userId = <?= (int)$userId ?>;

  const scrollBottom = () => { flow.scrollTop = flow.scrollHeight; };
  const addMsg = (msg) => {
    const own = (msg.sender_id === userId);

    const wrap = document.createElement("div");
    wrap.className = "chatt__meddelande" + (own ? " chatt__meddelande--egen" : "");
    wrap.setAttribute("data-msg-id", msg.id);

    const bubble = document.createElement("div");
    bubble.className = "chatt__bubbla";
    bubble.textContent = msg.text;

    const meta = document.createElement("span");
    meta.className = "chatt__meta";
    meta.textContent = `${own ? "Du" : msg.sender_name} • ${msg.time}`;

    wrap.appendChild(bubble);
    wrap.appendChild(meta);
    flow.appendChild(wrap);
    scrollBottom();
  };

  const ws = new WebSocket("ws://localhost:8080");

  ws.addEventListener("open", () => {
    ws.send(JSON.stringify({ type: "join", match_id: matchId, user_id: userId }));
    scrollBottom();
  });

  ws.addEventListener("message", (e) => {
    const data = JSON.parse(e.data);

    if (data.type === "error") {
      console.error(data.message);
      return;
    }
    if (data.type === "message" && data.match_id === matchId) {
      addMsg(data);
    }
  });

  form.addEventListener("submit", (ev) => {
    ev.preventDefault();
    const text = input.value.trim();
    if (!text) return;
    input.value = "";

    ws.send(JSON.stringify({ type: "message", text }));
  });

  scrollBottom();
})();
</script>

</body>
</html>
