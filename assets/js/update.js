var q480 = document.getElementById("q480_switch");
q480.addEventListener("click", function() {
  var div = document.getElementById("q480");
  if(q480.checked == true) {
    div.removeAttribute("class");
  } else {
    div.setAttribute("class", "hidden");
  }
});
var q720 = document.getElementById("q720_switch");
q720.addEventListener("click", function() {
  var div = document.getElementById("q720");
  if(q720.checked == true) {
    div.removeAttribute("class");
  } else {
    div.setAttribute("class", "hidden");
  }
});
var q1080 = document.getElementById("q1080_switch");
q1080.addEventListener("click", function() {
  var div = document.getElementById("q1080");
  if(q1080.checked == true) {
    div.removeAttribute("class");
  } else {
    div.setAttribute("class", "hidden");
  }
});