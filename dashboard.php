<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header('Location: 2login.php');
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'money_lend';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the user's details
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE email = :email");
    $stmt->bindParam(':email', $_SESSION['user_email']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }

    // Extract user details
    $first_name = htmlspecialchars($user['first_name']);
    $last_name = htmlspecialchars($user['last_name']);
    $address = htmlspecialchars($user['address']);
    $phone = htmlspecialchars($user['phone']);
    $profile_photo = htmlspecialchars($user['profile_photo']);
    $email = htmlspecialchars($_SESSION['user_email']);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Lend - Dashboard</title>
    <link rel="stylesheet" href="dash.css">
</head>
<body>
    <div class="top-container">
        <div class="logo"><h3>Money Lend</h3></div>
    </div>

    <div class="down-container">
        <div class="left-section">
            <div class="dashboard"><h1>Dashboard</h1></div>

            <div class="profile-section">
                <img class="profile-photo" src="<?= $profile_photo ?>" alt="Profile Photo">
                <h2 class="full-name"><?= $first_name . ' ' . $last_name ?></h2>
                <p class="email"><?= $email ?></p>
            </div>

            <div class="personal-info">
                <p><strong>Phone:</strong> <?= $phone ?></p>
                <p><strong>Address:</strong> <?= $address ?></p>
            </div>
            
            <form action="logout.php" method="POST" class="logout-form">
                <button type="submit" class="logout-btn">
                    Logout <img src="images/exit.png" alt="Logout Icon" class="logout-icon">
                </button>
            </form>
        </div>

        <div class="right-section">
            <div class="loan-status">
                <h2>We're processing your loan!</h2>
                <p>Your application is being reviewed by our team. We are excited to help you with your financial goals!</p>
                <p>Rest assured, you're on the right track! We'll notify you once the final decision is made.</p>
                <p><strong>Thank you for choosing us!</strong></p>
            </div>
        </div>
    </div>
</body>
</html>