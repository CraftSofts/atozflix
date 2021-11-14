var username = document.getElementById("username");
var first_name = document.getElementById("first_name");
var last_name = document.getElementById("last_name");
var email = document.getElementById("email");
var password = document.getElementById("password");
var password_confirm = document.getElementById("password_confirm");
var show = document.getElementById("show");
var reloadBtn = document.getElementById("reload_btn");
username.addEventListener("blur", function() {
    document.getElementById("username_check").innerHTML = "";
   });
username.addEventListener("focus", function() {
    document.getElementById("username_check").innerHTML = '<i class="material-icons middled">info</i> Username should contain letters, numbers and underscore only';
});
username.addEventListener("keyup", function() {
    ajaxRequest("username_check","<i class=\"material-icons middled\">hourglass_full</i> Checking availabilty...","check_username","username",username.value);
});
first_name.addEventListener("blur", function() {
    document.getElementById("first_name_check").innerHTML = "";
   });
first_name.addEventListener("focus", function() {
    document.getElementById("first_name_check").innerHTML = '<i class="material-icons middled">info</i> First Name should contain letters only';
});
last_name.addEventListener("blur", function() {
    document.getElementById("last_name_check").innerHTML = "";
   });
last_name.addEventListener("focus", function() {
    document.getElementById("last_name_check").innerHTML = '<i class="material-icons middled">info</i> Last Name should contain letters only';
});
email.addEventListener("blur", function() {
    document.getElementById("email_check").innerHTML = "";
   });
email.addEventListener("keyup", function() {
    var emailPattern = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    if(email.value.length > 10) {
        if(emailPattern.test(String(email.value).toLowerCase()) == false) {
            document.getElementById("email_check").innerHTML = '<span class="red-text"><i class="material-icons middled">warning</i> The email is invalid!</span>';
        } else {
            document.getElementById("email_check").innerHTML = '';
        }
    } else {
        document.getElementById("email_check").innerHTML = '';
    }
});
password.addEventListener("blur", function() {
    document.getElementById("hidden-div").style.display = "none";
    document.getElementById("password_check").innerHTML = "";
   });
password.addEventListener("focus", function() {
    document.getElementById("hidden-div").style.display = "block";
});
password.addEventListener("keyup", function() {
    var letter = document.getElementById("letter");
    var capital = document.getElementById("capital");
    var specialChar = document.getElementById("special");
    var number = document.getElementById("number");
    var length = document.getElementById("length");
    var lowerCaseLetters = /[a-z]/g;
    if(password.value.match(lowerCaseLetters)) {
        letter.classList.remove("red-text");
        letter.classList.add("green-text");
    } else {
        letter.classList.remove("green-text");
        letter.classList.add("red-text");
    }
    var upperCaseLetters = /[A-Z]/g;
    if(password.value.match(upperCaseLetters)) {
        capital.classList.remove("red-text");
        capital.classList.add("green-text");
    } else {
        capital.classList.remove("green-text");
        capital.classList.add("red-text");
    }
    var numbers = /[0-9]/g;
    if(password.value.match(numbers)) {
        number.classList.remove("red-text");
        number.classList.add("green-text");
    } else {
        number.classList.remove("green-text");
        number.classList.add("red-text");
    }
    if(password.value.length >= 6) {
        length.classList.remove("red-text");
        length.classList.add("green-text");
    } else {
        length.classList.remove("green-text");
        length.classList.add("red-text");
    }
    var specialCharacter = /\W+/g;
    if(password.value.match(specialCharacter)) {
        specialChar.classList.remove("red-text");
        specialChar.classList.add("green-text");
    } else {
        specialChar.classList.remove("green-text");
        specialChar.classList.add("red-text");
    }
});
password_confirm.addEventListener("blur", function() {
    document.getElementById("password_confirm_check").innerHTML = "";
   });
password_confirm.addEventListener("focus", function() {
    document.getElementById("password_confirm_check").innerHTML = '<i class="material-icons middled">info</i> Both passwords must be same';
});
password_confirm.addEventListener("keyup", function() {
    if(password_confirm.value != password.value) {
        document.getElementById("password_confirm_check").innerHTML = '<span class="red-text"><i class="material-icons middled">warning</i> Both passwords are not matching</span>';
    } else {
        document.getElementById("password_confirm_check").innerHTML = '<span class="green-text"><i class="material-icons middled">check_circle</i> Both passwords are same</span>';
    }
});
show.addEventListener("change", function() {
    if(password.type === "password" && show.checked === true) {
        password.type = 'text';
        password_confirm.type = 'text';
    } else {
        password.type = 'password';
        password_confirm.type = 'password';
    }
});
reloadBtn.addEventListener("click", function() {
    var img = document.getElementById("captcha_image");
    img.setAttribute('src',"/captcha?rand=" + Math.random());
});