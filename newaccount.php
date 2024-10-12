<?php
// Retrieve form data
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$accountNumber = $_POST['accountNumber'];
$userName = $_POST['userName'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];
$mobileNumber = $_POST['mobileNumber'];
$termsAccepted = isset($_POST['terms']); // Checkbox value

// Initialize an error message variable
$error = '';

// Validate that names contain only letters
if (!preg_match("/^[a-zA-Z]+$/", $firstName) || !preg_match("/^[a-zA-Z]+$/", $lastName)) {
    $error .= "Names should contain only letters.<br>";
}

// Validate that password and confirm password match
if ($password !== $confirmPassword) {
    $error .= "Passwords do not match.<br>";
}

// Check if terms and conditions checkbox is checked
if (!$termsAccepted) {
    $error .= "You must agree to the terms and conditions.<br>";
}

// If there are errors, display them and redirect back
if ($error) {
    echo "<script>alert('$error'); window.location.href = 'index.html';</script>";
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'createnewaccount');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
} else {
    $stmt = $conn->prepare("INSERT INTO newaccount (firstName, lastName, accountNumber, userName, password, mobileNumber) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstName, $lastName, $accountNumber, $userName, $password, $mobileNumber);
    
    if ($stmt->execute()) {
        // Registration successful, redirect to loginpage.html
        header("Location: loginpage.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>
