<?php
include_once("inc/start_session.php");
include_once("inc/ta_check.php");
include_once("inc/db_connect.php");
include_once("inc/header.php");

$per_student_view = "per_student_view.php";
$per_session_view = "per_session_view.php";

// Check get arguments
if (isset($_GET['module']) && isset($_GET['student_list']) && isset($_GET['module_name'])) {
    $module_name = $_GET['module_name'];
    $module_hash = $_GET['module'];
    $student_list_hash = $_GET['student_list'];
} else {
    echo "Error: get arguments was not found";
    die();
}
?>
<h4 style="margin-top:10px; color:dimgray; font-size:x-large">Module: <?php echo $module_name; ?></h4>
    <div style="margin-top: 10px">
        <table id="joint-table" width="100%" style="position:relative; top:0;">
            <tr>
                <div class=left-table>
                    <td style="width:50%; position:relative; top:0">
                        <table id="left-table" class="table table-striped" style="border-right:solid lightgrey 0.5px; border-bottom:solid lightgrey 0.5px; border-left:solid lightgrey 0.5px;">
                            <tr>
                                <th>Student</th>
                            </tr>
<?php

//  To get hash of per-student table:
// 1. Get student id from $student_list_hash table
// 2. Get a name of students table from $all_students_table_name table
// 3. Add this name of a table into link

// Student table
$sql = "SELECT student_id, firstname, lastname
        FROM $student_list_hash";
$big_result = $conn->query($sql);
if ($big_result->num_rows > 0) {
    // for each student
    while ($big_row = $big_result->fetch_assoc()) {
        $display_name = $big_row['firstname'] . " " . $big_row['lastname'] . " #" . $big_row['student_id'];
        $student_id = $big_row['student_id'];
        $sql = "SELECT student_table_hash
        FROM $all_students_table_name
        WHERE module_hash=\"$module_hash\"
        AND student_id=\"$student_id\"";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $student_table_hash = "";
                    $student_table_hash = $row['student_table_hash'];
                    if ($student_table_hash == "") {
                        echo "error: no student table hash found";
                    }
                }
        } else {
            echo "SQL error: " . $sql . "<br/>" . $conn->error;
            die();
        }
            
        print '<tr>';
            print '<td><a href=\'' . $per_student_view . 
                '?module_hash=' . $module_hash . 
                '&student_list_hash=' . $student_list_hash . 
                '&student_table_hash=' . $student_table_hash .
                '&student_id=' . $big_row['student_id'] . '\' >' . $display_name . '</a></td>';
        print '</tr>';
    }
} else {
    echo "SQL error: " . $sql . "<br/>" . $conn->error;
    die();
}
?>
                        </table>
                    </td>
                </div>
                <div class=right-table>
                    <td style="position:absolute; width:50%; top:0">
                        <table id="right-table" class="table table-striped" style="border-right:solid lightgrey 0.5px; border-bottom:solid lightgrey 0.5px;">
                            <tr>
                                <th>Teaching event</th>
                            </tr>
<?php
// Module table
$sql = "SELECT DISTINCT session
        FROM $module_hash";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
     while ($row = $result->fetch_assoc()) {
        $session_name = $row['session'];
        print '<tr>';
            print '<td><a href=\''. $per_session_view . 
                '?session=' . str_replace(' ', '_', $session_name) . 
                '&module_hash=' . $module_hash . 
                '&module_name=' . $module_name . 
                '&student_list_hash=' . $student_list_hash . '\' >' . $session_name . '</a></td>';
        print '</tr>';
    }
} else {
    echo "Error: " . $sql . "<br/>" . $conn->error;
}
?>
                        </table>
                    </td>
                </div>
        </table>
    </div>
<?php
include("inc/footer.php");
$conn->close();
?>