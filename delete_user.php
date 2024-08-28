<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include('db.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Prepare and execute the delete statement
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $response = "Deletion successful.";
    } else {
        $response = "Error deleting user: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    
    // Return the response
    echo "<script>
            alert('$response');
            window.location.href = 'welcome.php';
          </script>";
} else {
    echo "<script>
            alert('No user ID provided.');
            window.location.href = 'welcome.php';
          </script>";
}
?>