<?php
session_start();
require __DIR__ . "/../config/db.php";

header("Content-Type: application/json; charset=utf-8");

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
  http_response_code(401);
  echo json_encode(["error" => "Inte inloggad"]);
  exit;
}

$annonser = [];

$stmt = $conn->prepare("
  SELECT 
    a.id,
    a.titel,
    a.beskrivning AS text,
    a.stad,
    a.anstallningsform AS form,
    a.sokt_tjanst AS tjanst,
    a.adress,
    a.foretagsnamn AS foretag,
    a.user_id
  FROM annons a
  WHERE a.user_id != ?
  ORDER BY a.skapad_datum DESC
");

$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
  $row['taggar'] = [];
  $annonser[] = $row;
}

$stmt->close();

echo json_encode($annonser, JSON_UNESCAPED_UNICODE);
