<html>
<head>
  <title>Chat</title>
  <link rel="stylesheet" type="text/css" href="chat.css">
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
  <meta name="viewport" content="width=device-width">
  <?php
  include 'Funktionen.php';
  session_start();
  if (!isset($_SESSION["user"])) {
    header("Location: login.php");
  }

  // ID des Freundes und des Benutzers holen
  $friend_id = $_GET["id"];
  $friend_name = $_GET["name"];
  $user_id = $_SESSION["user"]["id"];

  // Verbindung zur Datenbank herstellen
  $connection = connect("chat", "root");

  // überprüfen, ob der Benutzer mit dem Freund befreundet ist
  $sql = "SELECT * FROM friends WHERE user_id = :user_id AND friend_id = :friend_id AND state = 'accepted'";
  $result = runQuery($connection, $sql, [":user_id" => $user_id, ":friend_id" => $friend_id]);
  if ($result == false) header("Location: kontakte.php");

  // Chat ID holen, falls der Chat schon existiert. Wenn nicht, dann erstellen und ID holen
  $sql = "SELECT id FROM chats WHERE (user_1 = :user_id AND user_2 = :friend_id) OR (user_1 = :friend_id AND user_2 = :user_id)";
  $result = runQuery($connection, $sql, [":user_id" => $user_id, ":friend_id" => $friend_id]);
  if ($result == false) {
    $sql = "INSERT INTO chats (user_1, user_2) VALUES (:user_id, :friend_id); SELECT LAST_INSERT_ID() AS id";
    $result = runQuery($connection, $sql, [":user_id" => $user_id, ":friend_id" => $friend_id]);
    $sql = "SELECT LAST_INSERT_ID() AS id";
    $result = runQuery($connection, $sql);
  }
  echo "<script>var chat_id = $result[id];</script>";
  ?>
  <script defer src="chat.js"></script>
</head>
<body>
  <header>
    <div>
      <h1>Chat</h1>
      <div id="state">Nicht Verbunden</div>
    </div>
    <div id="friend_name">
      <?php echo $friend_name; ?>
    </div>
    <nav>
      <a href="contacts.php">Kontakte</a>
      <a href="php/logout.php">Logout</a>
      <img src="<?php echo $_SESSION["user"]["avatar"] ?>" alt="Avatar" id="avatar" />
    </nav>
  </header>
  <main id="chat">
  </main>
  <footer id="input">
    <div class="input">
    <input type="text" id="message" />
    <input type="button" id="send" onclick="send()" value="Senden" />
  </div>
  </footer>
</body>
</html>