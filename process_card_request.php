<?php
session_start();  // Start session

// Check if the user is logged in
if (!isset($_SESSION['userName'])) {
    header("Location: loginpage.html");
    exit;
}

// Get the logged-in user's account number
$accountNumber = $_SESSION['accountNumber'];

// Retrieve form data
$cardType = $_POST['cardType'];
$reason = $_POST['reason'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'createnewaccount');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Prepare and execute SQL statement to insert request into database
$stmt = $conn->prepare("INSERT INTO card_requests (accountNumber, cardType, reason, requestDate) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sss", $accountNumber, $cardType, $reason);

if ($stmt->execute()) {
    // Success message
    echo "<script>alert('Your request for a $cardType card has been submitted successfully.'); window.location.href = 'dashboard.php';</script>";
} else {
    // Error message
    echo "<script>alert('Failed to submit your request. Please try again.'); window.location.href = 'new_replace_card.php';</script>";
}

// Close connection
$stmt->close();
$conn->close();
?>
