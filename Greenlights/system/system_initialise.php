<?php
include_once("../inc/enable_debug.php");
include_once("variables.php");

// Create connection
$conn = new mysqli($servername, $server_username, $server_password);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if exists, if not, create a db
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully with the name $dbname<br/>";
} else {
    echo "Error creating $dbname database: " . $conn->error;
}
include_once("../inc/db_connect.php");

// Create all modules table
$sql = "CREATE TABLE IF NOT EXISTS $all_modules_table_name (
    num INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    module_name VARCHAR(128) NOT NULL,
    module_hash VARCHAR(70) NOT NULL,
    access_user_id INT(10) UNSIGNED NOT NULL,
    access_user_type ENUM('Lecturer', 'TA', 'Student', 'admin') NOT NULL,
    student_list_hash VARCHAR(70) NOT NULL
)";
if ($conn->query($sql) === TRUE) {
  echo "All modules created successfully<br/>";
} else {
  echo "Error creating all modules table: " . $conn->error;
}

// Create all students table
$sql = "CREATE TABLE IF NOT EXISTS $all_students_table_name (
    num INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id INT(9) UNSIGNED NOT NULL,
    firstname VARCHAR(128) NOT NULL,
    lastname VARCHAR(128) NOT NULL,
    email VARCHAR(128) NOT NULL,
    course_code VARCHAR(10) NOT NULL,
    year SMALLINT(2) NOT NULL,
    module_name VARCHAR(128) NOT NULL,
    module_hash VARCHAR(70) NOT NULL,
    student_table_hash VARCHAR(70) NOT NULL
)";
if ($conn->query($sql) === TRUE) {
  echo "All students table created successfully<br/>";
} else {
  echo "Error creating all students table: " . $conn->error;
}

// Create credentials table
$sql = "CREATE TABLE IF NOT EXISTS $credentials_table_name (
    user_id INT(9) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(64) NOT NULL,
    lastname VARCHAR(64) NOT NULL,
    email VARCHAR(128) NOT NULL,
    pass VARCHAR(128) NOT NULL,
    user_type ENUM('Lecturer', 'TA', 'Student', 'admin')
)";
if ($conn->query($sql) === TRUE) {
  echo "Credentials table created successfully<br/>";
} else {
  echo "Error creating credentials table: " . $conn->error;
}
$sql = "ALTER TABLE $credentials_table_name AUTO_INCREMENT=100000001";
if ($conn->query($sql) === TRUE) {
  echo "Altered auto_increment<br/>";
} else {
  echo "Error setting credentials table auto-increment: " . $conn->error;
}

// Add default users
include("register-user.php");
registerUser("admin@uclgreenlighs", "ucladmin", "admin", "Mr", "Admin", "");

registerUser("lecturer@ucl", "ucladmin", "Lecturer", "Stanley", "Brown", "");
registerUser("zdadaur@ucl.ac.uk", "yusdfgusdgygdsfyug", "Lecturer", "David", "Urwin", "");
registerUser("zdajawa@ucl.ac.uk", "sduihiudshuiuhdshd", "Lecturer", "Jack", "Walsh", "");

registerUser("ta@ucl", "ucladmin", "TA", "Dan", "Matthews", "");
registerUser("zceanco@ucl.ac.uk", "udshfuidshaidhsaf", "TA", "Andrew", "Cooper", "");
registerUser("zcedaja@ucl.ac.uk", "eruhuirehuihuhdsi", "TA", "David", "Jackson", "");
registerUser("zcephsc@ucl.ac.uk", "asjdijsfdauhuisdh", "TA", "Phillip", "Scott", "");
registerUser("zceshst@ucl.ac.uk", "reughuigisadgugda", "TA", "Sharone", "Stone", "");
registerUser("zcemase@ucl.ac.uk", "ebrbsbdahudisfhud", "TA", "Mark", "Seal", "");

registerUser("student@ucl", "ucladmin", "Student", "Leo", "Walker", "18293867");
registerUser("aishwarya.shaji.19@ucl.ac.uk", "udshiudshiudshfudss", "Student", "Aishwarya", "Shaji", "19017016");
registerUser("zceeast@ucl.ac.uk", "dgfsuygdfsygdsfugdfs", "Student", "Andrey", "Staniukynas", "18030116");
$conn->close();
?>