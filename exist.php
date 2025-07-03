<?php
// Database connection
$servername = "localhost"; // Database server
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "money_lend"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // SQL query to check if the user exists
    $check_email_sql = "SELECT * FROM applications WHERE email = ?";
    $stmt_check_email = $conn->prepare($check_email_sql);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $result_check_email = $stmt_check_email->get_result();

    if ($result_check_email->num_rows > 0) {
       
        header("Location: register.php");
        exit();
    }
    

    // Close connection
    $stmt->close();
    $conn->close();
}
?>


