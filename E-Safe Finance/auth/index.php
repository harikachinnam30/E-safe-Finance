<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            date_default_timezone_set("Asia/Kolkata");
            session_start();
            if($_SESSION["username"]) {
                header("Location: ../");
            }
            if($_SESSION["e-usr"]) {
                header("Location: ../employee.php");
            }
            $conn = new mysqli("localhost", "root", "", "E-Safe Finance");
            if($_SERVER["REQUEST_METHOD"]=="POST") {
                if(empty($_POST["passwd"])) {
                    if($_POST["passwd1"]==$_POST["passwd2"]) {
                        if(strlen($_POST["passwd1"])>7) {
                            $sql = "select password from Auth where username='".$_POST["username"]."';";
                            $result = $conn->query($sql);
                            if($result->num_rows==0) {
                                $sql = "insert into Auth(username, password) values('".$_POST["username"]."', '".$_POST["passwd1"]."');";
                                $_SESSION["sql"] = $sql;
                                $_SESSION["t-usr"] = $_POST["username"];
                                header("Location: open.php");
                            } else {
                                echo "<script>window.alert('UserName is already taken. Try Another!');</script>";
                            }
                        } else {
                            echo "<script>window.alert('Password must contain atleast 8 characters!');</script>";
                        }
                    } else {
                        echo "<script>window.alert('Passwords didn\'t match!');</script>";
                    }
                } else {
                    if(strlen($_POST["passwd"])>7) {
                        $sql = "select password from Auth where username='".$_POST["username"]."';";
                        $result = $conn->query($sql);
                        if($result->num_rows==1) {
                            $row = $result->fetch_assoc();
                            if($row["password"]==$_POST["passwd"]) {
                                $sql = "insert into UserActivity(username, description, DNT) values('".$_POST['username']."','You had logged into your account.','".date("Y-m-d H:i:s")."');";
                                $conn->query($sql);
                                $conn->close();
                                $_SESSION["username"] = $_POST["username"];
                                header("Location: ../");
                            } else {
                                echo "<script>window.alert('UserName or Password is invalid!');</script>";
                            }
                        } else {
                            echo "<script>window.alert('UserName or Password is invalid!');</script>";
                        }
                    } else {
                        echo "<script>window.alert('Password must contain atleast 8 characters!');</script>";
                    }
                }
            }
            $conn->close();
        ?>
        <title>Authentication</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../img/icon.png" type="image/x-icon">
        <link rel="stylesheet" href="style.css">
        <script src="script.js"></script>
        <link rel="stylesheet" href="../loader.css">
        <script src="../loader.js"></script>
    </head>
    <body>
        <div class="loader">
            <img src="../img/auth-loader.gif" alt="Loading">
        </div>
        <div id="login">
            <div class="con">
                <div class="psv-con" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                    
                </div>
                <div class="atv-con" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px;">
                    <img src="../img/logo.png" alt="LOGO" style="margin: 5% 0 0 0;" width="200px" height="200px">
                    <h2>WELCOME ACCOUNT HOLDER</h2>
                    <p>Enter your email and password to access your account</p>
                    <form action="?" method="post">
                        <div class="form-con">
                            <div class="form-items">
                                <label for="username">Enter your username</label>
                                <input type="text" name="username" id="username" required>
                            </div>
                            <div class="form-items">
                                <label for="passwd">Enter your password</label>
                                <input type="password" name="passwd" id="passwd" required>
                            </div>
                            <div class="form-items" style="justify-content: center;">
                                <input type="submit" value="Login">
                            </div>
                            <div class="form-items" style="justify-content: center;">
                                <p>Doesn't have any account? <span style="color: blue; cursor: pointer;" onclick="window.location.hash='#signup'">open one</span></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="signup">
            <div class="con">
                <div class="atv-con" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                    <img src="../img/logo.png" alt="LOGO" style="margin: 5% 0 0 0;" width="200px" height="200px">
                    <h2>WELCOME JOINER</h2>
                    <p>Give your email and a password to open an account</p>
                    <form action="?" method="post">
                        <div class="form-con">
                            <div class="form-items">
                                <label for="C-username">Enter your username</label>
                                <input type="text" name="username" id="C-username" required>
                            </div>
                            <div class="form-items">
                                <label for="passwd1">Enter your password</label>
                                <input type="password" name="passwd1" id="passwd1" required>
                            </div>
                            <div class="form-items">
                                <label for="passwd2">Confirm your password</label>
                                <input type="password" name="passwd2" id="passwd2" required>
                            </div>
                            <div class="form-items" style="justify-content: center;">
                                <input type="submit" value="Proceed">
                            </div>
                            <div class="form-items" style="justify-content: center;">
                                <p>Have an account? <span style="color: blue; cursor: pointer;" onclick="window.location.hash='#login'">Go for login</span></p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="psv-con" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px;">
                    
                </div>
            </div>
        </div>
        <div id="employee">
            <div class="container">
            <form action="../employee.php?" method="post">
                <div class="form-con">
                    <div class="form-items">
                        <label for="E-username">Enter your username</label>
                        <input type="text" name="username" id="E-username" required>
                    </div>
                    <div class="form-items">
                        <label for="E-passwd">Enter your password</label>
                        <input type="password" name="passwd" id="E-passwd" required>
                    </div>
                    <div class="form-items" style="justify-content: center;">
                        <input type="submit" value="Login">
                    </div>
                </div>
            </form>
            </div>
        </div>
        <script>
            window.addEventListener("load", loadingDone);
        </script>
    </body>
</html>