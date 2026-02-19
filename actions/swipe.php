<?php
session_start();
require __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$fromUser = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$toUser   = isset($_POST['to_user']) ? (int)$_POST['to_user'] : 0;
$typ      = $_POST['typ'] ?? '';

if ($fromUser <= 0) {
  http_response_code(401);
  echo json_encode(["error" => "Inte inloggad"], JSON_UNESCAPED_UNICODE);
  exit;
}

if ($toUser <= 0 || $toUser === $fromUser || !in_array($typ, ['upp','ner'], true)) {
  http_response_code(400);
  echo json_encode(["error" => "Ogiltig data"], JSON_UNESCAPED_UNICODE);
  exit;
}

$matchSkapad = false;
$matchId = null;

$conn->begin_transaction();

try {
  // 1) Spara swipe (UNIQUE(from_user,to_user) finns i DB)
  $stmt = $conn->prepare("
    INSERT INTO swipes (from_user, to_user, typ)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE typ = VALUES(typ)
  ");
  $stmt->bind_param("iis", $fromUser, $toUser, $typ);
  $stmt->execute();
  $stmt->close();

  // 2) Om upp: kolla om motparten redan gillat
  if ($typ === 'upp') {
    $stmt = $conn->prepare("
      SELECT 1
      FROM swipes
      WHERE from_user = ?
        AND to_user = ?
        AND typ = 'upp'
      LIMIT 1
    ");
    $stmt->bind_param("ii", $toUser, $fromUser);
    $stmt->execute();
    $reciprocated = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if ($reciprocated) {
      // 3) Skapa match sorterat (UNIQUE(user1_id,user2_id) finns i DB)
      $u1 = min($fromUser, $toUser);
      $u2 = max($fromUser, $toUser);

      $stmt = $conn->prepare("
        INSERT INTO matches (user1_id, user2_id)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE id = id
      ");
      $stmt->bind_param("ii", $u1, $u2);
      $stmt->execute();

      if ($stmt->affected_rows === 1) {
        $matchSkapad = true;
        $matchId = (int)$stmt->insert_id;
        $stmt->close();
      } else {
        $stmt->close();

        $stmt2 = $conn->prepare("
          SELECT id FROM matches
          WHERE user1_id = ? AND user2_id = ?
          LIMIT 1
        ");
        $stmt2->bind_param("ii", $u1, $u2);
        $stmt2->execute();
        $row = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        $matchId = $row ? (int)$row["id"] : null;
      }
    }
  }

  $conn->commit();

  echo json_encode([
    "success" => true,
    "match" => $matchSkapad,
    "match_id" => $matchId
  ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  $conn->rollback();
  http_response_code(500);
  echo json_encode(["error" => "Serverfel"], JSON_UNESCAPED_UNICODE);
}
