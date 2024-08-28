<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include('db.php');

// Fetch user details from the database
$email = $_SESSION['email'];
$sql = "SELECT id, firstname, lastname, email, dob, image FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $image = $_FILES["image"];
    if ($image["error"] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($image["name"]);
        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            $updateSql = "UPDATE users SET image = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ss", $image["name"], $email);
            if ($updateStmt->execute()) {
                $success = "Image uploaded successfully. 🎉";
                $user["image"] = $image["name"];
            } else {
                $error = "Failed to update image in the database. ❌";
            }
        } else {
            $error = "Failed to upload image. ❌";
        }
    } else {
        $error = "Error in file upload. ❌";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        /* Light theme and responsive styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0; /* Light background for consistency */
            color: #333; /* Dark text color for contrast */
            margin: 0;
            padding: 0;
        }
        .navbar {
            width: 100%;
            background-color: #ffffff; /* White background for the navbar */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 10px 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }
        .navbar a {
            text-decoration: none;
            color: #333;
            font-size: 16px;
            margin: 0 15px;
            transition: color 0.3s, transform 0.3s;
            padding: 8px 12px;
            border-radius: 4px;
        }
        .navbar a:hover, .navbar a.active {
            color: #007bff; /* Blue color on hover */
            background-color: #e0e0e0; /* Light grey background on hover */
            transform: scale(1.05);
        }
        .navbar .logo {
            font-weight: bold;
            font-size: 20px;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 80px auto 0; /* Margin to account for fixed navbar */
            background-color: #ffffff; /* White background for container */
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333; /* Dark text color */
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333; /* Dark grey for labels */
        }
        .form-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f9f9f9; /* Light grey input background */
            color: #333; /* Dark text color for input */
            box-sizing: border-box;
        }
        .form-group input[type="file"] {
            padding: 0;
        }
        .form-group img {
            max-width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: block;
            margin-bottom: 10px;
        }
        .form-group input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            padding: 14px;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .form-group input[type="submit"]:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
        .error, .success {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .success {
            color: #32cd32; /* Green color for success messages */
        }
        .back-btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 16px;
        }
        .back-btn:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Home</a>
        <div>
            <a href="welcome.php">Users</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>User Profile 🧑‍💻</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="text" id="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="image">Profile Image</label>
                <?php if ($user['image']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Image">
                <?php else: ?>
                    <p>No image uploaded</p>
                <?php endif; ?>
                <input type="file" id="image" name="image">
            </div>
            <div class="form-group">
                <input type="submit" value="Upload Image">
            </div>
        </form>
    </div>
</body>
</html>
