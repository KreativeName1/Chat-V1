var ajax = new XMLHttpRequest();
var state = document.getElementById("state");
var chat = document.getElementById("chat");
var message_box = document.getElementById("message");

// Benutzerdaten aus der Session holen
ajax.open("GET", "php/session.php", false);
ajax.send();
var user = JSON.parse(ajax.response);
if (user == null) {
  window.location.href = "login.php";
}

// IP aus der Datei holen und mit dem WebSocket verbinden
ajax.open("GET", "ip.php", false);
ajax.send();
var ip = ajax.response;
websocket = new WebSocket("ws://"+ip+":8080/chatserver.php");
state.innerHTML = "verbinden...";


websocket.onerror = function(ev) { state.innerHTML = "Fehler"; }
websocket.onclose = function(ev) { state.innerHTML = "Nicht Verbunden"; }

websocket.onopen = function(ev) {
  state.innerHTML = "Verbunden";

  // Raum beitreten
  websocket.send(JSON.stringify({
    type: 'join',
    room: chat_id,
    user_id: user.id,
    user_name: user.name,
  }));
}


websocket.onmessage = function(ev) {

  // Nachrichten parsen
  var response = JSON.parse(ev.data);

  // Die verschiedenen Nachrichtenarten verarbeiten
  switch (response.type) {
    case 'message': chat.innerHTML += "<div class='message'><div class='message-header'><span>" + response.date + "</span></div><div class='message-body'><div class='message-text'>" + response.message + "</div></div></div>"; break;
    case 'system': chat.innerHTML += "<div class='message'><div class='message-body'><div class='message-text bold'>" + response.message + "</div></div></div>"; break;
    case 'room': room = response.room; break;
    case 'db_message':
    if (response.user_id == user.id) chat.innerHTML += "<div class='message me'><div class='message-header'><span>" + response.date + "</span></div><div class='message-body'><div class='message-text'>" + response.message + "</div></div></div>";
    else chat.innerHTML += "<div class='message'><div class='message-header'><span>" + response.date + "</span></div><div class='message-body'><div class='message-text'>" + response.message + "</div></div></div>";
    break;
  }

  // Zum Ende scrollen
  chat.scrollTop = chat.scrollHeight;
}


// Wenn Enter gedrückt wird, sende Nachricht
message_box.addEventListener("keyup", function(event) {
  event.preventDefault();
  if (event.keyCode === 13) send();
});

// Nachricht senden
function send() {
  var message = message_box.value;
  message_box.value = '';

  // Nachricht senden, wenn sie nicht leer ist
  if (message != "") {
    var date = new Date().toLocaleString('de-DE', { day: 'numeric',month: 'numeric',year: 'numeric',hour: 'numeric',minute: 'numeric' })
    chat.innerHTML += "<div class='message me'><div class='message-header'><span>" + date + "</span></div><div class='message-body'><div class='message-text'>" + message + "</div></div></div>";
    // Zum Ende scrollen
    chat.scrollTop = chat.scrollHeight;
    websocket.send(JSON.stringify({
      type: 'message',
      message: message,
      name: user.name,
      date: date,
      user_id: user.id,
      room: room
    }));
  }
}