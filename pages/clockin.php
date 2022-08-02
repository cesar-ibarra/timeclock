<?php
session_start();

// if user is not logged in
if( !$_SESSION['loggedInAdmin'] ) {
    
    // send them to the login page
    header("Location: ../index.php");
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

//employee result
// if result is returned
if( mysqli_num_rows($result) > 0 ) {
    
    // we have data!
    // set some variables
    while( $row = mysqli_fetch_assoc($result) ) {
        $userStatus    = $row['status'];
    }
} else { // no results returned
    $alertMessage = "<div class='alert alert-warning'>Nothing to see here. <a href='../clockinout.php'>Head back</a>.</div>";
}

//verify if you are intented clock in for 3rd time
    date_default_timezone_set('America/Los_Angeles');
    $date = date("Y-m-d");
    //insert timedetail
    $querydetail = "SELECT * FROM timedetail WHERE (id_employee='$employeeId') && (timedetail_status='2') && (date_set='$date')";
    $resultdetail = mysqli_query( $conn, $querydetail );
    if( mysqli_num_rows($resultdetail) > 0 ) {
        // redirect to clockinout.php page with query string
        header("Location: ../clockinout.php?alert=danger");
    }
    //end verification

// if update button was submitted
if( isset($_POST['update']) ) {
    
    // set variables
    $userStatus    = '1';
    
    // new database query & result
    $query = "UPDATE employees
            SET status='$userStatus'
            WHERE id_employee='$employeeId'";
    
    $result = mysqli_query( $conn, $query );
    
    date_default_timezone_set('America/Los_Angeles');
    $date = date("Y-m-d");
    //insert timedetail
    $querydetail = "SELECT * FROM timedetail WHERE (id_employee='$employeeId') && (timedetail_status!='') && (date_set='$date')";
    $resultdetail = mysqli_query( $conn, $querydetail );

    if( mysqli_num_rows($resultdetail) > 0 ) {

    
        while( $row = mysqli_fetch_assoc($resultdetail) ) {
            $timeintwo    = $row['time_in_two'];
            $detailStatus    = $row['timedetail_status'];
        }
        
        date_default_timezone_set('America/Los_Angeles');
        $timeintwo  = date("H:i");
        $detailStatus = "2";

        $queryd = "UPDATE timedetail
                SET time_in_two='$timeintwo',
                timedetail_status='$detailStatus'
                WHERE (id_employee='$employeeId') && (timedetail_status = '1')";
    
        $resultd = mysqli_query( $conn, $queryd );

    
    } else { // no results returned
        // $alertMessage = "<div class='alert alert-warning'>Nothing to see here. <a href='../clockinout.php'>Head back</a>.</div>";
        //timezone
 /*        echo 'No se encontro registro'; */
        date_default_timezone_set('America/Los_Angeles');
        $date = date("Y-m-d");
        $timeinone  = date("H:i");
        $timeoutone = "";
        $calctimeone = 0;
        $timeintwo  = "";
        $timeouttwo = "";
        $calctimetwo = 0;
        $totaltime = 0;
        $regulartime = 0;
        $overtime = 0;
        $detailStatus = "1";

        $queryDetail = "INSERT INTO timedetail (id_timedetail, date_set, time_in_one, time_out_one, calc_time_one, time_in_two, time_out_two, calc_time_two, total_time, regular_time, over_time, timedetail_status, id_employee) VALUES (NULL, '$date', '$timeinone', '$timeoutone', '$calctimeone', '$timeintwo', '$timeouttwo', '$calctimetwo', '$totaltime', '$regulartime', '$overtime', '$detailStatus', '$employeeId')";
        
        $resultDetail = mysqli_query( $conn, $queryDetail );
        
        
        // end insert timedetail
    }
    

    //end insert timedetail
    

    if( $result ) {
        
        // redirect to client page with query string
        header("Location: ../clockinout.php");
    } else {
        echo "Error updating record: " . mysqli_error($conn); 
    }
}



// close the mysql connection
mysqli_close($conn);

include('../includes/header.php');
?>

        <div id="container">
            <h1 id="Date"></h1>
            <h3 class="">Welcome <?php echo $_SESSION['loggedInUser']; ?></h3>
            <h4 class="">Employee #: <?php echo $_SESSION['loggedInEmployee']; ?></h4>
            <h4 class="">Time In: <?php date_default_timezone_set('America/Los_Angeles'); echo date("H:i"); ?></h4>
            <!-- <p class="">Welcome <?php echo $_SESSION['loggedInUserId']; ?>!</p> -->
            
            <form action="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] ); ?>?id_employee=<?php echo $employeeId; ?>" method="POST" class="row">
            <button type="submit" class="btn btn-lg btn-success" name="update">CLOCK IN</button>
            </form>
        </div>

    
<?php
    include('../includes/footer.php');   
?>