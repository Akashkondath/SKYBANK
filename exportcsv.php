<?php
// Start session and check login status
session_start();
if (!isset($_SESSION['userName'])) {
    header("Location: loginpage.html");
    exit;
}

$accountNumber = $_SESSION['accountNumber'];
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="transactions.csv"');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'createnewaccount');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Fetch transactions
$query = "SELECT * FROM transactions WHERE senderAccount = ? OR receiverAccount = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $accountNumber, $accountNumber);
$stmt->execute();
$result = $stmt->get_result();

// Output CSV headers
$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Sender Account', 'Receiver Account', 'Amount', 'Date', 'Description']);

// Output transaction rows
while ($row = $result->fetch_assoc()) {
    $description = ($row['senderAccount'] == $accountNumber) ? 'Debit' : 'Credit';
    fputcsv($output, [$row['id'], $row['senderAccount'], $row['receiverAccount'], $row['amount'], $row['date'], $description]);
}

// Close resources
fclose($output);
$stmt->close();
$conn->close();
exit;
?>
