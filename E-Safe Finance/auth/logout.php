<?php
    date_default_timezone_set("Asia/Kolkata");
    session_start();
    if($_SESSION["username"]) {
        $sql = "insert into UserActivity(username, description, DNT) values('".$_SESSION['username']."','You had logged out of your account.','".date("Y-m-d H:i:s")."');";
        $conn = new mysqli("localhost", "root", "", "E-Safe Finance");
        $conn->query($sql);
        $conn->close();
    }
    session_unset();
    session_destroy();
    header("Location: ../");
?>