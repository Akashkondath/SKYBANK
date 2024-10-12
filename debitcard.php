<?php
session_start();  // Start session

// Check if the user is logged in
if (!isset($_SESSION['userName'])) {
    header("Location: loginpage.html");
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'createnewaccount');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Initialize variables
$message = '';
$showForm = true;
$accountNumber = '';
$phoneNumber = '';
$fullName = '';
$cardNumber = ''; // Initialize card number

// Fetch account number, full name, and mobile number from the database
if (isset($_SESSION['userName'])) {
    $userName = $_SESSION['userName'];
    $query = "SELECT accountNumber, mobileNumber, CONCAT(firstName, ' ', lastName) AS fullName FROM newaccount WHERE userName = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userName);
    $stmt->execute();
    $stmt->bind_result($accountNumber, $phoneNumber, $fullName);
    $stmt->fetch();
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['requestType'])) {
        $requestType = $_POST['requestType'];
        $cardType = $_POST['cardType'];
        $email = $_POST['email'];
        $cardNumber = $_POST['cardNumber'] ?? ''; // Get card number from POST data
        $accountNumber = $_SESSION['accountNumber']; // Get the account number from the session

        // Check if there is already a request with the same details
        $checkQuery = "SELECT * FROM card_requests WHERE account_number = ? AND card_type = ? AND request_type = ? AND full_name = ? AND email = ? AND phone_number = ? AND card_number = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("sssssss", $accountNumber, $cardType, $requestType, $fullName, $email, $phoneNumber, $cardNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If a request already exists with the same details
            $message = "Your request has already been submitted. Please feel free to contact us if any further assistance is needed.";
            $showForm = false; // Hide the form
        } else {
            // Insert new request if no previous request exists with the same details
            $query = "INSERT INTO card_requests (full_name, account_number, card_type, email, phone_number, request_type, card_number) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssss", $fullName, $accountNumber, $cardType, $email, $phoneNumber, $requestType, $cardNumber);
            
            if ($stmt->execute()) {
                $message = "Your request has been submitted. You may receive a confirmation call from our team within 3 working days. For any other concerns, feel free to contact us from the contact section.";
                $showForm = false; // Hide the form after submission
            } else {
                $message = "There was an error submitting your request. Please try again.";
            }
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <title>Debit Card Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f7f9fc;
            font-family: 'Arial', sans-serif;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 50px auto;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            width: 100%;
        }
        .message {
            text-align: center;
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            animation: fadeIn 1s ease-in-out;
            margin: 25px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($showForm): ?>
            <!-- Step 1: Request Type -->
            <div class="form-container" id="requestTypeForm">
                <h2>Request New/Replace Debit Card</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="requestType" class="form-label">Request Type</label>
                        <select id="requestType" name="requestType" class="form-select" required>
                            <option value="">Select Request Type</option>
                            <option value="New">New Debit Card</option>
                            <option value="Replace">Renew Existing Debit Card</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="showDetailsForm()">Next</button>
                </form>
            </div>

            <!-- Step 2: Details Form (hidden by default) -->
            <div class="form-container" id="detailsForm" style="display:none;">
                <h2>Card Details</h2>
                <form method="POST" action="">
                    <!-- Hidden input to store the selected request type -->
                    <input type="hidden" name="requestType" id="hiddenRequestType">
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <!-- Pre-fill and make the full name read-only -->
                        <input type="text" id="fullName" name="fullName" class="form-control" value="<?php echo $fullName; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="cardType" class="form-label">Card Type</label>
                        <select id="cardType" name="cardType" class="form-select" required>
                            <option value="">Select Card Type</option>
                            <option value="Visa">Visa</option>
                            <option value="MasterCard">MasterCard</option>
                            <option value="American Express">American Express</option>
                            <option value="RuPay">RuPay</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3" id="cardNumberContainer" style="display: none;">
                        <label for="cardNumber" class="form-label">Card Number</label>
                        <input type="text" id="cardNumber" name="cardNumber" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">Phone Number</label>
                        <!-- Pre-fill and make the phone number read-only -->
                        <input type="tel" id="phoneNumber" name="phoneNumber" class="form-control" value="<?php echo $phoneNumber; ?>" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Success/Error Message -->
            <div class="message">
                <?php echo $message; ?>
            </div>

            <script>
                // Automatically redirect to dashboard.php after 10 seconds
                setTimeout(function() {
                    window.location.href = 'dashboard.php';
                }, 10000); // 10000ms = 10 seconds
            </script>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showDetailsForm() {
            var requestType = document.getElementById('requestType').value;
            if (requestType) {
                document.getElementById('hiddenRequestType').value = requestType;
                document.getElementById('requestTypeForm').style.display = 'none';
                document.getElementById('detailsForm').style.display = 'block';

                // Show card number input if "Replace" is selected
                if (requestType === 'Replace') {
                    document.getElementById('cardNumberContainer').style.display = 'block';
                } else {
                    document.getElementById('cardNumberContainer').style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
