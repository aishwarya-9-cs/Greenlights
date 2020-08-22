<?php
$servername = "localhost";
$username = "root";
$password = "root";
$database = "TA_development";
$students_name = "All_Students";
$module = "ELECLAB1";

//debug enabled
error_reporting(E_ALL);
ini_set('display_errors',1);

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Create table for each student named by their student id
foreach ($conn->query("SELECT id FROM $students_name") as $row) {
    $id = $row['id'];
    $table = "s" . $id;
    
    // Create table. Example table name "s18365826"
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        week INT(2) UNSIGNED NOT NULL,
        session VARCHAR(128) NOT NULL,
        task VARCHAR(256) NOT NULL,
        group_number SMALLINT(3) UNSIGNED DEFAULT NULL,
        rating ENUM('Green', 'Amber', 'Red') DEFAULT NULL,
        task_expected SMALLINT(4) NOT NULL,
        task_actual SMALLINT(4) DEFAULT NULL,
        comment VARCHAR(256) DEFAULT NULL,
        action VARCHAR(256) DEFAULT NULL,
        meeting_date DATETIME DEFAULT NULL,
        meeting_duration INT(3) DEFAULT NULL
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table $table created successfully<br/>";
    } else {
        echo "Error creating $table table: " . $conn->error;
        break;
    }
}

// Close the connection
$conn->close();
?>