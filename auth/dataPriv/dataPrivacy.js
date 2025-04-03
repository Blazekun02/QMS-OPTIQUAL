
document.getElementById("backButton").addEventListener("click", function () {
    window.location.href = "../../auth/landing/landingPage.html";
});
  
document.getElementById("submitButton").addEventListener("click", function () {
    const agreeCheckbox = document.getElementById("agree");
    const messageBox = document.getElementById("messageBox");

    if (agreeCheckbox.checked) {
        window.location.href = "../../auth/sign-up/signup.php";
    } else {
        messageBox.style.display = "block";

        setTimeout(() => { messageBox.style.display = "none";
        }, 2000);
    }
});
  
document.getElementById("messageBox").addEventListener("click", function () {
    this.style.display = "none";
});