function setCookie(name, value, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}
  
function getCookie(name) {
    var target = name + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(target) == 0) {
        return c.substring(target.length, c.length);
      }
    }
    return "";
}
  
function checkCookie(name) {
    var name = getCookie(name);
    if (name != "") {
        return true;
    } else {
        return false;
    }
} 