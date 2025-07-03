<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "money_lend";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error_message = "";

// Check if it's a login action
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user_ip = $_POST['user_ip'] ?? '';

    // Validate inputs
    if (empty($email)) {
        $error_message = "Email is required.";
    } elseif (empty($password)) {
        $error_message = "Password is required.";
    } else {
        // Get IP location data
        $city = $region = $country = 'Unknown';
        if (!empty($user_ip)) {
            $api_url = "https://ipinfo.io/$user_ip/json";
            $response = @file_get_contents($api_url);
            if ($response !== false) {
                $location_data = json_decode($response, true);
                $city = $location_data['city'] ?? 'Unknown';
                $region = $location_data['region'] ?? 'Unknown';
                $country = $location_data['country'] ?? 'Unknown';
            }
        }

        // Validate login credentials
        $sql = "SELECT * FROM applications WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['user_email'] = $user['email'];
                $login_status = 'successful';
                
                // Log the successful login
                $sql_log = "INSERT INTO user_logins (email, ip_address, city, region, country, login_status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_log = $conn->prepare($sql_log);
                $stmt_log->bind_param("ssssss", $email, $user_ip, $city, $region, $country, $login_status);
                $stmt_log->execute();
                $stmt_log->close();
                
                header("Location: log_user2.php");
                exit();
            } else {
                $login_status = 'failed';
                $error_message = "Invalid credentials!";
            }
        } else {
            $login_status = 'failed';
            $error_message = "Invalid credentials!";
        }

        // Log the login attempt
        $sql_log = "INSERT INTO user_logins (email, ip_address, city, region, country, login_status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_log = $conn->prepare($sql_log);
        $stmt_log->bind_param("ssssss", $email, $user_ip, $city, $region, $country, $login_status);
        $stmt_log->execute();
        $stmt_log->close();
        
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
    <title>Money Lend - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="left">
            <h1>Money Lend</h1>
            <ul>
                <li>Success Loans & Credit in Minutes</li>
                <li><img src="images/check.png" alt="Check" class="check-icon"> Loans up to ₹2,00,000 in 2 minutes</li>
                <li><img src="images/check.png" alt="Check" class="check-icon"> Available to use across India</li>
            </ul>
        </div>
        <div class="right">
            <h2>Welcome back!</h2>
            <h3>Please login to access your account.</h3>

            <!-- Login Form -->
            <form action="2login.php" method="POST">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <!-- Hidden input for IP address -->
                <input type="hidden" name="user_ip" id="user_ip" value="">

                <button type="submit">Login</button>
            </form>

            <!-- Display error message if credentials are wrong -->
            <?php if (!empty($error_message)): ?>
                <p style='color:red; text-align:center;'><?= $error_message ?></p>
            <?php endif; ?>

            <p>Don't have an account? <a href="log_user1.php" id="create-account-link">Create</a></p>
        </div>
    </div>

    <script>
        // Get the user's IP address using JavaScript
        fetch('https://api.ipify.org?format=json')
            .then(response => response.json())
            .then(data => {
                document.getElementById('user_ip').value = data.ip;
                document.getElementById('create-account-link').href = `log_user1.php?user_ip=${data.ip}`;
            })
            .catch(error => {
                console.error("Error fetching IP:", error);
            });
    </script>
</body>
</html>