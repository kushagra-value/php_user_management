<?php
ob_start(); // Start output buffering

include('db.php'); // Include the database connection

$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $mobile_number = $_POST['mobile_number'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $dob = $_POST['dob'];

    // Basic validation
    if (empty($firstname) || empty($lastname) || empty($email) || empty($mobile_number) || empty($password) || empty($repassword) || empty($dob)) {
        $error = "All fields are required.";
    } else if ($password !== $repassword) {
        $error = "Passwords do not match.";
    } else if (!preg_match('/^\d{10}$/', $mobile_number)) {
        $error = "Mobile number must be exactly 10 digits.";
    } else if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{4,}$/', $password)) {
        $error = "Password must be at least 4 characters long, contain at least one uppercase letter, one number, and one special character.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user into the database
        $sql = "INSERT INTO users (firstname, lastname, email, mobile_number, password, dob) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $firstname, $lastname, $email, $mobile_number, $hashed_password, $dob);

        if ($stmt->execute()) {
            $success = "Registration successful!";
            header("Location: login.php"); // Redirect to the login page
            ob_end_flush(); // Flush and turn off output buffering
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}

$conn->close();
ob_end_flush(); // Ensure any buffered output is sent
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Light, modern styling with creative touches */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9; /* Very light grey background */
            margin: 0;
            padding: 0;
            color: #333; /* Dark text color for readability */
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
            background-color: #ffffff; /* White background for the container */
            padding: 40px;
            border-radius: 12px; /* More rounded corners */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); /* Larger shadow for depth */
            margin: 40px auto;
            box-sizing: border-box;
            border: 1px solid #ddd; /* Light border to frame the container */
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333; /* Dark text color */
            font-size: 26px;
            font-weight: 700;
            text-transform: uppercase; /* Capitalize the heading */
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            color: #555; /* Darker grey for labels */
        }
        .form-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ccc; /* Light grey border */
            border-radius: 8px; /* Rounded corners for inputs */
            box-sizing: border-box;
            font-size: 16px;
            background-color: #fafafa; /* Slightly off-white background */
            color: #333; /* Dark text color */
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus {
            border-color: #007bff; /* Blue border on focus */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Light blue shadow on focus */
            outline: none; /* Remove default outline */
        }
        .form-group input[type="submit"] {
            background-color: #007bff; /* Blue background for the button */
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 14px;
            border-radius: 8px; /* Rounded corners */
            transition: background-color 0.3s, transform 0.3s;
        }
        .form-group input[type="submit"]:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: scale(1.05);
        }
        .error, .success {
            color: #d9534f; /* Red color for error messages */
            margin-bottom: 15px;
            text-align: center;
            font-weight: 600;
        }
        .success {
            color: #5bc0de; /* Light blue color for success messages */
        }
        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                margin: 20px auto;
                padding: 25px;
            }
            .navbar a {
                font-size: 16px;
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">User Management App</div>
        <div>
            <a href="login.php">Login</a>
        </div>
    </div>
    <div class="container">
        <h2>Register</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="mobile_number">Mobile Number</label>
                <input type="text" id="mobile_number" name="mobile_number" pattern="\d{10}" title="Mobile number must be exactly 10 digits" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="repassword">Re-enter Password</label>
                <input type="password" id="repassword" name="repassword" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Register">
            </div>
        </form>
    </div>

    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var repassword = document.getElementById("repassword").value;
            var passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{4,}$/;

            if (!passwordPattern.test(password)) {
                alert("Password must be at least 4 characters long, contain at least one uppercase letter, one number, and one special character.");
                return false;
            }
            if (password !== repassword) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
