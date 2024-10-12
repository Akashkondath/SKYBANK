document.querySelector("form").addEventListener("submit", function(event) {
    // Get form values
    let firstName = document.querySelector("[name='firstName']").value;
    let lastName = document.querySelector("[name='lastName']").value;
    let password = document.querySelector("[name='password']").value;
    let confirmPassword = document.querySelector("[name='confirmPassword']").value;
    let termsAccepted = document.querySelector("[name='terms']").checked;

    // Initialize error message
    let errorMessage = "";

    // Name validation using regex
    let nameRegex = /^[a-zA-Z]+$/;
    if (!nameRegex.test(firstName) || !nameRegex.test(lastName)) {
        errorMessage += "Names should contain only letters.\n";
    }

    // Password matching validation
    if (password !== confirmPassword) {
        errorMessage += "Passwords do not match.\n";
    }

    // Check if terms and conditions checkbox is checked
    if (!termsAccepted) {
        errorMessage += "You must agree to the terms and conditions.\n";
    }

    // Display errors and prevent form submission if there are errors
    if (errorMessage) {
        alert(errorMessage);
        event.preventDefault(); // Prevent form submission
        return;
    }
});