// Show Password Functionality
document.getElementById("showPassword").addEventListener("change", function () {
    const passwordField = document.getElementById("password");
    const confirmPasswordField = document.getElementById("confirmPassword");

    // Toggle the type attribute between 'password' and 'text'
    const type = this.checked ? "text" : "password";
    passwordField.type = type;
    confirmPasswordField.type = type;
});

const emailInput = document.getElementById("email");
const emailReqBox = document.getElementById("emailRequirements");

// Show the requirements box when the email field is focused
emailInput.addEventListener("focus", () => {
    emailReqBox.style.display = "block";
});

// Hide the requirements box when the email field is blurred
emailInput.addEventListener("blur", () => {
    emailReqBox.style.display = "none";
});

const passwordInput = document.getElementById("password");
const passReqBox = document.getElementById("passwordRequirements");

const lengthRequirement = document.getElementById("length");
const uppercaseRequirement = document.getElementById("uppercase");
const lowercaseRequirement = document.getElementById("lowercase");
const numberRequirement = document.getElementById("number");
const specialRequirement = document.getElementById("special");

// Show the requirements box when the password field is focused
passwordInput.addEventListener("focus", () => {
    passReqBox.style.display = "block";
});

// Hide the requirements box when the password field is blurred
passwordInput.addEventListener("blur", () => {
    passReqBox.style.display = "none";
});

// Validate the password as the user types
passwordInput.addEventListener("input", () => {
    const password = passwordInput.value;

    // Check length
    if (password.length >= 12) {
        lengthRequirement.classList.add("valid");
        lengthRequirement.classList.remove("invalid");
    } else {
        lengthRequirement.classList.add("invalid");
        lengthRequirement.classList.remove("valid");
    }

    // Check for uppercase letter
    if (/[A-Z]/.test(password)) {
        uppercaseRequirement.classList.add("valid");
        uppercaseRequirement.classList.remove("invalid");
    } else {
        uppercaseRequirement.classList.add("invalid");
        uppercaseRequirement.classList.remove("valid");
    }

    // Check for lowercase letter
    if (/[a-z]/.test(password)) {
        lowercaseRequirement.classList.add("valid");
        lowercaseRequirement.classList.remove("invalid");
    } else {
        lowercaseRequirement.classList.add("invalid");
        lowercaseRequirement.classList.remove("valid");
    }

    // Check for number
    if (/[0-9]/.test(password)) {
        numberRequirement.classList.add("valid");
        numberRequirement.classList.remove("invalid");
    } else {
        numberRequirement.classList.add("invalid");
        numberRequirement.classList.remove("valid");
    }

    // Check for special character
    if (/[\W_]/.test(password)) {
        specialRequirement.classList.add("valid");
        specialRequirement.classList.remove("invalid");
    } else {
        specialRequirement.classList.add("invalid");
        specialRequirement.classList.remove("valid");
    }
});
