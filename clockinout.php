<?php
session_start();

include('includes/functions.php');


if( !$_SESSION['loggedInAdmin'] ) {
    
    // send them to the login page
    header("Location: index.php");
}


// check for query string
if( isset( $_GET['alert'] ) ) {
        
    // error
    if( $_GET['alert'] == 'danger' ) {
        $alertMessage = "<div class='alert alert-danger'>You have exceeded the limit of clock in / clock out for the day. Please contact your Manager. <a class='close' data-dismiss='alert'>&times;</a></div>";
    } 
}


// if login form was submitted
if( isset( $_POST['clockinout'] ) ) {
    
    // create variables
    // wrap data with validate function
    $formEmployee = validateFormData( $_POST['employee_number'] );
    $formPass = validateFormData( $_POST['passcode'] );
    
    // connect to database
    include('includes/connection.php');
    
    // create query
    $query = "SELECT * FROM employees WHERE employee_number='$formEmployee'";
    
    // check for query string
    
    // store the result
    $result = mysqli_query( $conn, $query );
    
    // verify if result is returned
    if( mysqli_num_rows($result) > 0 ) {
        
        // store basic user data in variables
        while( $row = mysqli_fetch_assoc($result) ) {
            $employee_id = $row['id_employee'];
            $employee_name       = $row['employee_name'];
            $employee_lastname   = $row['employee_lastname'];
            $employee_number = $row['employee_number'];
            $hashedPass = $row['passcode'];
            $status     = $row['status'];
        }
        
        // verify hashed password with submitted password
        if( password_verify( $formPass, $hashedPass ) ) {
            
            // correct login details!
            // store data in SESSION variables
            $_SESSION['loggedInUser'] = $employee_name." ".$employee_lastname;
            $_SESSION['loggedInUserId'] = $employee_id;
            $_SESSION['loggedInStatus'] = $status;
            $_SESSION['loggedInEmployee'] = $employee_number;
            


        if (isset($_SESSION['loggedInStatus']) && $_SESSION['loggedInStatus'] == '0') {
            // redirect user to clients page
        header( "Location: pages/clockin.php" );
        } 
        else if (isset($_SESSION['loggedInStatus']) && $_SESSION['loggedInStatus'] == '1') {
            // redirect user to clients page
        header( "Location: pages/clockout.php" );
        }
            
        } else { // hashed password didn't verify
            
            // error message
            $loginError = "<div class='alert alert-danger'>Wrong Employee number / password combination. Try again.</div>";
        }
        
    } else { // there are no results in database
        
        // error message
        $loginError = "<div class='alert alert-danger'>No such user in database. Please try again. <a class='close' data-dismiss='alert'>&times;</a></div>";
    }
    
}

// close mysql connection
mysqli_close($conn);

include('includes/header.php');

// $password = password_hash("", PASSWORD_DEFAULT);
// echo $password;

?>
    <div id="container">
        <h1>ELECTRONIC TIMECLOCK</h1>
        <?php echo $loginError; ?>
        <?php echo $alertMessage; ?>
        <form action="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] ); ?>" method="post">
        <div>
                    <div class="clock">
                    <div id="Date"></div>
                        <ul>
                            <li id="hours"></li>
                            <li id="point">:</li>
                            <li id="min"></li>
                            <li id="point">:</li>
                            <li id="sec"></li>
                        </ul>
                    </div>

                    <p>Employee # 
                    <input type="text" id="input1" onkeypress="nextFocus('input1', 'input2')" name="employee_number" value="<?php echo $formEmployee; ?>">
                    </p>    
                    
                    <p>Password #
                    <input type="password" id="input2" onkeypress="nextFocus('input2', 'input3')" name="passcode">
                    </p>
                    
                <button type="submit" id="input3" name="clockinout">CLOCK IN / OUT</button>
        </div><!-- calculator -->
        </form>

    </div><!-- container -->
        
<?php
include('includes/footer.php');
?>