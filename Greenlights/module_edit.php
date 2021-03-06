<?php
// Here we allow Lecturers to edit existing modules tables
// Editable table, files used: module_edit_helper.js; module_edit_helper.php
include_once("inc/enable_debug.php");

include_once("inc/start_session.php");
include_once("inc/ta_check.php");
include_once("inc/db_connect.php");
include("inc/header.php");

// Get information about module and a student list hash
if (isset($_GET['module']) && isset($_GET['student_list'])) {
    $module_hash = $_GET['module'];
    $student_list_hash = $_GET['student_list'];
} else {
    echo "Error: get arguments was not found";
    die();
}

// Get module name from $all_modules table
$sql = "SELECT module_name, module_hash FROM $all_modules_table_name WHERE module_hash='$module_hash' LIMIT 0,1";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
$row = mysqli_fetch_assoc($resultset);
$module_name = $row['module_name'];

$callBackURL = "./module_list.php";
// Helper for an editable table
?>     
    <div id="js-helper"
         data-module-id="<?php echo htmlspecialchars($module_hash); ?>">
    </div>
    <h3>
        <font color=grey><?php echo $module_name; ?></font>
    </h3>
    <div id="table_view" class="input-field">	
        <table id="data_table" class="table table-striped">
            <thead>
                <tr>
                    <th>Unique id</th>
                	<th>Week number</th>
                	<th>Teaching Event</th>
                	<th>Task</th>
                	<th>Estimated time for a task (minutes)</th>
                	<th>Group or Individual (G/I)</th>
                </tr>
            </thead>
            <tbody>
<?php
$sql = "SELECT id, week, session, task, task_duration, task_type FROM $module_hash";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
while( $row = mysqli_fetch_assoc($resultset) ) {
        print '<tr>';
            print '<td>' . $row['id'] . '</td>';
            print '<td>' . $row['week'] . '</td>';
            print '<td>' . $row['session'] . '</td>';
            print '<td>' . $row['task'] . '</td>';
            print '<td>' . $row['task_duration'] . '</td>';
            print '<td>' . $row['task_type'] . '</td>';
        print '</tr>';
}
?>
            </tbody>
        </table>
        <button id='add' for-table='#data_table'>Add Row (clones last row)</button>
    </div>
<?php
// Only allow Lecturers to change access
	if ($_SESSION['user_type'] == 'Lecturer') {
?>
    <div>
        <h3>
	       <font color=grey>Access edit</font>
        </h3>
<?php
// After user pressed 'add user' button
$option = isset($_POST['addUser']) ? $_POST['addUser'] : false;
   if ($option) {
        list($add_new_user_id, $add_new_user_type) = explode("_", htmlentities($_POST['addUser'], ENT_QUOTES, "UTF-8"));
        $sql = "INSERT INTO $all_modules_table_name (module_name, module_hash, access_user_id, access_user_type, student_list_hash) VALUES ('$module_name', '$module_hash', '$add_new_user_id', '$add_new_user_type', '$student_list_hash')";
        if ($conn->query($sql) === TRUE) {
            echo "";
        } else {
            die ("Error creating table: " . $conn->error);
        }
        
   } else {
     echo "";
   }
    
?>
        
        <div style="width:50%">
            <table class="table" style="border:solid lightgrey 0.5px;">
                <thead>
                    <tr>
                        <th>Full name</th>
                        <th>Email</th>
                        <th>User type</th>
                        <th>User id</th>
                    </tr>
                </thead>
                <tbody>
<?php
// Get TAs and Lecturers that have access to this module
$sql = "SELECT num, access_user_id, access_user_type
    FROM $all_modules_table_name
    WHERE module_hash='$module_hash'";
$big_resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
while($big_row = mysqli_fetch_assoc($big_resultset)) {
    print '<tr num="' . $big_row['num'] . '">';
        //print '<td>' . $big_row['id'] . '</td>';
        // Get user name and email from credentials table
        $user_id = $big_row['access_user_id'];
        $sql = "SELECT firstname, lastname, email
            FROM $credentials_table_name
            WHERE user_id='$user_id'";
        $resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
        $row = mysqli_fetch_assoc($resultset);
        $full_name = $row['firstname'] .' '. $row['lastname'];
        $email = $row['email'];

        print '<td>'. $full_name .'</td>';
        print '<td>'. $email .'</td>';
        print '<td>' . $big_row['access_user_type'] . '</td>';
        print '<td>' . $user_id . '</td>';
    print '</tr>';
}
?>
                </tbody>
            </table>
        </div>
        <font color=grey>Add new:</font>
        <form method="post">
            <select name="addUser"> 
                <option value="0">Select</option>
<?php
// Check if users already have access, if not, diplay as option to add
$sql = "SELECT user_id, firstname, lastname, email, user_type
    FROM $credentials_table_name
    WHERE user_id NOT IN (
        SELECT access_user_id
        FROM $all_modules_table_name
        WHERE module_hash='$module_hash'
    )
    AND (user_type='TA' OR user_type='Lecturer')";
$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
while($row = mysqli_fetch_assoc($resultset)) {
    print '<option value="'. $row['user_id'] .'_'. $row['user_type']. '">'. $row['user_type'] .', '. $row['firstname'] .' '. $row['lastname'] .', '. $row['email'] .', '. $row['user_id'] .'</option> '; 
}
?>
            </select>
            <input type="submit" value="Add user"/>
        </form>
<!--        <button>Add</button>-->
    </div>
<?php
}
?>
    <div style="margin:30px 30px 0px 0px;">
        <form action="inc/export_csv.php" method="post">
            <input type='hidden' name='export_module_name' value='<?php echo $module_name;?>' />
            <input type='hidden' name='export_module_hash' value='<?php echo $module_hash;?>' />
            <input type="submit" name="export_module" value="Save module as .csv file"/>
        </form>
    </div>
    <hr/>
     <h3>
        <font color=grey>List of students:</font>
    </h3>
    <table class="table table-stiped">
        <thead>
            <tr>
                <th>Student id</th>
                <th>First name</th>
                <th>Last name</th>
                <th>Email</th>
                <th>Course code</th>
                <th>Year</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $sql = "SELECT student_id, firstname, lastname, email, course_code, year FROM $student_list_hash";
            $resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
            while($row = mysqli_fetch_assoc($resultset)) {
                print '<tr>';
                    print '<td>'. $row['student_id'] .'</td>';
                    print '<td>'. $row['firstname'] .'</td>';
                    print '<td>' . $row['lastname'] . '</td>';
                    print '<td>' . $row['email'] . '</td>';
                    print '<td>' . $row['course_code'] . '</td>';
                    print '<td>' . $row['year'] . '</td>';
                print '</tr>';
            }

        ?>
       </tbody>
    </table>
    <script 
            type="text/javascript" 
            src="js/module_edit_helper.js">
    </script>
<?php
include("inc/footer.php");
?>