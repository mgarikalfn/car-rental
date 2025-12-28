<?php
function connect() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $databasename = "car_rental_system"; 

    $con = new mysqli($servername, $username, $password, $databasename);

    if ($con->connect_error) {
        die("Database connection failed: " . $con->connect_error);
    }

    return $con;
}
