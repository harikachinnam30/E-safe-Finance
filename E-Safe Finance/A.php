<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            session_start();
            if($_SESSION["e-usr"]) {
                header("Location: employee.php");
            }
            if($_SESSION["username"]) {
                $conn = new mysqli("localhost","root","","E-Safe Finance");
                $sql = "select * from UserAccounts where username='".$_SESSION["username"]."';";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                if($row["status"]!="working") {
                    $conn->close();
                    header("Location: status.php");
                }
                $sql = "select * from UserDetails where username='".$_SESSION["username"]."';";
                $result = $conn->query($sql);
                $row1 = $result->fetch_assoc();
                $sql = "select * from ContactDetails where username='".$_SESSION["username"]."';";
                $result = $conn->query($sql);
                $row2 = $result->fetch_assoc();
                $conn->close();
                $profile = "data:image;base64,".base64_encode($row1["profile"])."";
            }
        ?>
        <title>E-Safe Finance</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="img/icon.png" type="image/x-icon">
        <script>
            function toggle_dialog() {
                let dialog = document.getElementsByClassName("dialog")[0].style.display;
                if(dialog=="none") {
                    document.getElementsByClassName("dialog")[0].style.display = "block";
                } else {
                    document.getElementsByClassName("dialog")[0].style.display = "none";
                }
            }
        </script>
        <style>
            body {
                background-color : green;
            }
            .container {
                background-color : white;
                position : absolute;
                top : 50%;
                left : 50%;
                transform : translate(-50%, -50%);
                border-radius : 10px;
                width : 98vw;
                height : 96vh;
            }
            .navigator {
                display : flex;
                flex-direction : row-reverse;
                background-color : lightblue;
                position : fixed;
                width : 100%;
                border-top-left-radius : 10px;
                border-top-right-radius : 10px;
                min-height : 50px;
                align-items : center;
            }
            #signin {
                display : none;
                height: 40px;
                border: 1px solid black;
                border-radius:20px;
                margin-right:20px;
                cursor : pointer;
                align-items : center;
            }
            #profile {
                display : none;
                width: 40px;
                height: 40px;
                border: 1px solid black;
                border-radius:20px;
                margin-right:20px;
                cursor : pointer;
            }
            .dialog {
                display : none;
                border-radius : 10px;
                position : fixed;
                top : 70px;
                right : 20px;
                background-color : pink;
                z-index : 1;
                padding : 10px 50px;
            }
            .dialog button {
                border : none;
                border-radius : 5px;
                width : 46%;
                padding : 5px 1%;
                cursor : pointer;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="navigator">
                <div id="profile" onclick="toggle_dialog()">
                    <img src="<?php echo $profile; ?>" alt="Profile" width="40px" height="40px" style="border: 1px solid black; border-radius: 20px;">
                </div>
                <div id="signin" onclick="window.location='auth'">
                    <img src="img/avatar.png" alt="Avatar" width="40px" height="40px" style="border: 1px solid black; border-radius: 20px;">
                    <label>Sign In</label>
                </div>
            </div>
        </div>
        <div class="dialog">
            <h3>Personal Info:</h3>
            <p><b>Name : </b><?php echo $row1["Name"];?></p>
            <p><b>Date Of Birth : </b><?php echo $row1["DOB"]?></p>
            <p><b>Phone No. : </b><?php echo $row2["phone"]?></p>
            <button onclick="window.location='edit.php'">Edit</button>
            <button onclick="window.location='auth/logout.php'" style="background-color:red; color:white;">Sign Out</button>
        </div>
    </body>
    <script>
            <?php
                if($_SESSION["username"]) {
                    echo "document.getElementById('profile').style.display = 'block';";
                } else {
                    echo "document.getElementById('signin').style.display = 'flex';";
                }
            ?>
    </script>
</html>