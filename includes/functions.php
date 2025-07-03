<?php
require_once 'config.php';

function db_connect() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function handle_file_upload($file_input_name) {
    if (!isset($_FILES[$file_input_name])) {
        return "No file uploaded";
    }

    $file = $_FILES[$file_input_name];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "File upload error: " . $file['error'];
    }

    // Validate file type
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, ALLOWED_TYPES)) {
        return "Invalid file type. Only JPG, JPEG, PNG, PDF are allowed.";
    }

    // Validate file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return "File size exceeds 5MB limit.";
    }

    // Generate unique filename
    $new_file_name = uniqid($file_input_name . "_") . "." . $file_ext;
    $file_path = UPLOAD_DIR . $new_file_name;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return $file_path;
    } else {
        return "Failed to move uploaded file.";
    }
}

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>