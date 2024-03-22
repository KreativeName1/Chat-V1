<?php
namespace MyApp;
require 'vendor/autoload.php';
require 'Funktionen.php';
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
class Chat implements MessageComponentInterface {
  protected $clients;
  protected $rooms;
  protected $connection;

  public function __construct() {
    echo "Server gestartet!\n";
    $this->clients = new \SplObjectStorage();
    $this->rooms = [];
    echo "Verbindung zur Datenbank herstellen...\n";
    $this->connection = connect("chat", "root");
    if (!$this->connection) { echo "Verbindung zur Datenbank fehlgeschlagen!"; exit; }
    else { echo "Verbindung zur Datenbank erfolgreich!\n"; }
  }

  public function onOpen(ConnectionInterface $conn) {
    echo "Neue Verbindung! ({$conn->resourceId})\n";
    $this->clients->attach($conn);
  }
  public function onClose(ConnectionInterface $conn) { $this->clients->detach($conn); }
  public function onError(ConnectionInterface $conn, \Exception $e) { $conn->close(); }

  public function onMessage(ConnectionInterface $from, $msg) {
    // Nachricht dekodieren und dann an die Funktionen weiterleiten
    $data = json_decode($msg, true);
    switch ($data['type']) {
      case 'join':    $this->joinRoom($from, $data['room'], $data['user_id'], $data['user_name']); break;
      case 'message': $this->sendMessage($from, $data['room'], $data['message'], $data['name'], $data['date'], $data['user_id']); break;
    }
  }

  protected function joinRoom(ConnectionInterface $conn, $room, $user_id, $user_name) {

    // Überprüfen, ob der Raum existiert. Wenn nicht, erstellen
    if (!isset($this->rooms[$room])) {
      $this->rooms[$room] = new \SplObjectStorage();
      echo "Raum $room erstellt\n";
    }

    // Benutzer dem Chat hinzufügen
    $this->rooms[$room]->attach($conn);
    echo "\"$user_name\" wurde zu $room hinzugefügt!\n";
    // Dem Client die Raumnummer senden
    $conn->send(json_encode([
      'type' => 'room',
      'room' => $room,
      // 'user_id' => $user_id
      ]));
      // Alle bisherigen Nachrichten aus der Datenbank holen sortiert nach Datum
    $sql = "SELECT m.*,  u.name FROM messages m INNER JOIN users u ON m.user_id = u.id WHERE m.chat_id = :chat_id ORDER BY m.timestamp ASC";
    $result = runQueryAll($this->connection, $sql, [":chat_id" => $room]);

    // Alle bisherigen Nachrichten an den Client senden, falls vorhanden
    if ($result) {
      foreach ($result as $message) {
        $message["timestamp"] = date("d.m.Y, H:i", strtotime($message["timestamp"]));
        $conn->send(json_encode([
          'type' => 'db_message',
          'message' => $message["message"],
          'name' => $message["name"],
          'date' => $message["timestamp"],
          'user_id' => $message["user_id"]
        ]));
      }
    }

  }

  protected function sendMessage(ConnectionInterface $from, int $room, string $message, string $name, $date, int $user_id) {
    echo "Von: $name [$user_id] | Am: $date | Raum: $room\n Nachricht: $message\n";

    // Nachricht an alle anderen Clients im Raum senden
    foreach ($this->rooms[$room] as $client) {
      if ($from != $client) {
        $client->send(json_encode([
          'type' => 'message',
          'message' => $message,
          'name' => $name,
          'date' => $date,
          'user_id' => $user_id
        ]));
      }
    }

    // Nachricht in der Datenbank speichern
    $sql = "INSERT INTO messages (chat_id, user_id, message) VALUES (:chat_id, :user_id, :message)";
    runQuery($this->connection, $sql, [":chat_id" => $room, ":user_id" => $user_id, ":message" => $message]);
  }
}

// Server erstellen und starten
echo "Chat Server - Version 2\n-----------------------\nDrücken Sie STRG+C um den Server zu beenden.\n\nServer starten...\n";
$server = IoServer::factory(
  new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);
$server->run();
