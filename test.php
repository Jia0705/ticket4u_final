<?php
echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Test database connection
$conn = new mysqli('localhost', 'root', '', 'ticket4u');
if ($conn->connect_error) {
    echo "Database connection FAILED: " . $conn->connect_error;
} else {
    echo "Database connection SUCCESSFUL!<br>";
    
    // Test query
    $result = $conn->query("SELECT COUNT(*) as count FROM events");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Events in database: " . $row['count'];
    }
}
