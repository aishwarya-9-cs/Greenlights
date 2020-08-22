<?php 
include("inc/header.php"); 
include('inc/container.php');
$servername = "localhost";
$username = "root";
$password = "root";
$database = "TA_development";
$students_name = "All_Students";
$module = "ELECLAB1";

// Create connection
include_once("inc/db_connect.php");

// Get one students info
$sql = "SELECT id, firstname, lastname, email, course_code, year
        FROM $students_name
        WHERE id = 18182839
        ORDER BY id ASC
        LIMIT 0, 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $email = $row['email'];
        $course_code = $row['course_code'];
        $year = $row['year'];
    }
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Create table name
$table = "s" . $id;

// Echo student info
echo "Name: " . $firstname . " " . $lastname . "<br/>";
echo "Student id: " . $id . "<br/";
echo "Email: " . $email . "<br/>";
echo "Course code: " . $course_code . "<br/>";
echo "Year: " . $year . "<br/>";
?>
<html>
    <head>
        <title>TA view</title>
    </head>
    <body>
    <div class="container">	
    <div class="row">
        <table id="editableTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Unique id</th>
                <th>Week</th>
                <th>Session</th>
                <th>Task</th>
                <th>Group number</th>
                <th>Rating</th>
                <th>Task expected</th>
                <th>Task actual</th>
                <th>Comment</th>
                <th>Action</th>
                <th>Meeting date</th>
                <th>Meeting duration</th>
            </tr>
        </thead>
        <tbody>
<?php
// Get all info from student's table
// Editable fields: (counting from 0)
//  group_number 3
//  rating 4
//  task_actual 6
//  comment 7
//  action 8
//  meeting_date 9
//  meeting_duration 10
$sql_query = "SELECT id, week, session, task, group_number, rating, 
        task_expected, task_actual, 
        comment, action, meeting_date, meeting_duration
        FROM $table";
$resultset = mysqli_query($conn, $sql_query) or die("database error:". mysqli_error($conn));
while( $row = mysqli_fetch_assoc($resultset) ) {
        print '<tr id="' . $row['id'] . '">';
            print '<td>' . $row['id'] . '</td>';
            print '<td>' . $row['week'] . '</td>';
            print '<td>' . $row['session'] . '</td>';
            print '<td>' . $row['task'] . '</td>';
            print '<td>' . $row['group_number'] . '</td>';
            print '<td>' . $row['rating'] . '</td>'; 
            print '<td>' . $row['task_expected'] . '</td>';
            print '<td>' . $row['task_actual'] . '</td>';
            print '<td>' . $row['comment'] . '</td>'; 
            print '<td>' . $row['action'] . '</td>'; 
            print '<td>' . $row['meeting_date'] . '</td>'; 
            print '<td>' . $row['meeting_duration'] . '</td>'; 
        print '</tr>';
}
?>
        </tbody>
            </table>
        </div>
        </div>
        <script src="plugin/bootstable.js"></script>
        <script src="js/editable.js"></script>
</body>
</html>
<?php
$conn->close();
?>