<?php
include('db.php'); // Include the database connection

session_start(); // Start the session at the very beginning

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['email'] = $email;
            header("Location: welcome.php"); // Redirect to the welcome page
            exit();
        } else {
            $error = "⚠️ Invalid password.";
        }
    } else {
        $error = "⚠️ No user found with that email.";
    }
}

// Display messages
if (isset($_GET['status']) && $_GET['status'] == 'loggedout') {
    $success = "You have been logged out successfully. 👋";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Modern, responsive styling with a light theme */
        body {
            font-family: 'Arial', sans-serif;
            background: #f0f0f0; /* Light grey background */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
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
            max-width: 500px;
            background-color: #ffffff; /* White for the container */
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Softer shadow */
            position: relative;
            box-sizing: border-box;
            margin-top: 80px; /* Margin to account for fixed navbar */
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333; /* Darker text color for better contrast */
            font-size: 28px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #555; /* Slightly darker grey for labels */
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            background-color: #f9f9f9; /* Light input background */
            color: #333; /* Dark text color for input */
        }
        .form-group input[type="submit"] {
            background-color: #007bff; /* Blue background */
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 12px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .form-group input[type="submit"]:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: scale(1.05);
        }
        .error, .success {
            color: #ff4d4d; /* Red color for error messages */
            margin-bottom: 10px;
            text-align: center;
        }
        .success {
            color: #28a745; /* Green color for success messages */
        }
        .register-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff; /* Blue background */
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .register-btn:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .emoji {
            margin-right: 8px;
        }
        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                margin-top: 70px; /* Adjusted margin for mobile view */
                padding: 20px;
            }
            .register-btn {
                padding: 8px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<div class="navbar">
        <div class="logo">User Management App</div>
        <div>
            <a href="index.php">Register</a>
        </div>
    </div>
    <div class="container">
        <h2>Login 🔐</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="email"><span class="emoji"></span>Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password"><span class="emoji"></span>Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Login">
            </div>
        </form>
    </div>
</body>
</html>
