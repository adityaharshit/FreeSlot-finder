<?php
// Database connection
session_start();
require 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    // $password = 'Admin123';
    // $password = password_hash($password, PASSWORD_DEFAULT);
    // echo $password;
    $checkQuery = "SELECT * FROM Admin WHERE Username = ? AND Password = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Login successfull";
        $row = $result->fetch_assoc();
        $_SESSION["username"] = $row['username'];
        echo "<script>
        alert('Login successfull');
        window.location.href='dashboard.html';
        </script>"; 
    } else {
        
        echo "<script>
        alert('Invalid Creadentials');
        window.location.href='index.html';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
