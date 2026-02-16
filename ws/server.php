<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/db.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;

class ChatServer implements MessageComponentInterface {
  private $clients;
  private $rooms = []; // match_id => SplObjectStorage
  private $db;         // mysqli

  public function __construct($mysqliConn) {
    $this->clients = new \SplObjectStorage();
    $this->db = $mysqliConn;
    echo "WS chat server running...\n";
  }

  public function onOpen(ConnectionInterface $conn) {
    $this->clients->attach($conn);
  }

  public function onMessage(ConnectionInterface $from, $msg) {
    $data = json_decode($msg, true);
    if (!$data || !isset($data["type"])) return;

    if ($data["type"] === "join") {
      $matchId = (int)($data["match_id"] ?? 0);
      $userId  = (int)($data["user_id"] ?? 0);
      if ($matchId <= 0 || $userId <= 0) return;

      // access check
      $chk = $this->db->prepare("SELECT user1_id, user2_id FROM matches WHERE id = ? LIMIT 1");
      $chk->bind_param("i", $matchId);
      $chk->execute();
      $row = $chk->get_result()->fetch_assoc();
      $chk->close();

      if (!$row) {
        $from->send(json_encode(["type"=>"error","message"=>"Match finns inte."]));
        return;
      }
      if ((int)$row["user1_id"] !== $userId && (int)$row["user2_id"] !== $userId) {
        $from->send(json_encode(["type"=>"error","message"=>"Ingen access till denna chatt."]));
        return;
      }

      $from->match_id = $matchId;
      $from->user_id  = $userId;

      if (!isset($this->rooms[$matchId])) {
        $this->rooms[$matchId] = new \SplObjectStorage();
      }
      $this->rooms[$matchId]->attach($from);

      $from->send(json_encode(["type"=>"joined","match_id"=>$matchId]));
      return;
    }

    if ($data["type"] === "message") {
      $matchId = (int)($from->match_id ?? 0);
      $userId  = (int)($from->user_id ?? 0);
      $text    = trim((string)($data["text"] ?? ""));

      if ($matchId <= 0 || $userId <= 0 || $text === "") return;
      if (mb_strlen($text) > 2000) return;

      // spara i DB
      $ins = $this->db->prepare("INSERT INTO messages (match_id, sender_id, text) VALUES (?, ?, ?)");
      $ins->bind_param("iis", $matchId, $userId, $text);
      $ins->execute();
      $newId = $ins->insert_id;
      $ins->close();

      // hämta namn
      $nameStmt = $this->db->prepare("SELECT namn FROM users WHERE id = ? LIMIT 1");
      $nameStmt->bind_param("i", $userId);
      $nameStmt->execute();
      $nameRow = $nameStmt->get_result()->fetch_assoc();
      $nameStmt->close();

      $senderName = $nameRow ? $nameRow["namn"] : "Okänd";
      $time = date("H:i");

      $payload = [
        "type" => "message",
        "id" => (int)$newId,
        "match_id" => $matchId,
        "sender_id" => $userId,
        "sender_name" => $senderName,
        "text" => $text,
        "time" => $time
      ];

      // broadcast till room
      if (isset($this->rooms[$matchId])) {
        foreach ($this->rooms[$matchId] as $client) {
          $client->send(json_encode($payload, JSON_UNESCAPED_UNICODE));
        }
      }
      return;
    }
  }

  public function onClose(ConnectionInterface $conn) {
    $this->clients->detach($conn);
    $matchId = $conn->match_id ?? null;

    if ($matchId && isset($this->rooms[$matchId])) {
      $this->rooms[$matchId]->detach($conn);
      if (count($this->rooms[$matchId]) === 0) {
        unset($this->rooms[$matchId]);
      }
    }
  }

  public function onError(ConnectionInterface $conn, \Exception $e) {
    $conn->close();
  }
}

$server = IoServer::factory(
  new HttpServer(new WsServer(new ChatServer($conn))),
  8080
);

$server->run();
