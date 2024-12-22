<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Your database password
$dbname = "finder"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    
        // Proceed with insertion
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insertQuery = "INSERT INTO Admin (Username, Password) VALUES ( ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ss",$username, $password);

        if ($stmt->execute()) {
            echo "<script>
                alert('Registration successful');
                window.location.href='login.html';
                </script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    

    $stmt->close();
    $conn->close();
}
?>
