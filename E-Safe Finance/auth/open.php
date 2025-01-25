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
                if(getimagesize($_FILES["profile"]["tmp_name"]) && getimagesize($_FILES["dob_proof"]["tmp_name"]) && getimagesize($_FILES["address_proof"]["tmp_name"])) {
                    if($_FILES["profile"]["size"]<10000000 && $_FILES["dob_proof"]["size"]<10000000 && $_FILES["address_proof"]["size"]<10000000) {
                        $today = new DateTime();
                        $dob = new DateTime($_POST["dob"]);
                        $age = $dob->diff($today)->y;
                        if($age>=18) {
                            $profile = addslashes(file_get_contents($_FILES["profile"]["tmp_name"]));
                            $dob_proof = addslashes(file_get_contents($_FILES["dob_proof"]["tmp_name"]));
                            $address_proof = addslashes(file_get_contents($_FILES["address_proof"]["tmp_name"]));
                            $conn->query($_SESSION["sql"]);
                            $usr = $_SESSION["t-usr"];
                            session_unset();
                            $_SESSION["username"] = $usr;
                            $sql = "insert into UserAccounts(username, AccountType, Balance, status, TicCoin) values('".$usr."','".$_POST["type"]."',0,'not working',0);";
                            $conn->query($sql);
                            $sql = "insert into UserDetails(username, Name, DOB, gender, profile, DOB_proof) values('".$usr."','".$_POST["name"]."','".$_POST["dob"]."','".$_POST["gender"]."','".$profile."','".$dob_proof."');";
                            $conn->query($sql);
                            $sql = "insert into ContactDetails(username, phone, email, address, address_proof) values('".$usr."','".$_POST["mobile"]."','".$_POST["email"]."','".$_POST["address"]."','".$address_proof."');";
                            $conn->query($sql);
                            $sql = "insert into UserActivity(username, description, DNT) values('".$usr."','Your account had created.','".date("Y-m-d H:i:s")."');";
                            $conn->query($sql);
                            $conn->close();
                            header("Location: ../");
                        } else {
                            echo "<script>window.alert('You are not eligible to open a bank account');</script>";
                        }
                    } else {
                        echo "<script>window.alert('Image size should be less than 10MB');</script>";
                    }
                } else {
                    echo "<script>window.alert('File should be an image');</script>";
                }
            }
            $conn->close();
        ?>
        <title>Opening an Account</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../img/icon.png" type="image/x-icon">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="container">
            <h1>Account Creation</h1>
            <h3>fill the mentioned details correctly</h3>
            <form action="?" method="post" enctype="multipart/form-data">
                <div class="form-con">
                    <div class="form-items">
                        <label for="type">Account type</label>
                        <select name="type" id="type">
                            <option value="savings">Savings Account</option>
                            <option value="loan">Loan Account</option>
                        </select>
                    </div>
                    <div class="heading">
                        <h2>Personal Details</h2>
                    </div>
                    <div class="form-items">
                        <label for="profile">Upload a passport sized photo</label>
                        <input type="file" name="profile" id="profile" required>
                    </div>
                    <div class="form-items">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                    <div class="form-items">
                        <label for="dob">Date of Birth</label>
                        <input type="date" name="dob" id="dob" required>
                    </div>
                    <div class="form-items">
                        <label for="dob_proof">Please provide any DOB proof</label>
                        <input type="file" name="dob_proof" id="dob_proof" required>
                    </div>
                    <div class="form-items">
                        <label>Gender</label>
                        <div>
                            <input type="radio" name="gender" id="male" value="Male" checked> <label for="male">Male</label>
                            <input type="radio" name="gender" id="female" value="Female"> <label for="female">Female</label>
                        </div>
                    </div>
                    <div class="heading">
                        <h2>Contact Details</h2>
                    </div>
                    <div class="form-items">
                        <label for="mobile">Phone Number</label>
                        <input type="tel" name="mobile" id="mobile" pattern="\d{10}" required>
                    </div>
                    <div class="form-items">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="form-items">
                        <label for="address">Address</label>
                        <textarea name="address" id="address" cols="35" rows="5" required></textarea>
                    </div>
                    <div class="form-items">
                        <label for="address_proof">Please provide the address proof</label>
                        <input type="file" name="address_proof" id="address_proof" required>
                    </div>
                    <hr style="width:100%">
                    <p style="text-align: justify;"><input type="checkbox" required> I hereby declare that the information provided in this application for opening a bank account with E-Safe Finance is true, complete, and accurate to the best of my knowledge. I understand that any false or misleading information may result in the rejection of my application or the closure of my account.</p>
                    <div class="form-items" style="justify-content: center;">
                        <input type="submit" value="Create Account">
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>