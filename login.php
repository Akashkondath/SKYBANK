<?php
session_start();  // Start the session to store user data

// Retrieve form data
$userName = $_POST['userName'];
$password = $_POST['password'];
$recaptchaResponse = $_POST['g-recaptcha-response']; // Get CAPTCHA response from form

// Initialize an error message variable
$error = '';

// Verify CAPTCHA
$secretKey = '6LfDe14qAAAAAFATDCt5pYfQs9x-f5xsXJDT9TNQ'; // Replace with your reCAPTCHA secret key
$captchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
$response = file_get_contents($captchaVerifyUrl . '?secret=' . $secretKey . '&response=' . $recaptchaResponse);
$responseKeys = json_decode($response, true);

if (!$responseKeys['success']) {
    $error .= "Captcha verification failed.";
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'createnewaccount');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Prepare and execute the SQL statement
$stmt = $conn->prepare("SELECT * FROM newaccount WHERE userName = ? AND password = ?");
$stmt->bind_param("ss", $userName, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0 && empty($error)) {
    // Fetch user data from the result
    $user = $result->fetch_assoc();
    
    // Store user information in session variables
    $_SESSION['firstName'] = $user['firstName'];
    $_SESSION['lastName'] = $user['lastName'];
    $_SESSION['userName'] = $user['userName'];
    $_SESSION['mobileNumber'] = $user['mobileNumber'];
    $_SESSION['accountNumber'] = $user['accountNumber'];

    // Redirect to dashboard.php
    header("Location: dashboard.php");
    exit();
} else {
    // Credentials do not match or CAPTCHA failed, display an error message
    if (empty($error)) {
        $error = "Username or password is mismatched.";
    }
    echo "<script>alert('$error'); window.location.href = 'loginpage.html';</script>";
    exit();
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
