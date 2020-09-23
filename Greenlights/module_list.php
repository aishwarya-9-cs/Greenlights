<?php
include_once("inc/start_session.php");
include_once("inc/ta_check.php");
include_once("inc/db_connect.php");
include("inc/header.php");

// Option when user came back from module edit
if (isset($_POST['module_hash_to_save'])) {
    $module_hash_to_save = $_POST['module_hash_to_save'];
    $_POST = array();
    $sql = "SELECT student_table_hash 
                FROM $all_students_table_name
                WHERE module_hash='$module_hash_to_save'";
    $big_resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
    // for each student table
    while($big_row = mysqli_fetch_array($big_resultset)) {
        $student_table_hash = $big_row['student_table_hash'];
        $sql = "SELECT id, week, session, task, task_duration, task_type FROM $module_hash_to_save";
        $small_resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
        // for each row in module table
        while($small_row = mysqli_fetch_array($small_resultset)) {
            $id = $small_row['id'];
            $week = $small_row['week'];
            $session = $small_row['session'];
            $task = $small_row['task'];
            $task_duration = $small_row['task_duration'];
            $task_type = $small_row['task_type'];
            // update table for each student
            $sql = "UPDATE $student_table_hash SET id='$id', week='$week', session='$session', task='$task', task_duration='$task_duration', task_type='$task_type' WHERE id='$id'";
            if ($conn->query($sql) === TRUE) {
                echo "";
            } else {
                die ("Error: " . $conn->error);
            }
        }
    }
}

// ----- YOUR MODULES AREA -----
$no_modules_message = "No modules added yet";
?>
<h3 class="large-section-title">
        <font>Your modules</font>
</h3>
<table id="your-modules-table">
<?php
    $sql = "SELECT module_name, module_hash, student_list_hash
            FROM $all_modules_table_name
            WHERE access_user_id=$user_id";  
    if ($result = $conn->query($sql)) {
        if($result->num_rows > 0) {
            while($row = mysqli_fetch_array($result)) {
?>
    <tr class="your-modules-row">
        <td class="your-modules-td">
<?php
echo '<h4><a href=module_overview.php?module='. $row['module_hash'] .'&student_list='. $row['student_list_hash'] .'&module_name='. $row['module_name'] .'>'. $row['module_name'] .'</a></h4>';
?>
        </td>
        <td class="your-modules-span">
<?php
echo '<a href=module_edit.php?module='. $row['module_hash'] .'&student_list='. $row['student_list_hash'] . '&module_name='. $row['module_name'] .'>Edit module</a><br>';
?>
        </td>
    </tr>
<?php
            }
        } else {
            print $no_modules_message;
        }
    } else {
        print $no_modules_message;
    }
?>
</table>
<?php
// ----- ADD MODULE AREA -----
// Only allow lecturers to add new modules
if ($_SESSION['user_type'] == 'Lecturer') {
?>
<form name=course_entry method=post action="add_module_1.php" enctype="multipart/form-data">
    <h3 class="large-section-title">
        <font>Add new module</font>
    </h3>
        <h4 class="section-title">
            <font>Module name</font>
        </h4>
        <p>
            <input type=text placeholder="Enter Module Name" name=module_name size=50>
        </p>
        <h4 class="section-title">
            <font>Task list</font>
        </h4>
        <div class="section-contents">Option 1: input tasks by hand (default, no action needed)</div>
        <div class="section-contents">Option 2: upload via CSV file
            <input style="margin-left:10px" type="file" name="task_file" id="task_file" accept=".csv">
        </div>
        <div class="section-contents">Option 3: clone tasks from another module
            <select style="margin-left:10px" name="module_hash">
                    <option value="0">No</option>  
<?php
                $sql = "SELECT module_name, module_hash
                        FROM $all_modules_table_name
                        WHERE access_user_id=$user_id";  
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_array($result)) {
                    echo '<option value="'. $row['module_hash'] .'">'. $row['module_name'] .'</option>';
                }
?> 
            </select>  
        </div>
        <h4 class="section-title">
            <font>Class list</font>
        </h4>
        <div class="section-contents">Option 1: upload via CSV file
            <input style="margin-left:10px" type="file" name="file" id="file" accept=".csv">
        </div>
        <div class="section-contents">Option 2: select from existing class lists
            <select style="margin-left:10px" name="student_list_hash"> 
                <option value="0">No</option>
<?php
                $sql = "SELECT module_name, student_list_hash
                        FROM $all_modules_table_name
                        WHERE access_user_id=$user_id";  
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_array($result)) {
                    echo '<option value="'. $row['student_list_hash'] .'">'. $row['module_name'] .'</option>';
                }
?>
            </select> 
        </div>
    <input style="size:large" type=submit name=submit value="Add New Module">
</form>

<?php
}
include("footer_test.php");
?>