<html>
<head>
  <title>Client Kontakte</title>
  <link rel="stylesheet" type="text/css" href="chat.css">
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
  <meta name="viewport" content="width=device-width">
  <script src="contacts.js"></script>
</head>
<body>
  <header>
    <h1>Client</h1>
    <nav>
      <a href="php/logout.php">Logout</a>
      <img src="<?php session_start(); echo $_SESSION["user"]["avatar"] ?>" alt="Avatar" id="avatar" />
    </nav>
  </header>
  <main class="wrapper">
    <h2 class="center">Kontakte</h2>
      <div class="center add-kontakt">
      <input class="forminput" type="text" id="name" placeholder="Name" />
      <div class="add-kontakt-buttons">
        <input class="formbutton forminput" type="submit" onclick="sendRequest()" value="Hinzufügen" />
        <button class="formbutton forminput" onclick="showRequests()">Anfragen</button>
      </div>
      </div>
    <?php
      include 'Funktionen.php';
      if (!isset($_SESSION["user"])) {
        header("Location: login.php");
      }

      // Verbindung zur Datenbank herstellen
      $connection = connect("chat", "root");
      if ($connection == false) {
        die("Etwas ist schief gelaufen!");
      }

      // Alle Benutzer holen, die der Benutzer als Kontakt hinzugefügt hat
      $sql = "SELECT * FROM friends f INNER JOIN users u ON f.friend_id = u.id WHERE f.user_id = :id";
      $result = runQueryAll($connection, $sql, [":id" => $_SESSION["user"]["id"]]);

      if ($result) {
        foreach ($result as $friend) {
          echo "<div class='freund'>";
          echo "<div>";
          echo "<img class='avatar' src='{$friend["avatar"]}' alt='Avatar' />";
          echo "<div class='name'>{$friend["name"]}</div>";
          echo "</div>";
          echo "<div>";
          if ($friend["state"] == 'accepted') {
                echo "<button class='formbutton forminput small' onclick='window.location.href=\"chat.php?id={$friend["id"]}&name={$friend["name"]}\"'>Chat</button>";
                echo "<button class='formbutton forminput small' onclick='remove({$friend["id"]})'>Entfernen</button>";
              } elseif ($friend["state"] == 'sent') {
                echo "<p class='status'>Anfrage gesendet</p>";
              } elseif ($friend["state"] == 'rejected') {
                echo "<p class='status abgelehnt'>Anfrage abgelehnt</p>";
                echo "<button class='formbutton forminput small' onclick='remove({$friend["id"]})'>Entfernen</button>";
              }
              echo "</div>";
          echo "</div>";
        }
      }
    ?>
  </main>
</body>
</html>