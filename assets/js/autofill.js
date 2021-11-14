// if page loaded,
window.addEventListener('load', function(){ // execute this function
    var inputs = document.cookie.split(';'); // get all cookies
    for(var i = 0; i < inputs.length; i++) { // loop them
        var data = inputs[i].split("="); // split cookie name and value
        var inputName = data[0].replace(/^\s+|\s+$/g,""); // trim extra white spaces from name and store
        var inputValue = data[1]; // store cookie value
        if(document.getElementById(inputName)) { // if input exists with the id 
            document.getElementById(inputName).value = inputValue; // finally set the input value from last input stored in the cookie
        }
    }
 }, false);
var inputs, textareas, index, i; // basic variables declaring
inputs = document.getElementsByTagName('input'); // get all input tags
for (index = 0; index < inputs.length; ++index) { // loop them
    if(inputs[index].id != "") { // if input have a id
        document.getElementById(inputs[index].id).addEventListener("keyup", function() { // on keyup event,
            setCookie(this.name,this.value,1); // set cookie of current input value
        });
        document.getElementById(inputs[index].id).addEventListener("blur", function() { // losing foci=us of current input,
            setCookie(this.name,this.value,1); // set cookie of current input value
        });
    }
}
textareas = document.getElementsByTagName('textarea'); // get all textarea tags
for (i = 0; i < textareas.length; ++i) { // loop them
    if(textareas[i].id != "") { // if textarea have a id
        document.getElementById(textareas[i].id).addEventListener("keyup", function() { // on keyup event,
            setCookie(this.name,this.value,1); // set cookie of current textarea value
        });
        document.getElementById(textareas[i].id).addEventListener("blur", function() { // losing foci=us of current textarea,
            setCookie(this.name,this.value,1); // set cookie of current textarea value
        });
    }
}
var ele = document.getElementById("form"); // get the form
if (ele.addEventListener) { // if addevent listener supported
    ele.addEventListener("submit", clearCookies, false); // clear all saved cookies of inputs
} else if(ele.attachEvent) { // if addevent listener supported in internet explorer
    ele.attachEvent('onsubmit', clearCookies); // clear all saved cookies of inputs
}
function clearCookies() { // clear all cookies of saved inputs
    for (index = 0; index < inputs.length; ++index) {
        if(getCookie(inputs[index].id) != "") {
            setCookie(inputs[index].id,"",0);
        }
    }
}