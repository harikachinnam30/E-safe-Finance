<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editing</title>
        <link rel="icon" href="img/icon.png" type="image/x-icon">
        <?php
            date_default_timezone_set("Asia/Kolkata");
            session_start();
            if($_SESSION["e-usr"]) {
                header("Location: employee.php");
            }
            if($_SESSION["username"]) {
                $conn = new mysqli("localhost","root","","E-Safe Finance");
                if($_SERVER["REQUEST_METHOD"]=="POST") {
                    $success = 1;
                    $a = false;
                    $sql = "You had changed your";
                    if($_POST["checkPasswd"]=="on") {
                        if(strlen($_POST["passwd"])>7) {
                            $conn->query("update Auth set password='".$_POST["passwd"]."' where username='".$_SESSION["username"]."';");
                            $sql = $sql." password,";
                            $a = true;
                        } else {
                            echo "<script>window.alert('Password must contain atleast 8 characters!');</script>";
                            $success = 0;
                        }
                    }
                    if($_POST["checkProfile"]=="on") {
                        if(getimagesize($_FILES["profile"]["tmp_name"])) {
                            if($_FILES["profile"]["size"]<10000000) {
                                $profile = addslashes(file_get_contents($_FILES["profile"]["tmp_name"]));
                                $conn->query("update UserDetails set profile='".$profile."' where username='".$_SESSION["username"]."';");
                                $sql = $sql." profile,";
                                $a = true;
                            } else {
                                echo "<script>window.alert('Image size should be less than 10MB');</script>";
                                $success = 0;
                            }
                        } else {
                            echo "<script>window.alert('File should be an image');</script>";
                            $success = 0;
                        }
                    }
                    if($_POST["checkMobile"]=="on") {
                        $conn->query("update ContactDetails set phone='".$_POST["mobile"]."' where username='".$_SESSION["username"]."';");
                        $sql = $sql." phone number,";
                        $a = true;
                    }
                    if($_POST["checkEmail"]=="on") {
                        $conn->query("update ContactDetails set email='".$_POST["email"]."' where username='".$_SESSION["username"]."';");
                        $sql = $sql." email,";
                        $a = true;
                    }
                    if($_POST["checkAddress"]=="on") {
                        if(getimagesize($_FILES["address_proof"]["tmp_name"])) {
                            if($_FILES["address_proof"]["size"]<10000000) {
                                $address = addslashes(file_get_contents($_FILES["address_proof"]["tmp_name"]));
                                $conn->query("update ContactDetails set address='".$_POST["address"]."', address_proof='".$address."' where username='".$_SESSION["username"]."';");
                                $sql = $sql." address,";
                                $a = true;
                            } else {
                                echo "<script>window.alert('Image size should be less than 10MB');</script>";
                                $success = 0;
                            }
                        } else {
                            echo "<script>window.alert('File should be an image');</script>";
                            $success = 0;
                        }
                    }
                    $sql = "insert into UserActivity values('".$_SESSION["username"]."','".substr_replace($sql,".",-1)."','".date("Y-m-d H:i:s")."');";
                    if($a)
                        $conn->query($sql);
                    if($success)
                        header("Location: ../E-Safe Finance");
                }
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
            } else {
                header("Location: auth/");
            }
        ?>
        <style>
            @keyframes PrimaryColorChange {
                0% { background-color : rgb(100,64,255); }
                50% { background-color : rgb(255,100,255) }
                100% { background-color : rgb(100,64,255); }
            }
            @keyframes SecondaryColorChange {
                0% { background-color : rgb(0,0,127); color : white; }
                25% { background-color : rgb(0,127,255); }
                50% { background-color : rgb(0,185,255); color : black; }
                75% { background-color : rgb(0,127,255); }
                100% { background-color : rgb(0,0,127); color : white; }
            }
            body {
                animation-name : PrimaryColorChange;
                animation-duration : 6s;
                animation-iteration-count : infinite;
            }
            form {
                background-color : white;
                border-radius : 15px;
                border-top : 5px solid rgb(178,82,255);
                position: absolute;
                left : 50%;
                transform: translateX(-50%);
                width: 50vw;
                min-width: 500px;
                padding : 30px 10px;
                display : flex;
                flex-direction : column;
                align-items : center;
                justify-content : space-evenly;
            }
            .form-items {
                display : flex;
                justify-content : space-between;
                width : 60%;
                margin : 20px 0;
            }
            label {
                cursor : pointer;
            }
            input[type="submit"] {
                border : none;
                border-radius : 10px;
                padding : 20px 50px;
                margin : 30px 0;
                font-size : 1.2em;
                animation-name : SecondaryColorChange;
                animation-duration : 9s;
                animation-iteration-count : infinite;
            }
            input[type="tel"], input[type="email"], input[type="password"] {
                height : 30px;
                width : 215px;
            }
            input {
                outline-color : rgb(178,82,255);
                accent-color : rgb(178,82,255);
            }
            textarea {
                resize : none;
                outline-color : rgb(178,82,255);
            }
        </style>
        <script>
            function toggle_check() {
                if(document.getElementById("passwd1").checked) {
                    document.getElementById("passwd2").disabled = false;
                    document.getElementById("passwd2").required = true;
                } else {
                    document.getElementById("passwd2").disabled = true;
                    document.getElementById("passwd2").required = false;
                }
                if(document.getElementById("profile1").checked) {
                    document.getElementById("profile2").disabled = false;
                    document.getElementById("profile2").required = true;
                } else {
                    document.getElementById("profile2").disabled = true;
                    document.getElementById("profile2").required = false;
                }
                if(document.getElementById("mobile1").checked)
                    document.getElementById("mobile2").disabled = false;
                else {
                    document.getElementById("mobile2").disabled = true;
                    document.getElementById("mobile2").value ="<?php echo $row2["phone"]; ?>";
                }
                if(document.getElementById("email1").checked)
                    document.getElementById("email2").disabled = false;
                else {
                    document.getElementById("email2").disabled = true;
                    document.getElementById("email2").value ="<?php echo $row2["email"]; ?>";
                }
                if(document.getElementById("address1").checked) {
                    document.getElementById("address2").disabled = false;
                    document.getElementById("address3").style.display = "inline-block";
                    document.getElementById("address3").required = true;
                } else {
                    document.getElementById("address2").disabled = true;
                    document.getElementById("address2").value =`<?php echo $row2["address"]; ?>`;
                    document.getElementById("address3").style.display = "none";
                    document.getElementById("address3").required = false;
                }
            }
        </script>
    </head>
    <body>
        <form action="?" method="POST" enctype="multipart/form-data">
            <h2>Edit Request Form</h2>
            <p>Please select the section you wanna change</p>
            <div class="form-items">
                <div>
                    <input type="checkbox" name="checkPasswd" id="passwd1" onclick="toggle_check()">
                    <label for="passwd1">Change the Password</label>
                </div>
                <input type="password" name="passwd" id="passwd2" disabled>
            </div>
            <div class="form-items">
                <div>
                    <input type="checkbox" name="checkProfile" id="profile1" onclick="toggle_check()">
                    <label for="profile1">Change the Profile</label>
                </div>
                <input type="file" name="profile" id="profile2" disabled>
            </div>
            <div class="form-items">
                <div>
                    <input type="checkbox" name="checkMobile" id="mobile1" onclick="toggle_check()">
                    <label for="mobile1">Change the Phone Number</label>
                </div>
                <input type="tel" name="mobile" id="mobile2" pattern="\d{10}" value="<?php echo $row2['phone']; ?>" required disabled>
            </div>
            <div class="form-items">
                <div>
                    <input type="checkbox" name="checkEmail" id="email1" onclick="toggle_check()">
                    <label for="email1">Change the Email</label>
                </div>
                <input type="email" name="email" id="email2" value="<?php echo $row2['email']; ?>" required disabled>
            </div>
            <div class="form-items">
                <div>
                    <input type="checkbox" name="checkAddress" id="address1" onclick="toggle_check()">
                    <label for="address1">Change the Address</label>
                </div>
                <textarea name="address" id="address2" cols="25" rows="5" required disabled><?php echo $row2['address']; ?></textarea>
            </div>
            <input type="file" name="address_proof" id="address3" style="display:none;">
            <div style="text-align:center;">
                <input type="submit" value="Submit">
            </div>
        </form>
    </body>
</html>