const loginButton = document.getElementById("login_button_desktop");
const loginForm = document.querySelector(".login-form");
const retourButton = document.querySelector(".closebutton_modal");

loginButton.addEventListener("click", displayLoginForm)
retourButton.addEventListener("click", closeLoginForm)

function displayLoginForm () {
    loginForm.style.display = "block";
}

function closeLoginForm () {
    loginForm.style.display = "none";
}