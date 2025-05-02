// Show Password Functionality
document.getElementById("showPassword").addEventListener("change", function () {
    const passwordField = document.getElementById("password");

    // Toggle the type attribute between 'password' and 'text'
    const type = this.checked ? "text" : "password";
    passwordField.type = type;
});

document.getElementById("backButton").addEventListener("click", function () {
    window.location.href = "/qms_optiqual/auth/landing/landingPage.php";
});

