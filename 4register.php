<?php
session_start();
$errors = [];

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "money_lend";
$upload_dir = "uploads/";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure upload directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Function to handle file upload with validation
function handleFileUpload($file_input_name, $upload_dir) {
    $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
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
    if (!in_array($file_ext, $allowed_types)) {
        return "Invalid file type. Only JPG, JPEG, PNG, PDF are allowed.";
    }

    // Validate file size
    if ($file['size'] > $max_size) {
        return "File size exceeds 5MB limit.";
    }

    // Generate unique filename
    $new_file_name = uniqid($file_input_name . "_") . "." . $file_ext;
    $file_path = $upload_dir . $new_file_name;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return $file_path;
    } else {
        return "Failed to move uploaded file.";
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $pan_number = trim($_POST['pan_number'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($first_name)) $errors['first_name'] = "First Name is required.";
    if (empty($last_name)) $errors['last_name'] = "Last Name is required.";
    if (empty($dob)) $errors['dob'] = "Date of Birth is required.";
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (empty($phone)) {
        $errors['phone'] = "Phone number is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors['phone'] = "Invalid phone number format.";
    }
    if (empty($address)) $errors['address'] = "Address is required.";
    if (empty($pincode)) $errors['pincode'] = "Pincode is required.";
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters.";
    }
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT email FROM applications WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors['email'] = "Email already registered. Try logging in.";
    }
    $stmt->close();

    // Process file uploads if no errors
    if (empty($errors)) {
        $pan_front_path = handleFileUpload('pan_front', $upload_dir);
        $pan_back_path = handleFileUpload('pan_back', $upload_dir);
        $profile_photo_path = handleFileUpload('profile_photo', $upload_dir);
        $bank_statement_path = handleFileUpload('bank_statement', $upload_dir);

        // Check for file upload errors
        if (!is_string($pan_front_path)) $errors['pan_front'] = $pan_front_path;
        if (!is_string($pan_back_path)) $errors['pan_back'] = $pan_back_path;
        if (!is_string($profile_photo_path)) $errors['profile_photo'] = $profile_photo_path;
        if (!is_string($bank_statement_path)) $errors['bank_statement'] = $bank_statement_path;
    }

    // If still no errors, insert data
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO applications (first_name, last_name, dob, email, phone, address, pincode, pan_number, pan_front, pan_back, profile_photo, bank_statement, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssss", $first_name, $last_name, $dob, $email, $phone, $address, $pincode, $pan_number, $pan_front_path, $pan_back_path, $profile_photo_path, $bank_statement_path, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['user_email'] = $email;
            header("Location: dashboard.php");
            exit();
        } else {
            $errors['general'] = "Error: " . $stmt->error;
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
    <title>Money Lend - Registration</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="top-container">
        <div class="logo"><h3>Money Lend</h3></div>
    </div>
    <div class="down-container">
        <div class="inner-container">
            <div class="top">
                <h3 class="heading">Complete your application to get approved.</h3>
            </div>
            <div class="bottom">
                <form action="4register.php" method="post" enctype="multipart/form-data">
                    <div class="left">
                        <div class="input-container">
                            <label for="first_name">First Name</label>
                            <span class="error"><?= $errors['first_name'] ?? '' ?></span>
                            <input type="text" name="first_name" value="<?= htmlspecialchars($first_name ?? '') ?>" <?= isset($errors['first_name']) ? 'class="error"' : '' ?>>
                        </div>
                        
                        <div class="input-container">
                            <label for="last_name">Last Name</label>
                            <span class="error"><?= $errors['last_name'] ?? '' ?></span>
                            <input type="text" name="last_name" value="<?= htmlspecialchars($last_name ?? '') ?>" <?= isset($errors['last_name']) ? 'class="error"' : '' ?>>
                        </div>
                        
                        <div class="input-container">
                            <label for="dob">Date of Birth</label>
                            <span class="error"><?= $errors['dob'] ?? '' ?></span>
                            <input type="date" name="dob" value="<?= htmlspecialchars($dob ?? '') ?>" <?= isset($errors['dob']) ? 'class="error"' : '' ?>>
                        </div>
                        
                        <div class="input-container">
                            <label for="email">Email</label>
                            <span class="error"><?= $errors['email'] ?? '' ?></span>
                            <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" <?= isset($errors['email']) ? 'class="error"' : '' ?>>
                        </div>
                        
                        <div class="input-container">
                            <label for="phone">Phone Number (+91)</label>
                            <span class="error"><?= $errors['phone'] ?? '' ?></span>
                            <input type="tel" name="phone" pattern="[0-9]{10}" value="<?= htmlspecialchars($phone ?? '') ?>" <?= isset($errors['phone']) ? 'class="error"' : '' ?>>
                        </div>
                        
                        <div class="input-container">
                            <label for="address">Address</label>
                            <span class="error"><?= $errors['address'] ?? '' ?></span>
                            <textarea name="address" <?= isset($errors['address']) ? 'class="error"' : '' ?>><?= htmlspecialchars($address ?? '') ?></textarea>
                        </div>
                        
                        <div class="input-container">
                            <label for="pincode">Pincode</label>
                            <span class="error"><?= $errors['pincode'] ?? '' ?></span>
                            <input type="text" name="pincode" value="<?= htmlspecialchars($pincode ?? '') ?>" <?= isset($errors['pincode']) ? 'class="error"' : '' ?>>
                        </div>
                    </div>

                    <div class="right">
                        <div class="input-container">
                            <label for="pan_number">PAN Number</label>
                            <span class="error"><?= $errors['pan_number'] ?? '' ?></span>
                            <input type="text" name="pan_number" value="<?= htmlspecialchars($pan_number ?? '') ?>" <?= isset($errors['pan_number']) ? 'class="error"' : '' ?>>
                        </div>
                        
                        <div class="input-container"> 
                            <label for="pan_front">Upload PAN Card Front</label>
                            <span class="error"><?= $errors['pan_front'] ?? '' ?></span>
                            <input type="file" name="pan_front" <?= isset($errors['pan_front']) ? 'class="error"' : '' ?>>
                        </div>
                        
                        <div class="input-container">
                            <label for="pan_back">Upload PAN Card Back</label>
                            <span class="error"><?= $errors['pan_back'] ?? '' ?></span>  
                            <input type="file" name="pan_back" <?= isset($errors['pan_back']) ? 'class="error"' : '' ?>>
                        </div>
                                             
                        <div class="input-container">
                            <label for="profile_photo">Upload Your Photo</label>
                            <span class="error"><?= $errors['profile_photo'] ?? '' ?></span>
                            <input type="file" name="profile_photo" <?= isset($errors['profile_photo']) ? 'class="error"' : '' ?>>
                        </div>
                        
                        <div class="input-container">
                            <label for="bank_statement">Bank Statement</label>
                            <span class="error"><?= $errors['bank_statement'] ?? '' ?></span>
                            <input type="file" name="bank_statement" accept=".jpg,.jpeg,.png,.pdf" <?= isset($errors['bank_statement']) ? 'class="error"' : '' ?>>
                        </div>
                        
                        <div class="input-container">
                            <label for="password">Password</label>
                            <span class="error"><?= $errors['password'] ?? '' ?></span>
                            <input type="password" name="password" <?= isset($errors['password']) ? 'class="error"' : '' ?>>
                        </div>
                        
                        <div class="input-container">
                            <label for="confirm_password">Confirm Password</label>
                            <span class="error"><?= $errors['confirm_password'] ?? '' ?></span>
                            <input type="password" name="confirm_password" <?= isset($errors['confirm_password']) ? 'class="error"' : '' ?>>
                        </div>
                        
                        <button type="submit">Submit</button>
                    </div>
                </form>
                <?php if (isset($errors['general'])): ?>
                    <p class="error"><?= $errors['general'] ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>