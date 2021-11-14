window.addEventListener('load', function(){
   M.AutoInit();
    var allimages= document.getElementsByTagName('img');
    if(allimages.length > 0) {
    for (var i=0; i<allimages.length; i++) {
        if (allimages[i].getAttribute('data-src')) {
            allimages[i].setAttribute('src', allimages[i].getAttribute('data-src'));
        }
    }
  }
   var elems = document.querySelectorAll('.dropdown-trigger-menu');
    var options = {
        'closeOnClick': true,
        'coverTrigger': false,
        'constrainWidth': false,
        'hover': true
    }
    var instance = M.Dropdown.init(elems, options);
    var elements = document.getElementsByName('search');
  for(var i=0; i<elements.length; i++) {
    elements[i].addEventListener("keydown", autoComplete(elements[i].value));
  }
  if(document.getElementById("search_year")) {
    var searchYear = document.getElementById("search_year");
    M.CharacterCounter.init(searchYear);
  } 
  var elements = document.getElementsByName('q');
  for(var i=0; i<elements.length; i++) {
    elements[i].setAttribute("class", "autocomplete white-text");
  }
  M.updateTextFields();
}, false);
function ajaxRequest(resultDiv,processing,action,paramName,param,parseJs) {
  if(processing) {
  document.getElementById(resultDiv).innerHTML = processing;
  }
  var xmlhttp= window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
  xmlhttp.onreadystatechange = function() {
   if (this.readyState === 4 && this.status === 200) {
  var resp = this.responseText;
  document.getElementById(resultDiv).innerHTML = resp;
  if(parseJs) {
  var scripts = [];
  while(resp.indexOf("<script") > -1 || resp.indexOf("</script") > -1) {
  var s = resp.indexOf("<script");
  var s_e = resp.indexOf(">", s);
  var e = resp.indexOf("</script", s);
  var e_e = resp.indexOf(">", e);
  scripts.push(resp.substring(s_e+1, e));
  resp = resp.substring(0, s) + resp.substring(e_e+1);
  }
  for(var i=0; i<scripts.length; i++) {
  try {
  var script = document.createElement('script');
  script.text = scripts[i];
  document.body.append(script);
  }
  catch(ex) {
  console.log("Error while executing");
  }
  }
  }
  }
  }
   xmlhttp.open("GET","/ajax?action=" + action + "&" + paramName + "=" + param, true);
   xmlhttp.send();
  }
if ('serviceWorker' in navigator) {
window.addEventListener('load', function () {
navigator.serviceWorker.register('/assets/cache-sw.js').then(function (registration) {
  console.log("Service worker for caching registered successfuly!");
}).catch(function (error) {
  console.log("Failed to register service worker");
});
});
}
    function autoComplete(value,form) {
    var elems = document.querySelectorAll('.autocomplete');
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var result = JSON.parse(this.responseText);
        var data = {};
        for(var i=0; i<result.length; i++) {
          var item = result[i];
          data[item] = null;
        }
        var options = {
        'limit': 5,
        'onAutocomplete': function() {
          document.getElementById(form).submit();
        },
        'data': data
    }
    var instance = M.Autocomplete.getInstance(elems);

        if (instance===null || instance===undefined){
           instance = M.Autocomplete.init(elems, options);
        }else{
           instance.updateData(options);
        }
      }
    };
      xmlhttp.open("GET", "/ajax?action=search&q=" + value, true);
      xmlhttp.send();
  }