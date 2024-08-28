<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include('db.php');

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Fetch all users from the database
$sql = "SELECT id, firstname, lastname, email, mobile_number, dob FROM users";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        /* Light theme and responsive styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0; /* Light background for consistency */
            color: #333; /* Dark text color for contrast */
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
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
        h2 {
            text-align: center;
            margin-top: 80px; /* Margin to account for fixed navbar */
            margin-bottom: 20px;
            color: #333; /* Dark text color */
            font-size: 24px;
        }
        table {
            width: 100%;
            max-width: 1200px;
            border-collapse: separate;
            border-spacing: 0 10px; /* Add space between rows */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background-color: #ffffff; /* White background for the table */
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4; /* Light grey for table header */
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9; /* Alternate row color for better readability */
        }
        tr:hover {
            background-color: #f1f1f1; /* Light grey on hover */
        }
        .action-btn {
            padding: 8px 16px;
            color: white;
            border: none;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: inline-block;
            margin-right: 5px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .action-btn.edit {
            background-color: #1e88e5; /* Blue for edit button */
        }
        .action-btn.delete {
            background-color: #e53935; /* Red for delete button */
        }
        .action-btn:hover {
            transform: scale(1.05);
        }
        .logout-btn {
            padding: 10px 20px;
            background-color: #444;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 16px;
        }
        .logout-btn:hover {
            background-color: #333;
            transform: scale(1.05);
        }
        .profile-link {
            color: #1e88e5;
            text-decoration: none;
        }
        .profile-link:hover {
            text-decoration: underline;
        }
        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            table {
                width: 100%;
                font-size: 14px;
            }
            .logout-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
    <div class="logo">User Management App</div>
        <div>
            
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <h2>Welcome <a href="profile.php" class="profile-link"><?php echo htmlspecialchars($_SESSION['email']); ?></a>!</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Mobile Number</th>
                <th>Date of Birth</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";

                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['mobile_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
                    echo "<td>";
                    echo "<a href='edit_user.php?id=" . htmlspecialchars($row['id']) . "' class='action-btn edit'>Edit</a>";
                    echo "<a href='#' class='action-btn delete' onclick=\"confirmDelete(" . htmlspecialchars($row['id']) . ")\">Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        function confirmDelete(userId) {
            if (confirm('Do you really want to delete this user?')) {
                window.location.href = 'delete_user.php?id=' + userId;
            }
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
