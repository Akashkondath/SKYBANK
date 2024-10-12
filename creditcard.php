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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cardType'])) {
        $fullName = $_POST['fullName'];
        $cardType = $_POST['cardType'];
        $email = $_POST['email'];
        $phoneNumber = $_POST['phoneNumber'];
        $address = $_POST['address'];
        $income = !empty($_POST['income']) ? $_POST['income'] : null;
        $accountNumber = $_SESSION['accountNumber']; // Get account number from session

        // Check if there is already an application for the same credit card
        $checkQuery = "SELECT * FROM credit_card_requests WHERE account_number = ? AND card_type = ? AND full_name = ? AND email = ? AND phone_number = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("sssss", $accountNumber, $cardType, $fullName, $email, $phoneNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If a request already exists
            $message = "Your credit card application has already been submitted. Please contact us if you need further assistance.";
            $showForm = false; // Hide the form
        } else {
            // Insert new application
            $query = "INSERT INTO credit_card_requests (full_name, account_number, card_type, email, phone_number, address, income) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssss", $fullName, $accountNumber, $cardType, $email, $phoneNumber, $address, $income);

            if ($stmt->execute()) {
                $message = "Your credit card application has been submitted. You may receive a confirmation call within 3 working days.";
                $showForm = false; // Hide form after submission
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
    <title>Apply for Credit Card</title>
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
            max-width: 600px;
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
            <div class="form-container">
                <h2>Apply for Credit Card</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" id="fullName" name="fullName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="cardType" class="form-label">Credit Card Type</label>
                        <select id="cardType" name="cardType" class="form-select" required>
                            <option value="">Select Card Type</option>
                            <option value="Visa">Visa</option>
                            <option value="MasterCard">MasterCard</option>
                            <option value="American Express">American Express</option>
                            <option value="Discover">Discover</option>
                            <option value="Rupay">RuPay</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">Phone Number</label>
                        <input type="tel" id="phoneNumber" name="phoneNumber" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea id="address" name="address" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="income" class="form-label">Annual Income (Optional)</label>
                        <input type="number" id="income" name="income" class="form-control" placeholder="e.g. 500000" step="0.01">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Success/Error Message -->
            <div class="message">
                <?php echo $message; ?>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = "dashboard.php";
                }, 10000); // Redirects after 10 seconds
            </script>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
