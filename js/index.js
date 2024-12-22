const navbar = document.querySelector('nav');
fetch("components/header.php")
.then(res => res.text())
.then(data =>{
    navbar.innerHTML = data;
})


//Attaching footer to every page
const footer = document.querySelector('.custom-footer');
fetch("components/footer.html")
.then(res => res.text())
.then(data =>{
    footer.innerHTML = data;
})

// Password validation function
function validatePassword() {
    const password = document.getElementById('password').value;
    const passwordError = document.getElementById('password-error');
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/;

    if (!regex.test(password)) {
        passwordError.textContent = "Password must contain at least 1 capital letter, 1 small letter, 1 symbol, and 1 number.";
        return false;
    } else {
        passwordError.textContent = "";
        return true;
    }
}

// Show/hide password functionality
function togglePasswordVisibility() {
    const passwordField = document.getElementById('password');
    const toggleButton = document.getElementById('togglePassword')
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleButton.textContent = "Hide password";
    } else {
        passwordField.type = "password";
        toggleButton.textContent = "Show password";
    }
}

// Attach the password validation function to the form submit event
document.querySelector('form').addEventListener('submit', function (e) {
    if (!validatePassword()) {
        e.preventDefault(); // Prevent form submission if validation fails
    }
});
