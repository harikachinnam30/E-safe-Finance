function showLogin() {
    document.getElementById("signup").style.display = "none";
    document.getElementById("login").style.display = "block";
    document.getElementById("employee").style.display = "none";
}

function showSignUp() {
    document.getElementById("signup").style.display = "block";
    document.getElementById("login").style.display = "none";
    document.getElementById("employee").style.display = "none";
}

function showEmployee() {
    document.getElementById("signup").style.display = "none";
    document.getElementById("login").style.display = "none";
    document.getElementById("employee").style.display = "block";
}

window.addEventListener("DOMContentLoaded", function() {
    var hash = window.location.hash;
    if(hash == "#signup") {
        showSignUp();
    } else if(hash=="#employee") {
        showEmployee();
    } else {
        showLogin();
    }
})

window.addEventListener("hashchange", function() {
    var hash = window.location.hash;
    if(hash == "#signup") {
        showSignUp();
    } else if(hash=="#employee") {
        showEmployee();
    } else {
        showLogin();
    }
})