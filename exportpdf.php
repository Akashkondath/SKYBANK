<?php
require('fpdf/fpdf.php'); // Ensure this path is correct

// Start session to access user details
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userName'])) {
    header("Location: loginpage.html");
    exit;
}

// Get the logged-in user's account number
$accountNumber = $_SESSION['accountNumber'];

// Create instance of FPDF class
$pdf = new FPDF();
$pdf->AddPage();

// Set font to Arial
$pdf->SetFont('Arial', '', 12);

// Add Title
$pdf->Cell(0, 10, 'Transaction Report', 0, 1, 'C');

// Add table headers
$pdf->SetFont('Arial', 'B', 12);

// Define column widths
$columnWidths = [40, 40, 30, 50, 0]; // Adjust widths as needed
$pdf->Cell($columnWidths[0], 10, 'Sender Account', 1);
$pdf->Cell($columnWidths[1], 10, 'Receiver Account', 1);
$pdf->Cell($columnWidths[2], 10, 'Amount (INR)', 1, 0, 'R');
$pdf->Cell($columnWidths[3], 10, 'Date', 1);
$pdf->Cell($columnWidths[4], 10, 'TXN Type', 1, 1); // No width for last column, it will take the remaining space

// Database connection
$conn = new mysqli('localhost', 'root', '', 'createnewaccount');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Query to get transactions for the logged-in user
$query = "SELECT senderAccount, receiverAccount, amount, date, transactionType FROM transactions WHERE senderAccount = ? OR receiverAccount = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $accountNumber, $accountNumber);
$stmt->execute();
$result = $stmt->get_result();

// Set font for row content
$pdf->SetFont('Arial', '', 12);

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell($columnWidths[0], 10, $row['senderAccount'], 1);
        $pdf->Cell($columnWidths[1], 10, $row['receiverAccount'], 1);
        $pdf->Cell($columnWidths[2], 10, number_format($row['amount'], 2) . '/- INR', 1, 0, 'R');
        $pdf->Cell($columnWidths[3], 10, $row['date'], 1);
        // Add a MultiCell for longer descriptions and avoid extra space
        $pdf->MultiCell($columnWidths[4], 10, $row['transactionType'], 1);
        $pdf->Ln(); // Move to the next line after the description
    }
} else {
    $pdf->Cell(array_sum($columnWidths), 10, 'No transactions found', 1, 1, 'C');
}

// Close connection
$conn->close();

// Generate a unique filename with a timestamp
$filename = 'transactions_report_' . date('Ymd_His') . '.pdf';

// Output PDF with the unique filename
$pdf->Output('D', $filename); // Download the file
?>
