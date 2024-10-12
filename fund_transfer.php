<?php
session_start();  // Start the session

// Check if the user is logged in
if (!isset($_SESSION['userName'])) {
    header("Location: loginpage.html");
    exit();
}

// Retrieve the sender's account number from the session
$senderAccountNumber = $_SESSION['accountNumber'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $senderPassword = $_POST['senderPassword'];
    $receiverAccountNumber = $_POST['receiverAccountNumber'];
    $amount = $_POST['amount'];

    // Redirect to process_transfer.php with form data
    header("Location: process_transfer.php?senderPassword=" . urlencode($senderPassword) . "&receiverAccountNumber=" . urlencode($receiverAccountNumber) . "&amount=" . urlencode($amount));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <title>Fund Transfer</title>
    <link rel="stylesheet" href="fund_transfer.css"> <!-- Adjust the path as needed -->
</head>
<body>
    <div class="container">
        <h2>Fund Transfer</h2>
        <form action="fund_transfer.php" method="post">
            <div class="form-group">
                <label for="senderAccountNumber">Sender Account Number:</label>
                <input type="text" id="senderAccountNumber" name="senderAccountNumber" style="color:green"; value="<?php echo htmlspecialchars($senderAccountNumber); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="senderPassword">Sender Password:</label>
                <input type="password" id="senderPassword" name="senderPassword" required>
            </div>
            <div class="form-group">
                <label for="receiverAccountNumber">Receiver Account Number:</label>
                <input type="text" id="receiverAccountNumber" name="receiverAccountNumber" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" step="0.01" required>
            </div>
            <button type="submit">Transfer</button>
        </form>
    </div>
</body>
</html>
