$(document).ready(function() {

    // Eye Button Logic
    $("#togglePassword").on('click', function() {
        // Toggle the icon class
        $(this).toggleClass("fa-eye fa-eye-slash");

        // Toggle input type
        var input = $("#password");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    // Back Button Logic
    $("#backButton").on('click', function() {
        console.log("Back button clicked"); // Check your console (F12) to see if this triggers
        window.location.href = "/QMS-OPTIQUAL/auth/landing/landingPage.php";
    });

});
 