<?php
session_start();
require __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($userId <= 0) {
  http_response_code(401);
  echo json_encode(["error" => "Inte inloggad"], JSON_UNESCAPED_UNICODE);
  exit;
}

/*
  Viktigt:
  - plocka bara arbetstagare
  - exkludera mig själv
  - exkludera profiler jag redan swipat på (så kortleken töms)
*/
$sql = "
  SELECT 
    u.id,
    u.namn,
    u.stad,
    u.biografi,
    u.profilbild,
    t.namn AS tag_namn
  FROM users u
  LEFT JOIN user_tags ut ON ut.user_id = u.id
  LEFT JOIN tags t ON t.id = ut.tag_id
  WHERE u.kontotyp = 'arbetstagare'
    AND u.id <> ?
    AND u.id NOT IN (
      SELECT s.to_user
      FROM swipes s
      WHERE s.from_user = ?
    )
  ORDER BY u.id DESC
  LIMIT 200
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$res = $stmt->get_result();

$profilerMap = [];

while ($row = $res->fetch_assoc()) {
  $id = (int)$row["id"];

  if (!isset($profilerMap[$id])) {
    $profilerMap[$id] = [
      "id" => $id,
      "namn" => $row["namn"] ?? "",
      "stad" => $row["stad"] ?? "",
      "bio" => $row["biografi"] ?? "",
      "profilbild" => $row["profilbild"] ?? null,
      "taggar" => []
    ];
  }

  if (!empty($row["tag_namn"])) {
    // undvik dubletter om DB råkar ge samma tagg flera gånger
    if (!in_array($row["tag_namn"], $profilerMap[$id]["taggar"], true)) {
      $profilerMap[$id]["taggar"][] = $row["tag_namn"];
    }
  }
}

$stmt->close();

echo json_encode(array_values($profilerMap), JSON_UNESCAPED_UNICODE);
