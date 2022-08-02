<?php
session_start();

if( !$_SESSION['loggedInAdmin'] ) {
    
    // send them to the login page
    header("Location: index.php");
}

// if user is not logged in
if( !$_SESSION['loggedInUser'] ) {
    
    // send them to the login page
    header("Location: ../clockinout.php");
}

// get ID sent by GET collection
$userEmployee = $_SESSION['loggedInEmployee'];
$employeeId = $_SESSION['loggedInUserId'];

// connect to database
include('../includes/connection.php');

// include functions file
include('../includes/functions.php');

// query the database with client ID
$query = "SELECT status FROM employees WHERE id_employee='$employeeId'";
$result = mysqli_query( $conn, $query );

// if result is returned
if( mysqli_num_rows($result) > 0 ) {
    
    // we have data!
    // set some variables
    while( $row = mysqli_fetch_assoc($result) ) {
        $userStatus    = $row['status'];
    }
} else { // no results returned
    $alertMessage = "<div class='alert alert-warning'>Nothing to see here. <a href='clockinout.php'>Head back</a>.</div>";
} //end employee result

		date_default_timezone_set('America/Los_Angeles');
        $date = date("Y-m-d");
		//time detail one
		$querydetailone = "SELECT * FROM timedetail WHERE (id_employee='$employeeId') && (timedetail_status='1') && (date_set='$date')";
		$resultdetailone = mysqli_query( $conn, $querydetailone );
		
		
		if( mysqli_num_rows($resultdetailone) > 0 ) {
		    
		    // we have data!
		    // set some variables
		    while( $row = mysqli_fetch_assoc($resultdetailone) ) {
		    	
		        $timeInOne    = $row['time_in_one'];
		        $currentStatus = $row['timedetail_status'];
		        $calcTimeOne = $row['calc_time_one'];
		        $regularTime  = $row['regular_time'];
		        $overTime     = $row['over_time'];
		        $totalTime    = $row['total_time'];
		    }
		    
		    // if update button was submitted
				if( isset($_POST['update']) ) {
				    
				    //default timezone
				    date_default_timezone_set('America/Los_Angeles');
				    // set variables
				    $employeeStatus    = "0";
				    
				    $timeInOne = validateFormData( $_POST['time_in_one'] );
				    $timeOutOne = date('H:i');
				    $calcTimeOne = validateFormData( $_POST['calc_time_one'] );
				    
				    // new database query & result
				    //update user
				    $query = "UPDATE employees
				            SET status='$employeeStatus'
				            WHERE id_employee='$employeeId'";
				    
				    $result = mysqli_query( $conn, $query );
						    
						    //update time detail one
						    $querytimeone = "UPDATE timedetail
						                SET time_out_one='$timeOutOne',
						                calc_time_one='$calcTimeOne',
						                total_time = total_time + '$calcTimeOne',
						                regular_time = IF( total_time <= 8.00, total_time, 8.00),
						                over_time = IF( total_time > 8.00, total_time - 8.00, 0.00)
						                WHERE (id_employee='$employeeId') && (timedetail_status = '1')";
						    
						    $resulttimeone = mysqli_query( $conn, $querytimeone );
							//end time detail
				    
				    if( $result ) {
				        
				        // redirect to client page with query string
				        header("Location: ../clockinout.php");
				    } else {
				        echo "Error updating record: " . mysqli_error($conn); 
				    }
				} //end update
		    }
		    // end if time detail = 1 result
		    //end time detail one
		    

		date_default_timezone_set('America/Los_Angeles');
        $date = date("Y-m-d");
		//time detail two
		$querydetailtwo = "SELECT * FROM timedetail WHERE (id_employee='$employeeId') && (timedetail_status= '2') && (date_set='$date')";
		$resultdetailtwo = mysqli_query( $conn, $querydetailtwo );
		
		// if time detail = 2 result
		if( mysqli_num_rows($resultdetailtwo) > 0 ) {
		    
		    // we have data!
		    // set some variables
		    while( $row = mysqli_fetch_assoc($resultdetailtwo) ) {
		        $timeInTwo   = $row['time_in_two'];
		        $currentStatus = $row['timedetail_status'];
		        $calcTimeTwo = $row['calc_time_two'];
		        $regularTime  = $row['regular_time'];
		        $overTime     = $row['over_time'];
		        $totalTime    = $row['total_time'];
		    }
		    
		    // if update button was submitted
				if( isset($_POST['update']) ) {
				    
				    //default timezone
				    date_default_timezone_set('America/Los_Angeles');
				    // set variables
				    $employeeStatus    = "0";
				    
				    $timeInTwo = validateFormData( $_POST["time_in_two"] );
				    $timeOutTwo = date('H:i');
				    $timeDetailStatus = '2';
				    $calcTimeTwo = validateFormData( $_POST["calc_time_two"] );
				    
				    // new database query & result
				    //update user
				    $query = "UPDATE employees
				            SET status='$employeeStatus'
				            WHERE id_employee='$employeeId'";
				    
				    $result = mysqli_query( $conn, $query );
						    
						    //update time detail one
						    $querytimetwo = "UPDATE timedetail
						                SET time_out_two='$timeOutTwo',
						                calc_time_two='$calcTimeTwo',
						                timedetail_status='$timeDetailStatus',
						                total_time = total_time + '$calcTimeTwo',
						                regular_time = IF( total_time <= 8.00, total_time, 8.00),
						                over_time = IF( total_time > 8.00, total_time - 8.00, 0.00)
						                WHERE (id_employee='$employeeId') && (timedetail_status = '2')";
						    
						    $resulttimetwo = mysqli_query( $conn, $querytimetwo );
							//end time detail

				    
				    if( $result ) {
				        
				        // redirect to client page with query string
				        header("Location: ../clockinout.php");
				    } else {
				        echo "Error updating record: " . mysqli_error($conn); 
				    }
				} //end update  
			} //  end if time detail = 2 result


// close the mysql connection
mysqli_close($conn);

include('../includes/header.php');
?>

        <div id="container">
            <h1 id="Date"></p>
            <h3 class="">Good Bye <?php echo $_SESSION['loggedInUser']; ?>!</h3>
            <h3 class="">Employee #: <?php echo $_SESSION['loggedInEmployee']; ?>!</h3>
            <p id="Date"></p>
            
            <form action="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] ); ?>?id_employee=<?php echo $employeeId; ?>" method="POST" class="row">
               <?php 
               
               if ($currentStatus == 1){
               	?>
               	<h4 class="text-center">Time In #: <?php echo $timeInOne; ?>!</h4>
               	<input type="hidden" class="Time1" id="time_in_one" name="timeinone" value="<?php echo $timeInOne; ?>">
                <input type="hidden" class="Time2" id="time_out_one" name="time_out_one" value="<?php date_default_timezone_set('America/Los_Angeles'); echo date("H:i"); ?>" />
               <input type="hidden" class="form-control input-lg Hours" id="calc_time_one" name="calc_time_one" value="<?php echo $calcTimeOne; ?>">

				<?php
               }
               
               if ($currentStatus == 2){
               	?>
               	<h4 class="text-center">Time In #: <?php echo $timeInTwo; ?>!</h4>
				<input type="hidden" class="Time1" id="time_in_two" name="time_in_two" value="<?php echo $timeInTwo; ?>">
                <input type="hidden" class="Time2" id="time_out_two" name="time_out_two" value="<?php date_default_timezone_set('America/Los_Angeles'); echo date("H:i"); ?>" />
               <input type="hidden" class="form-control input-lg Hours" id="calc_time_two" name="calc_time_two" value="<?php echo $calcTimeTwo; ?>">
				
				<?php
				}
               ?>
               <h4 class="text-center">Time Out: <?php date_default_timezone_set('America/Los_Angeles'); echo date("H:i"); ?>!</h4>
            
               
                       <hr>
             <!-- visibility:hidden -->
               
                <button type="submit" class="btn btn-lg btn-danger" name="update">CLOCK OUT</button>
            </form>
        
        </div>
    
<?php
     include('../includes/footer.php');   
?>