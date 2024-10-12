<?php
session_start(); // Start the session

// Retrieve sender account number from the session
$senderAccountNumber = $_SESSION['accountNumber'];

// Retrieve form data from GET parameters
$senderPassword = isset($_GET['senderPassword']) ? $_GET['senderPassword'] : '';
$receiverAccountNumber = isset($_GET['receiverAccountNumber']) ? $_GET['receiverAccountNumber'] : '';
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;

// Database connection
$conn = new mysqli('localhost', 'root', '', 'createnewaccount');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Check if sender's credentials are correct
$stmt = $conn->prepare("SELECT balance FROM newaccount WHERE accountNumber = ? AND password = ?");
$stmt->bind_param("ss", $senderAccountNumber, $senderPassword);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $currentBalance = $user['balance'];

    // Check if receiver account exists
    $stmt = $conn->prepare("SELECT accountNumber FROM newaccount WHERE accountNumber = ?");
    $stmt->bind_param("s", $receiverAccountNumber);
    $stmt->execute();
    $receiverResult = $stmt->get_result();

    if ($receiverResult->num_rows > 0) {
        // If the receiver account exists, proceed with the transaction
        if ($amount > $currentBalance) {
            echo "<script>alert('Sorry, insufficient funds.'); window.location.href = 'fund_transfer.php';</script>";
        } else {
            // Proceed with the transaction
            $conn->begin_transaction();

            try {
                // Deduct amount from sender's account
                $stmt = $conn->prepare("UPDATE newaccount SET balance = balance - ? WHERE accountNumber = ?");
                $stmt->bind_param("ds", $amount, $senderAccountNumber);
                $stmt->execute();

                // Add amount to receiver's account
                $stmt = $conn->prepare("UPDATE newaccount SET balance = balance + ? WHERE accountNumber = ?");
                $stmt->bind_param("ds", $amount, $receiverAccountNumber);
                $stmt->execute();

                // Record the transaction
                $stmt = $conn->prepare("INSERT INTO transactions (senderAccount, receiverAccount, amount, date, description) VALUES (?, ?, ?, NOW(), 'Fund Transfer')");
                $stmt->bind_param("ssd", $senderAccountNumber, $receiverAccountNumber, $amount);
                $stmt->execute();

                // Commit transaction
                $conn->commit();

                echo "<script>alert('Transaction successful.'); window.location.href = 'dashboard.php';</script>";
            } catch (Exception $e) {
                $conn->rollback();
                echo "<script>alert('Transaction failed. Please try again.'); window.location.href = 'fund_transfer.php';</script>";
            }
        }
    } else {
        // If the receiver account does not exist
        echo "<script>alert('Receiver account does not exist.'); window.location.href = 'fund_transfer.php';</script>";
    }
} else {
    echo "<script>alert('Password mismatched. Please check your details and try again.'); window.location.href = 'fund_transfer.php';</script>";
}

$stmt->close();
$conn->close();
?>
