<?php
session_start();  // Start session

// Check if the user is logged in
if (!isset($_SESSION['userName'])) {
    header("Location: loginpage.html");
    exit;
}

$accountNumber = $_SESSION['accountNumber'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'createnewaccount');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Query to get transactions where the user is the sender or receiver
$query = "SELECT * FROM transactions WHERE senderAccount = ? OR receiverAccount = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $accountNumber, $accountNumber);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data for the pie chart
$transactionData = [];
while ($row = $result->fetch_assoc()) {
    $description = ($row['senderAccount'] == $accountNumber) ? 'Debit' : 'Credit';
    if (!isset($transactionData[$description])) {
        $transactionData[$description] = 0;
    }
    $transactionData[$description] += $row['amount'];
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <title>My Transactions</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Transaction Type', 'Amount'],
                <?php
                foreach ($transactionData as $type => $amount) {
                    echo "['" . htmlspecialchars($type) . "', " . $amount . "],";
                }
                ?>
            ]);

            var options = {
                title: 'Transaction Distribution',
                pieHole: 0.4
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <!-- Export button -->
        <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle mb-3" data-bs-toggle="dropdown" aria-expanded="false">
                Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="exportcsv.php">Export as CSV File</a></li>
                <li><a class="dropdown-item" href="exportpdf.php">Export as PDF File</a></li>
            </ul>
        </div>

        <h2>Transaction History</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sender Account</th>
                    <th>Receiver Account</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>TXN Type</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Fetch and display transactions
                    $result->data_seek(0); // Reset the result pointer
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $description = ($row['senderAccount'] == $accountNumber) ? 'Debit' : 'Credit';
                            
                            // Set color based on the transaction type
                            $color = ($description == 'Debit') ? 'red' : 'green';

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['senderAccount']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['receiverAccount']) . "</td>";
                            echo "<td>₹" . htmlspecialchars($row['amount']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            // Apply color based on the transaction type
                            echo "<td style='color: " . $color . "; font-weight: bold;'>" . htmlspecialchars($description) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No transactions found</td></tr>";
                    }
                ?>
            </tbody>

        </table>

        <!-- Pie Chart -->
        <div id="piechart" style="width: 100%; height: 500px;"></div>

        <!-- Back button -->
        <button class="btn btn-primary m-4" id="transactionsbackbutton">Back</button>
    </div>

    <script>
        let transactionsbackbutton = document.querySelector("#transactionsbackbutton");
        transactionsbackbutton.addEventListener("click", () => {
            window.location.href = "dashboard.php";
        });
    </script>
</body>
</html>
