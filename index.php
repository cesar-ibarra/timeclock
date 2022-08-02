<?php
session_start();

include('includes/functions.php');

// if login form was submitted
if( isset( $_POST['login'] ) ) {
    
    // create variables
    // wrap data with validate function
    $formUsername = validateFormData( $_POST['username'] );
    $formPass = validateFormData( $_POST['password'] );
    hash('sha256', $_POST['ppasscode']);
    
    // connect to database
    include('includes/connection.php');
    
    // create query
    $query = "SELECT employee, username, password, privileges, fullname FROM administrator WHERE username='$formUsername'";
    
    // store the result
    $result = mysqli_query( $conn, $query );
    
    // verify if result is returned
    if( mysqli_num_rows($result) > 0 ) {
        
        // store basic user data in variables
        while( $row = mysqli_fetch_assoc($result) ) {
            $employee   = $row['employee'];
            $fullname = $row['fullname'];
            $hashedPass = $row['password'];
            $admin = $row['privileges'];
        }
        
        // verify hashed password with submitted password
        if( password_verify( $formPass, $hashedPass ) ) {
            
            // correct login details!
            // store data in SESSION variables
            $_SESSION['loggedInAdmin'] = $employee;
            $_SESSION['loggedInUserName'] = $fullname;
            $_SESSION['adminuser'] = $admin;
          
            // redirect user to sor page
            
            if( $_SESSION['adminuser']) {
                
                header( "Location: clockinout.php" );
            }
            
            
            
        } else { // hashed password didn't verify
            
            // error message
            $loginError = "<div class='alert alert-danger'>Wrong username / password combination. Try again.</div>";
        }
        
    } else { // there are no results in database
        
        // error message
        $loginError = "<div class='alert alert-danger'>No such user in database. Please try again. <a class='close' data-dismiss='alert'>&times;</a></div>";
    }
    
}

// close mysql connection
mysqli_close($conn);
//$password = password_hash("", PASSWORD_DEFAULT);
//echo $password;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Special Order Request</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>

  <div class="loginbox">
      <img src="img/patriotlogo.png" class="avatar">
      <?php echo $loginError; ?>
      <h1>Log in to your account.</h1>
      <form action="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'] ); ?>" method="post">
        
        <p>Username</p>
        <input type="text" id="login-username" placeholder="username" name="username" value="<?php echo $formUsername; ?>">
        
        <p>Password</p>
        <input type="password" class="form-control" id="login-password" placeholder="password" name="password">
        
        <input type="submit" name="login" value="Login">
        

      </form>
  </div>
</body>

<footer class="text-center footer">
    <hr>
    <small>Coded with &hearts; by <a href="http://cesar-ibarra.com/">Cesar</a> Ibarra</small>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</footer>
</html>