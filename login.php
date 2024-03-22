<html>
<head>
  <title>Client Login</title>
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
    <form action="" method="post">
      <h1>Login</h1>
      <div>
        <?php
          if (isset($_POST["submit"])) {
            include 'Funktionen.php';

            // Daten aus Formular holen
            $email = $_POST["email"];
            $password = $_POST["password"];
            echo "<p>Email: $email</p>";
            echo "<p>Passwort: $password</p>";
            // Benutzer in Datenbank suchen
            $connection = connect("chat", "root");
            $sql = "SELECT * FROM users WHERE email = :email";
            $result = runQuery($connection, $sql, [':email' => $email]);

            // Überprüfen, ob Benutzer gefunden wurde
            if ($result) {
              // Überprüfen, ob Passwort stimmt
              if (password_verify($password, $result["password"])) {

                // Benutzer in Session speichern und weiterleiten
                echo "<p style='color:green'>Login erfolgreich!</p>";
                session_start();
                $_SESSION["user"] = [
                  "id" => $result["id"],
                  "name" => $result["name"],
                  "email" => $result["email"],
                  "avatar" => $result["avatar"]
                ];
                header("Location: contacts.php");
              }
              else {
                echo "<p style='color:red'>Passwort ist falsch!</p>";
              }
            }
            else {
              echo "<p style='color:red'>Benutzer nicht gefunden!</p>";
            }
          }
        ?>
      </div>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Passwort" required>
      <input type="submit" name="submit" value="Login">
    </form>
  </main>
</body>
</html>