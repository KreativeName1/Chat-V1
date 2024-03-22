# Chat App

Das ist ein schon etwas älteres Projekt von mir. Es ist eine Chat App die ich in PHP geschrieben habe.
Es benutzt eine MySQL Datenbank um die User und Nachrichten zu speichern und einen WebSocket Server um die Nachrichten in Echtzeit zu versenden.

## Verwendete Technologien
- ![PHP](https://img.shields.io/badge/-PHP-000000?style=flat&logo=PHP)
- ![HTML](https://img.shields.io/badge/-HTML-000000?style=flat&logo=HTML5)
- ![CSS](https://img.shields.io/badge/-CSS-000000?style=flat&logo=CSS3)
- ![MySQL](https://img.shields.io/badge/-MySQL-000000?style=flat&logo=MySQL)
- ![JavaScript](https://img.shields.io/badge/-JavaScript-000000?style=flat&logo=JavaScript)
- WebSockets (PHP/Ratchet)

## Features

- Registrierung mit Profilbild
- Login
- Kontaktliste mit Anfragenverwaltung
  - Zum Suchen von Usern ganze Namen eingeben!
- In seperaten Chaträumen schreiben
- Nachrichten in Echtzeit versenden


## Setup

1. Composer installieren
2. `composer install` in der Konsole in dem Projektordner ausführen
3. Apache/MySQL Server starten
4. Datenbank.sql in Datenbank importieren
5. start.bat ausführen oder direct `php chatserver.php` in der Konsole eingeben