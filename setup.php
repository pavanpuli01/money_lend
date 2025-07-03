<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS money_lend";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db("money_lend");

// Create applications table
$sql = "CREATE TABLE IF NOT EXISTS applications (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dob DATE NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    pan_number VARCHAR(20) NOT NULL,
    pan_front VARCHAR(255) NOT NULL,
    pan_back VARCHAR(255) NOT NULL,
    profile_photo VARCHAR(255) NOT NULL,
    bank_statement VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "Table 'applications' created successfully or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create user_logins table
$sql = "CREATE TABLE IF NOT EXISTS user_logins (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100),
    ip_address VARCHAR(45) NOT NULL,
    latitude VARCHAR(20),
    longitude VARCHAR(20),
    city VARCHAR(50),
    region VARCHAR(50),
    country VARCHAR(50),
    user_agent TEXT,
    login_status ENUM('successful', 'failed') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "Table 'user_logins' created successfully or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create uploads directory if it doesn't exist
$upload_dir = "uploads/";
if (!file_exists($upload_dir)) {
    if (mkdir($upload_dir, 0777, true)) {
        echo "Uploads directory created successfully<br>";
    } else {
        echo "Failed to create uploads directory<br>";
    }
} else {
    echo "Uploads directory already exists<br>";
}

$conn->close();
echo "Setup completed successfully!";
?>