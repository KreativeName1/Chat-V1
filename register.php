<html>
<head>
  <title>Client</title>
  <link rel="stylesheet" type="text/css" href="chat.css">
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
  <meta name="viewport" content="width=device-width">
</head>
<body>
  <header>
    <h1>Client</h1>
    <nav>
      <a href="login.php">Login</a>
      <a href="register.php">Registrieren</a>
    </nav>
  </header>
  <main>
    <form action="" method="post" enctype="multipart/form-data">
      <h1>Registrieren</h1>
      <div>
        <?php
          if (isset($_POST["submit"])) {
            include 'Funktionen.php';
            // Daten aus Formular auslesen
            $name = $_POST["name"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $password2 = $_POST["password2"];

            // Wenn die Passwörter übereinstimmt
            if ($password == $password2) {
              $password = password_hash($password, PASSWORD_DEFAULT);

              // Falls Profilbild hochgeladen wurde
              if (isset($_FILES["avatar"])) {
                $avatar = $_FILES["avatar"];
                $avatar_name = "uploads/" . $avatar["name"];
                move_uploaded_file($_FILES["avatar"]["tmp_name"], $avatar_name);
              } else {
                $avatar = 'uploads/default.png';
              }

              // Benutzer in Datenbank speichern
              $connection = connect("chat", "root");
              $sql = "INSERT INTO users (name, email, password, avatar) VALUES (:name, :email, :password, :avatar)";
              $ergebnis = runQuery($connection, $sql, [
                ':name' => $name,
                ':email' => $email,
                ':password' => $password,
                ':avatar' => $avatar
              ]);

              // Überprüfung, ob erfolgreich
              if ($ergebnis) echo "<p style='color:red'>Registrierung Fehlgeschlagen!<p>";
              else echo "<p style='color:green'>Registrierung erfolgreich!<p>";
            } else {
              echo "<p style='color:red'>Passwörter stimmen nicht überein!<p>";
            }
          }
        ?>
      </div>
      <input type="text" name="name" placeholder="Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Passwort" required>
      <input type="password" name="password2" placeholder="Passwort wiederholen" required>
      <input type="file" name="avatar" accept="image/png">
      <input type="submit" name="submit" value="Registrieren">
    </form>
  </main>
</body>
</html>