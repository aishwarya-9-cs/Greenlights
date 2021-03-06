<?php 
include_once("inc/enable_debug.php");

include_once("inc/start_session.php");
include_once("inc/lecturer_check.php");
include_once("inc/db_connect.php");
include("inc/header.php");

if(isset($_POST["submit"]) && isset($_POST['student_list_option']) && isset($_POST['module_option'])) {
    echo "";
} else {
    echo "Please return to previous page";
    die();
}

// Get module name
if (isset($_POST['module_name'])) {
    $module_name = $_POST['module_name']; 
} else
    die("No module name");

// STUDENT LIST AREA
// If student list clone option was selected
if ($_POST['student_list_option'] != '0') {
    $student_list_hash = $_POST['student_list_option'];
}
// Populate student table with data from file
<<<<<<< HEAD
if(isset($_POST["submit"])) {
    if (isset($_FILES["file"])) {
        // Check for errors
        if ($_FILES["file"]["error"] > 0) {
            echo "There was an error uploading the file. Return Code: " . $_FILES["file"]["error"] . "<br />";
            die();
        }
        // Check if file already uploaded
        if (file_exists("upload/" . $_FILES["file"]["name"])) {
            echo $_FILES["file"]["name"] . " already exists. ";
            die();
        }
        
        
        
        if($_FILES['file']['name']) {
            $filename = explode(".", $_FILES['file']['name']);
            if($filename[1] == 'csv') {
                $temp = $_FILES["file"]["tmp_name"];
                $file = new SplFileObject($temp);
                $file->setFlags(SplFileObject::READ_CSV);
                $csv = new LimitIterator($file, 1); // Skips first row
                
       
       // Check if the number of columns are correct
        $file=fopen($_FILES["file"]["tmp_name"], "r");
        $columns=fgetcsv($file);
        $num_columns=count($columns);
        if ($num_columns != 6)
            die ("Please check that your file only has 6 columns");
        
             
      // Check if all the columns have the same length       
      $lengthArray = array();
      $row = 1;
      if(($fp = fopen($_FILES["file"]["tmp_name"], "r")) !== FALSE) {
        while (($data = fgetcsv($fp, 1000, ";")) !== FALSE) {
            $lengthArray[] = count($data);
            $row ++;
            }
            fclose($fp);
        }
            
     $lengthArray = array_unique($lengthArray);
     if (count($lengthArray) == 1) 
        echo "Check done";
     else
        echo "please try again";
                
     //Check that the Student ID are of the correct length
     $file=fopen($_FILES["file"]["tmp_name"], "r");
    while (($line = fgetcsv($file)) !== FALSE) {
        
       $line=fgetcsv($file);
       $id=$line[0];
       $length=strlen($id);
       if ($length > 9 && $lenth < 8 )
           die ("Error in student ID. Please check your file");
    }
         
                
                
                
         //Insert Values      
                foreach ($csv as $row) {
                    $data = explode(";", $row[0]);
                    
                    $studentID = $data[0]; 
                    $firstname = $data[1];
                    $lastname = $data[2];
                    $email = $data[3];
                    $course_code = $data[4];
                    $course_year = $data[5];
=======
else if (isset($_FILES["file"])) {
    // Check for errors
    if ($_FILES["file"]["error"] > 0) {
        echo "Please upload a file. <br/>Return Code: " . $_FILES["file"]["error"] . "<br/>";
        die();
    }
    // Check if file already uploaded
    if (file_exists("upload/" . $_FILES["file"]["name"])) {
        echo $_FILES["file"]["name"] . " already exists. ";
        die();
    }
>>>>>>> 10f3d3f2b99536e40869473d8c258d917f43a929

    if($_FILES['file']['name']) {
        $filename = explode(".", $_FILES['file']['name']);
        if($filename[1] == 'csv') {
            $temp = $_FILES["file"]["tmp_name"];
            $file = new SplFileObject($temp);
            $file->setFlags(SplFileObject::READ_CSV);
            $csv = new LimitIterator($file, 1); // Skips first row
            
            // Generate a hash of the table for the module
            $to_hash = str_replace('.', '', str_replace(':', '', str_replace('-', '', str_replace(' ', '', date("Y-m-d H:i:s").microtime())))); //get accurate date
            $to_hash .= "studentstable";
            $to_hash .= $user_id; //add lecturer's id
            $student_list_hash = hash('sha256', $to_hash);
            $student_list_hash = substr($student_list_hash, 1);
            $student_list_hash = "l" . $student_list_hash;

            // Create student table named by student hash
            $sql = "CREATE TABLE $student_list_hash (
                student_id INT(9) UNSIGNED PRIMARY KEY,
                firstname VARCHAR(128) NOT NULL,
                lastname VARCHAR(128) NOT NULL,
                email VARCHAR(128) NOT NULL,
                course_code VARCHAR(10) NOT NULL,
                year SMALLINT(2) NOT NULL
            )";
            if ($conn->query($sql) === TRUE) {
                $success = true;
            } else {
                die ("Error creating table: " . $conn->error);
                $success = false;
            }
            
            // For each csv row
            foreach ($csv as $row) {
                $data = explode(";", $row[0]);

                $studentID = $data[0]; 
                $firstname = $data[1];
                $lastname = $data[2];
                $email = $data[3];
                $course_code = $data[4];
                $course_year = $data[5];

                $sql = "INSERT INTO $student_list_hash (student_id, firstname, lastname, email, course_code, year) VALUES ('$studentID','$firstname','$lastname','$email','$course_code','$course_year')";
                if ($conn->query($sql) === TRUE) {
                    $success = true;
                } else {
                    die ("Error creating table: " . $conn->error);
                    $success = false;
                }
            }
            if (!$success)
                throwError("Failed to save to file", $student_list_hash);
        } else {
            throwError("Filename not .csv", "");
        }
    } else {
        throwError("File not found", "");
    }
} else {
    throwError("FILES is not set", "");
}
// Function to throw error if there is a problem with students table
function throwError ($message, $hash) {
    if ($hash == "") {
        echo 'Error: '. $message;
    } else {
         include_once("db_connect.php");
        echo 'Error: '. $message;
        $sql = "DROP TABLE IF EXISTS $hash";
        $conn->query($sql);
    }
    $_POST = array();
    die();
}


// TASKS AREA
// Get options for cloning tasks
if ($_POST['module_option'] != '0') {
    $module_hash = $_POST['module_option'];
    $_POST = array();
?>
<form name='fr' action='LA_add_module_2.php' method='POST'>
    <input type='hidden' name='module_name' value='<?php echo $module_name; ?>'/>
    <input type='hidden' name='student_list_hash' value='<?php echo $student_list_hash; ?>'/>
    <input type='hidden' name='module_hash' value='<?php echo $module_hash; ?>'/>
</form>
<script type='text/javascript'>
    document.fr.submit();
</script>
<?php
die();
// Allow user to insert options
} else {
?>
<form class="insert_form" id="insert_form" method=post action="LA_add_module_2.php">
    <hr>
    <h1>
        <?php echo $module_name;?>
    </h1>
    <hr>
    <div class="input-field">
        <table class="table table-bordered" id="table_field">
            <input type='hidden' name='module_name' value='<?php echo $_POST['module_name'];?>' />
            <input type='hidden' name='student_list_hash' value='<?php echo $student_list_hash;?>' />
            <tr>
                <th>Week number</th>
                <th>Teaching Event</th>
                <th>Task</th>
                <th>Estimated time for a task (minutes)</th>
                <th>Group or Individual (G/I)</th>
                <th>Add/Remove row</th>
            </tr>
            <tr>
                <td><input class="form-control" type=text name=week[] required>  </td>     
                <td><input class="form-control" type=text name=session[]  required> </td> 
                <td><input class="form-control" type=text name=task[]  required length=50 > </td>
                <td><input class="form-control" type=text name=task_duration[]  required>  </td> 
                <td><input class="form-control" type=text name=task_type[]  required>  </td>  
                <td><input class="btn btn-warning" type=button name=add id=add value=Add>  </td>
            </tr>
        </table>
        <center>
            <input class="btn btn-success" type=submit name=submit id="submit" value=Submit> 
        </center>
    </div> 
</form>  
<script 
    type="text/javascript" 
    src="js/LA_custom_table_edit.js">
</script>
<?php
}
    include("inc/footer.php");
?>