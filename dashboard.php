<?php
session_start();  // Start session to access user details

// Check if the user is logged in by verifying session variables
if (!isset($_SESSION['userName'])) {
    // If the session variable is not set, redirect to login page
    header("Location: loginpage.html");
    exit;
}

// Retrieve user details from session variables
$firstName = $_SESSION['firstName'];
$lastName = $_SESSION['lastName'];
$userName = $_SESSION['userName'];
$mobileNumber = $_SESSION['mobileNumber'];
$accountNumber = $_SESSION['accountNumber'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'createnewaccount');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Query to get user balance
$query = "SELECT balance FROM newaccount WHERE accountNumber = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $accountNumber);
$stmt->execute();
$result = $stmt->get_result();

// Fetch balance from query result
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $balance = $row['balance'];
} else {
    $balance = 'Not available'; // In case no balance is found
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="myaccount.css">
    <link rel="shortcut icon" type="x-icon" href="logo.png">
    <title>Account Details</title>
    <style>
        .user-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .user-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .user-info {
            color: #495057;
        }
        .user-info span {
            color: #007bff;
            font-weight: bold;
        }
        .section-header {
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .side-panel {
            background-color: #232740;
            color: white;
            min-height: 100vh;
            padding: 20px;
        }
        .side-panel a {
            color: rgb(236, 236, 236);
        }
        .allrights {
            margin: 0;
            font-size: 0.875rem;
        }
        .dashboard {
            display: flex;
        }
        /* Inline hover effect using internal CSS */
        a:hover {
            background-color:black; /* Change the background color */
            color:rgb(236, 236, 236); /* Change the text color */
        }
    
    </style>
</head>
<body>
    <div class="dashboard" >
        <!-- Side Panel -->
        <section class="side-panel">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-row">
                            <a href="index.html" class="tittle" style="background-color:transparent; margin-left:-25px;">SKYBANK</a>
                            <div class="d-flex flex-column justify-content-center mt-3 d-none d-md-block">
                                <img style="height:50px; width:50px; padding:5px;" src="logo.png" alt="Logo">
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <h3 style="color:#555a82; padding:5px; margin-top:50px;">ACCOUNTS</h3>
                            <a href="dashboard.php" class="active"  style="text-decoration: none; color:rgb(236, 236, 236); padding:5px; margin-top:5px;background-color:black; opacity:0.2px;">My account</a>
                            <h3 style="color:#555a82; padding:5px; margin-top:50px;">TRANSACTIONS</h3>
                            <a href="transactions.php" style="text-decoration: none; color:rgb(236, 236, 236); padding:5px; margin-top:5px;">My Transactions</a>
                            <h3 style="color:#555a82; padding:5px; margin-top:50px;">SERVICES</h3>
                            <div class="d-flex flex-column">
                                <a href="fund_transfer.php" style="text-decoration: none; color:rgb(236, 236, 236); padding:5px; margin-top:10px; margin-bottom:5px;">Fund Transfer</a><br>
                                <a href="debitcard.php" style="text-decoration: none; color:rgb(236, 236, 236); padding:5px; margin-top:10px; margin-bottom:5px;">New/Replace Debit Card</a><br>
                                <a href="creditcard.php" style="text-decoration: none; color:rgb(236, 236, 236); padding:5px; margin-top:10px; margin-bottom:5px;">Apply Credit Card</a><br>
                                <a href="customerservice.html" style="text-decoration: none; color:rgb(236, 236, 236); padding:5px; margin-top:10px; margin-bottom:5px;">Customer Service</a>
                            </div>
                            <div class="d-flex flex-row">
                                <i style="color:white;" class="bi bi-c-circle"></i>
                                <p class="allrights">ALL RIGHTS RESERVED.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="main-content mt-5 flex-fill " >
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="user-card text-center ">
                            <!-- User image (Placeholder) -->
                            <img class="user-image mb-3" src="user.jpg" alt="User Image">
                            <div class="user-content">
                                <h2 class="section-header">Account Details</h2>
                                <!-- Display user details -->
                                <div class="user-info mb-3">
                                    <h4><span>Account Holder:</span> <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></h4>
                                    <h5><span>Username:</span> <?php echo htmlspecialchars($userName); ?></h5>
                                    <h5><span>Mobile Number:</span> <?php echo htmlspecialchars($mobileNumber); ?></h5>
                                    <h5><span>Account Number:</span> <?php echo htmlspecialchars($accountNumber); ?></h5>
                                    <h5><span>Current Balance:</span> ₹<?php echo number_format($balance, 2); ?></h5>
                                </div>
                                <!-- Action Buttons -->
                                 
                            </div>
                                                        <!-- Logout button in your HTML -->
                            <form action="logout.php" method="post" style="display: inline;">
                                <button type="submit" class="btn btn-primary" id="dashboardlogoutbutton">Logout</button>
                            </form>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </section>
        
    </div>
    
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
