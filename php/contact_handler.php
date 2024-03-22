<?php
include '../Funktionen.php';
session_start();

$connection = connect("chat", "root");
if (!$connection) die("Etwas ist schief gelaufen!");

$user_id = $_SESSION["user"]["id"];
$action = $_POST["action"];

if ($action == "request") {

  $friend_name = $_POST["name"];

  // Wenn der Benutzer sich selbst hinzufügen will, dann abbrechen
  if ($friend_name == $_SESSION["user"]["name"]) {
    echo "Du kannst dich nicht selbst hinzufügen!";
    exit;
  }

  $sql = "SELECT * FROM users WHERE name = :name";
  $result = runQuery($connection, $sql, [":name" => $friend_name]);
  if ($result) {
    $friend = $result;
  } else {
    echo "Der Benutzer wurde nicht gefunden!";
    exit;
  }
  // SQL-Statement
  $sql = "INSERT INTO friends (user_id, friend_id) VALUES (:user_id, :friend_id)";
  runQuery($connection, $sql, [":user_id" => $user_id, ":friend_id" => $friend["id"]]);
  echo "ok";
}


else if ($action == "remove") {

  $friend_id = $_POST["id"];

  $sql = "DELETE FROM friends WHERE friend_id = :friend_id AND user_id = :user_id";
  runQuery($connection, $sql, [":friend_id" => $friend_id, ":user_id" => $user_id]);

  $sql = "DELETE FROM friends WHERE friend_id = :user_id AND user_id = :friend_id";
  runQuery($connection, $sql, [":user_id" => $user_id, ":friend_id" => $friend_id]);
  echo "ok";
}


else if ($action == "accept") {

  $friend_id = $_POST["id"];

  $sql = "UPDATE friends SET state = 'accepted' WHERE friend_id = :user_id AND friend_id = :user_id";
  runQuery($connection, $sql, [":friend_id" => $friend_id, ":user_id" => $user_id]);

  $sql = "INSERT INTO friends (user_id, friend_id, state) VALUES (:friend_id, :user_id, 'accepted')";
  runQuery($connection, $sql, [":user_id" => $friend_id, ":friend_id" => $user_id]);
  echo "ok";
}


else if ($action == "reject") {

    $friend_id = $_POST["id"];

    $sql = "UPDATE friends SET state = 'rejected' WHERE friend_id = :user_id AND friend_id = :user_id";
    runQuery($connection, $sql, [":friend_id" => $friend_id, ":user_id" => $user_id]);
    echo "ok";
}
else if ($action == "requests") {

  // Alle Benutzer holen, die den Benutzer eine Anfrage geschickt haben und status = gesendet
  $sql = "SELECT * FROM friends f INNER JOIN users u ON f.user_id = u.id WHERE f.friend_id = :id AND f.state = 'sent'";
  $result = runQueryAll($connection, $sql, [":id" => $_SESSION["user"]["id"]]);
  // Wenn es keine Anfragen gibt, dann eine Nachricht ausgeben
  if (!$result) {
    echo "<h2 style='text-align:center'>Du hast keine Anfragen!</h2>";
    exit;
  }
  foreach ($result as $friend) {
    echo "
    <div class='freund'>
      <img class='avatar' src='{$friend["avatar"]}' alt='Avatar' />
      <div class='name'>{$friend["name"]}</div>
      <div class='options'>
        <button class='formbutton forminput small' onclick='accept({$friend["id"]})'>Annehmen</button>
        <button class='formbutton forminput small' onclick='reject({$friend["id"]})'>Ablehnen</button>
      </div>
    </div>
    ";
  }
}
else {
  echo "Aktion nicht gefunden!";
}