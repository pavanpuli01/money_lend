<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "money_lend";

// Establish connection to the database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle AJAX Request (POST method)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the POST data (IP address, latitude, longitude, city, region, country, and user agent)
    $ip = $_POST['ip_address'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $city = $_POST['city'];  // City from Ipify location data
    $region = $_POST['region'];  // Region from Ipify location data
    $country = $_POST['country'];  // Country from Ipify location data
    $user_agent = $_SERVER['HTTP_USER_AGENT']; // Browser and OS info
    $created_at = date("Y-m-d H:i:s");  // Current date and time

    // Insert the data into the database
    $stmt = $conn->prepare("INSERT INTO user_logins (ip_address, latitude, longitude, city, region, country, user_agent, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $ip, $latitude, $longitude, $city, $region, $country, $user_agent, $created_at);

    if ($stmt->execute()) {
        echo "success";  // Return success message
    } else {
        echo "error: " . $stmt->error;  // Return error message if any issue occurs
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging User Info...</title>
</head>
<body>
    <h2>Logging your details... Please wait.</h2>

    <script>
      // Get the user's IP using Ipify API
fetch('https://api.ipify.org?format=json')
    .then(response => response.json())
    .then(data => {
        const userIP = data.ip;  // Get IP from Ipify
        console.log("User IP:", userIP); // Debugging log

        // Get User's Location using Geolocation API for latitude and longitude
        navigator.geolocation.getCurrentPosition(position => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            console.log("User Location:", latitude, longitude); // Debugging log

            // Now, we can get city, region, and country information based on the IP address
            fetch(`https://ipapi.co/${userIP}/json/`)  // Using ipapi.co to fetch location details
                .then(response => response.json())
                .then(locationData => {
                    const city = locationData.city || 'Unknown';
                    const region = locationData.region || 'Unknown';
                    const country = locationData.country_name || 'Unknown';

                    console.log("Location Data:", city, region, country); // Debugging log

                    // Get user agent (browser and OS info)
                    const userAgent = navigator.userAgent;  // User agent from browser

                    console.log("User Agent:", userAgent); // Debugging log

                    // Send Data to PHP for Database Insert
                    fetch('', {  // Send data to the same page (PHP)
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `ip_address=${userIP}&latitude=${latitude}&longitude=${longitude}&city=${city}&region=${region}&country=${country}&user_agent=${userAgent}`
                    })
                    .then(response => response.text())
                    .then(result => {
                        console.log("Server Response:", result); // Debugging log
                        if (result.trim() === "success") {
                            console.log("Redirecting to 3register.html...");
                            window.location.href = "3register.html";  // Redirect after successful logging
                        } else {
                            alert("Logging failed: " + result);  // Error logging
                        }
                    })
                    .catch(error => {
                        console.error("Error in sending data:", error);
                        alert("Error sending data. Redirecting to 3register.html.");
                        window.location.href = "3register.html";  // Redirect on error
                    });
                })
                .catch(error => {
                    console.error("Error fetching location from IP:", error);
                    alert("Error fetching location. Redirecting to 3register.html.");
                    window.location.href = "3register.html";  // Redirect if location fetch fails
                });
        }, () => {
            // If geolocation is denied or not available, send IP address with "Unknown" location
            console.warn("Geolocation access denied or not available.");

            fetch(`https://ipapi.co/${userIP}/json/`)  // Using ipapi.co to fetch location details
                .then(response => response.json())
                .then(locationData => {
                    const city = locationData.city || 'Unknown';
                    const region = locationData.region || 'Unknown';
                    const country = locationData.country_name || 'Unknown';

                    console.log("Location Data (No Geolocation):", city, region, country); // Debugging log

                    // Get user agent (browser and OS info)
                    const userAgent = navigator.userAgent;

                    console.log("User Agent (No Geolocation):", userAgent); // Debugging log

                    fetch('', {  // Send data to the same page (PHP)
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `ip_address=${userIP}&latitude=Unknown&longitude=Unknown&city=${city}&region=${region}&country=${country}&user_agent=${userAgent}`
                    })
                    .then(response => response.text())
                    .then(result => {
                        console.log("Server Response (No Location):", result); // Debugging log
                        if (result.trim() === "success") {
                            console.log("Redirecting to 3register.html...");
                            window.location.href = "3register.html";  // Redirect after successful logging
                        } else {
                            alert("Logging failed: " + result);  // Error logging
                        }
                    })
                    .catch(error => {
                        console.error("Error in sending data:", error);
                        alert("Error sending data. Redirecting to 3register.html.");
                        window.location.href = "3register.html";  // Redirect on error
                    });
                })
                .catch(error => {
                    console.error("Error fetching IP location:", error);
                    alert("Error fetching location. Redirecting to 3register.html.");
                    window.location.href = "3register.html";  // Redirect on error
                });
        });
    })
    .catch(error => {
        console.error("Error fetching IP:", error);
        alert("Error fetching IP. Redirecting to 3register.html.");
        window.location.href = "3register.html";  // Redirect if IP fetch fails
    });

    </script>
</body>
</html>
