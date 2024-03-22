var xhr = new XMLHttpRequest();

function showRequests() {
  var requests = document.createElement("div");
  document.body.appendChild(requests);
  requests.setAttribute("id", "anfragen");
  var box = requests.appendChild(document.createElement("div"));
  box.className = "box";

  requests.addEventListener("click", function(e) {
    if (e.target == requests) requests.remove();
  });
  xhr.open("POST", "php/contact_handler.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
      var response = xhr.responseText;
      document.getElementsByClassName("box")[0].innerHTML = response;
    }
  }
  xhr.send("action=requests");
}
function sendRequest() {
  xhr.open("POST", "php/contact_handler.php", true);
  var name = document.getElementById("name").value;
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
      var response = xhr.responseText;
      if (response == "ok") window.location.reload();
      else alert(response);
    }
  }
  xhr.send("name=" + name + "&action=request");
}
function accept(id) {
  xhr.open("POST", "php/contact_handler.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) window.location.reload();
  }
  xhr.send("id=" + id + "&action=accept");
}
function reject(id) {
  xhr.open("POST", "php/contact_handler.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) window.location.reload();
  }
  xhr.send("id=" + id + "&action=reject");
}
  function remove(id) {
    xhr.open("Post", "php/contact_handler.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
      if (xhr.readyState == 4 && xhr.status == 200) window.location.reload();
    }
    xhr.send("id=" + id + "&action=remove");
  }