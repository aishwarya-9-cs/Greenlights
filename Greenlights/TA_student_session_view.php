<?php
$servername = "localhost";
$username = "root";
$password = "root";
$database = "TA_development";
$students_name = "All_Students";
$module = "ELECLAB1";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


print '<table>
        <tr>
          <td class="align-top">';

// Student table
$sql = "SELECT id, firstname, lastname
        FROM $students_name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    print '<table border cellpadding="10" width="500">'; 
    print '<tr>
            <th>Student</th>
            </tr>';
    
    while ($row = $result->fetch_assoc()) {
        $name = $row['firstname'][0] . ". " . $row['lastname'] . " #" . $row['id'];
        print '<tr>';
            print '<td>' . $name . '</td>';
        print '</tr>';
    }
    print '</table>';
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

print '</td>
        <td>';

// Module table
$sql = "SELECT session
        FROM $module";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    print '<table border cellpadding="10" width="500">'; 
    print '<tr>
            <th>Session</th>
            </tr>';
    
    while ($row = $result->fetch_assoc()) {
        print '<tr>';
            print '<td>' . $row['session'] . '</td>';
        print '</tr>';
    }
    print '</table>';
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

print '</td>
     </tr>
</table>';

$conn->close();
?>