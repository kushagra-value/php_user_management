<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include('db.php');

// Check if the user ID is provided
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user data based on ID
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "<p>User not found! ⚠️</p>";
        exit();
    }
} else {
    echo "<p>No user ID provided! ⚠️</p>";
    exit();
}

// Handle form submission for updating user data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $mobile_number = $_POST['mobile_number'];
    $dob = $_POST['dob'];

    // Check if all fields are filled
    if (!empty($firstname) && !empty($lastname) && !empty($mobile_number) && !empty($dob)) {
        // Validate mobile number
        if (!preg_match('/^\d{10}$/', $mobile_number)) {
            $error = "Mobile number must be exactly 10 digits. 📞";
        } else {
            // Update user data in the database
            $update_sql = "UPDATE users SET firstname=?, lastname=?, mobile_number=?, dob=? WHERE id=?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssssi", $firstname, $lastname, $mobile_number, $dob, $user_id);

            if ($update_stmt->execute()) {
                $success = "User updated successfully. 🎉";
            } else {
                $error = "Error updating record: " . $conn->error . " ❌";
            }
        }
    } else {
        $error = "Please fill all fields. ⚠️";
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
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .success {
            color: #32cd32; /* Green color for success messages */
            margin-bottom: 15px;
            text-align: center;
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
        <div class="logo">User Management App</div>
        <div>
            <a href="welcome.php">Users</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Edit User</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly>
            </div>
            <div class="form-group">
                <label for="mobile_number">Mobile Number 📞</label>
                <input type="text" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($user['mobile_number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Update User">
            </div>
        </form>

    </div>
</body>
</html>
