<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- <script>
            var usernames = new Array();
            var names = new Array();
            var dob = new Array();
            var dob_proofs = new Array();
            var gender = new Array();
            var profile = new Array();
            var phones = new Array();
            var emails = new Array();
            var address = new Array();
            var address_proofs = new Array();
            var balances = new Array();
            var ticcoins = new Array();
            var status = new Array();
            var types = new Array();
        </script> -->
        <?php
            date_default_timezone_set("Asia/Kolkata");
            session_start();
            $conn = new mysqli("localhost","root","","E-Safe Finance");
            if($_SERVER["REQUEST_METHOD"]=="POST") {
                $sql = "select * from Employee where username='".$_POST["username"]."'";
                $result = $conn->query($sql);
                if($result->num_rows==1) {
                    $row = $result->fetch_assoc();
                    if($_POST["passwd"]==$row["password"]) {
                        $_SESSION["e-usr"] = $_POST["username"];
                    } else {
                        echo "<script>window.alert('Invalid Credentials');\nwindow.location='auth';</script>";
                    }
                } else {
                    echo "<script>window.alert('Invalid Credentials');\nwindow.location='auth';</script>";
                }
            } else {
                if(!$_SESSION["e-usr"]) {
                    $conn->close();
                    header("Location: auth");
                }
            }
            $result = $conn->query("select role from Employee where username='".$_SESSION["e-usr"]."'");
            $role = $result->fetch_assoc()["role"];
            if(($_REQUEST["f"]||$_REQUEST["w"])&&$role=="Account Handler") {
                if($_REQUEST["f"]) {
                    $conn->query("update UserAccounts set status='freeze' where username='".$_REQUEST["uname"]."';");
                } else {
                    $conn->query("update UserAccounts set status='working' where username='".$_REQUEST["uname"]."';");
                }
            }
            if($_REQUEST['search']) {
                /*$sql = "select * from UserDetails where username like '%".$_GET['s']."%';";
                $userDetails = $conn->query($sql);
                $sql = "select * from ContactDetails where username like '%".$_GET['s']."%';";
                $contactDetails = $conn->query($sql);
                $sql = "select * from UserAccounts where username like '%".$_GET['s']."%';";
                $userAccounts = $conn->query($sql);*/
                //$users = $conn->query("select * from UserDetails inner join ContactDetails on UserDetails.username=ContactDetails.username inner join UserAccounts on UserDetails.username=UserAccounts.username where username like '%".$_GET["s"]."%'");
                $users = $conn->query("select username, Name from UserDetails where username like '%".$_REQUEST["search"]."%';");
            } else {
                if($role=="Account Handler") {
                    /*$sql = "select * from UserAccounts where status='not working'";
                    $UserAccounts = $conn->query($sql);
                    $sql = "select * from UserDetails inner join UserAccounts on UserDetails.username=UserAccounts.username where UserAccounts.status='not working'";
                    $userDetails = $conn->query($sql);
                    $sql = "select * from ContactDetails inner join UserAccounts on ContactDetails.username=UserAccounts.username where UserAccounts.status='not working'";
                    $contactDetails = $conn->query($sql);*/
                    //$users = $conn->query("select * from UserDetails inner join ContactDetails on UserDetails.username=ContactDetails.username inner join UserAccounts on UserDetails.username=UserAccounts.username where status='not working'");
                    $users = $conn->query("select username from UserAccounts where status='not working';");
                } elseif($role=="Loan Granter") {
                    /*$sql = "select * from UserAccounts where status like 'loanRequest%'";
                    $UserAccounts = $conn->query($sql);
                    $sql = "select * from UserDetails inner join UserAccounts on UserDetails.username=UserAccounts.username where UserAccounts.status like 'loanRequest%'";
                    $userDetails = $conn->query($sql);
                    $sql = "select * from ContactDetails inner join UserAccounts on ContactDetails.username=UserAccounts.username where UserAccounts.status like 'loanRequest%'";
                    $contactDetails = $conn->query($sql);*/
                    //$users = $conn->query("select * from UserDetails inner join ContactDetails on UserDetails.username=ContactDetails.username inner join UserAccounts on UserDetails.username=UserAccounts.username where status like 'loanRequest%'");
                    $users = $conn->query("select username from UserAccounts where status like 'loanRequest%';");
                } else {
                    //
                }
            }
            if($_REQUEST["uname"]) {
                $User = $conn->query("select * from UserDetails inner join ContactDetails on UserDetails.username=ContactDetails.username inner join UserAccounts on UserDetails.username=UserAccounts.username where UserDetails.username='".$_REQUEST["uname"]."';")->fetch_assoc();
            }
            $conn->close();
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>E-Safe Finance</title>
        <link rel="icon" href="img/icon.png" type="image/x-icon">
        <style>
            .heading {
                display : flex;
                background-image : linear-gradient(to right, rgb(0,180,240), rgb(0,240,180));
                padding : 0 50px;
                justify-content : space-between;
                align-items : center;
                height : 100px;
            }
            #body {
                position : absolute;
                top : 0;
                left : 0;
                width : 100vw;
                height : 100vh;
            }
            h1, .nav h3, .nav p {
                margin : 0;
            }
            button {
                color : white;
                padding : 15px 25px;
                background-color : red;
                border : none;
                border-radius : 10px;
                cursor : pointer;
            }
            .nav {
                float : left;
                width : 30%;
                height : calc(100% - 100px);
                background-color : #EEE;
            }
            .main {
                position : relative;
                float : left;
                width : 60%;
                height : calc(100% - 120px);
                background-color : #DDD;
                overflow : scroll;
                padding : 10px 5%;
            }
            input {
                width : 90%;
                padding : 5%;
                border : none;
                background-color : #CCC;
                outline-color : rgb(0,225,225);
                font-size : 1.5em;
            }
            .users {
                display : flex;
                flex-direction : column;
                padding : 10px;
            }
            .user {
                width : 90%;
                padding : 5%;
                margin : 10px 0;
                text-align : center;
                background-image : linear-gradient(to right, rgba(0,180,240,0.5), rgba(0,240,180,0.5));
                border-radius : 10px;
                font-size : 1.2em;
                cursor : pointer;
            }
            .profile {
                position : absolute;
                top : 50px;
                right : 50px;
                width : 200px;
            }
            .main div {
                display : flex;
                justify-content : space-evenly;
            }
        </style>
    </head>
    <body>
        <div id="body">
            <div class="heading">
                <h1><?php echo $role; ?></h1>
                <button onclick="window.location = 'auth/logout.php'">Logout</button>
            </div>
            <div class="nav">
                <input type="text" onkeyup="window.location='employee.php?search='+this.value" value="<?php echo $_REQUEST["search"]; ?>" placeholder="Enter the username" <?php if($_REQUEST["search"]) echo "autofocus onfocus='this.setSelectionRange(this.value.length, this.value.length);'"; ?>>
                <div class="users">
                    <?php
                        while($user=$users->fetch_assoc()) {
                            echo "<div class='user' onclick='window.location=\"employee.php?uname=".$user["username"]."\"'><h3>".$user["username"]."</h3>\n<p>".$user["Name"]."</p></div>";
                        }
                    ?>
                </div>
            </div>
            <div class="main">
                <?php
                    if($User) {
                        if($role=="Account Handler") {
                            echo "<h2>".$User["username"]."</h2>";
                            echo "<img src='data:image;base64,".base64_encode($User["profile"])."' class='profile'>";
                            echo "<p><b>Personal Info:</b></p><p>Name : ".$User["Name"]."</p><p>Date Of Birth : ".$User["DOB"]."</p><p>Gender : ".$User["gender"]."</p><p>Date Of Birth Proof,</p><img src='data:image;base64,".base64_encode($User["DOB_proof"])."' width='600px'>";
                            echo "<p><b>Contact Info:</b></p><p>Phone Number : ".$User["phone"]."</p><p>Email Address : ".$User["email"]."</p><p>Address : ".$User["address"]."</p><p>Address Proof,</p><img src='data:image;base64,".base64_encode($User["address_proof"])."' width='600px'>";
                            echo "<p><b>Account Info:</b></p><p>Account Type : ".$User["AccountType"]."</p><p>Balance : ".$User["Balance"]." <em>(sensitive info)</em></p><p>TicCoins : ".$User["TicCoin"]." <em>(sensitive info)</em></p><p>Status : ".$User["status"]."</p>";
                            echo "<div><button onclick='window.location=\"employee.php?uname=".$User["username"]."&f=1\"' style='background-color:blue'>freeze</button><button onclick='window.location=\"employee.php?uname=".$User["username"]."&w=1\"' style='background-color:green'>continue</button></div>";
                        } elseif($role=="Loan Granter") {
                            echo "<h2>".$User["username"]."</h2>";
                            echo "<img src='data:image;base64,".base64_encode($User["profile"])."' class='profile'>";
                            echo "<p>Name : ".$User["Name"]."</p><p>Loan Type : ".substr($User["status"],12,-1)."</p><p><b>Contact Info:</b></p><p>Phone Number : ".$User["phone"]."</p><p>Email Address : ".$User["email"]."</p>";
                        } else {
                            //
                        }
                    } else {
                        echo "Nothing to display";
                    }
                ?>
            </div>
        </div>
    </body>
</html>