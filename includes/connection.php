<?php

//  $server     = "shareddb-g.hosting.stackcp.net";
//  $username   = "phtimecarddb-3731ce19";
//  $password   = "@98542124@";
//  $db         = "phtimecarddb-3731ce19";

$server     = "localhost";
 $username   = "root";
 $password   = "root";
 $db         = "phtimecarddb-3731ce19";

// create a connection
$conn = mysqli_connect( $server, $username, $password, $db );

// check connection
if( !$conn ) {
    die( "Connection failed: " . mysqli_connect_error() );
}

?>