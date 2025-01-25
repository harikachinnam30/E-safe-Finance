<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            date_default_timezone_set("Asia/Kolkata");
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
                $sql = "select * from UserActivity where username='".$_SESSION["username"]."' order by DNT desc;";
                $result = $conn->query($sql);
                if($_SERVER["REQUEST_METHOD"]=="POST") {
                    if($_POST["getTiccoin"]) {
                        if($row["Balance"]>=450*$_POST["getTiccoin"]) {
                            $sql = "update UserAccounts set TicCoin=".$row["TicCoin"]+$_POST["getTiccoin"].", Balance=".$row["Balance"]-450*$_POST["getTiccoin"]." where username='".$_SESSION["username"]."';";
                            $conn->query($sql);
                            $sql = "insert into UserActivity(username, description, DNT) values('".$_SESSION['username']."','You had brought ".$_POST["getTiccoin"]." TicCoin(s).','".date("Y-m-d H:i:s")."');";
                            $conn->query($sql);
                            echo "<script>window.alert('You brought ".$_POST["getTiccoin"]." TicCoin(s)');</script>";
                        } else {
                            echo "<script>window.alert(\"You don't have enough balance!\");</script>";
                        }
                    } elseif($_POST["setTiccoin"]) {
                        if($row["TicCoin"]>=$_POST["setTiccoin"]) {
                            $sql = "update UserAccounts set TicCoin=".$row["TicCoin"]-$_POST["setTiccoin"].", Balance=".$row["Balance"]+450*$_POST["setTiccoin"]." where username='".$_SESSION["username"]."';";
                            $conn->query($sql);
                            $sql = "insert into UserActivity(username, description, DNT) values('".$_SESSION['username']."','You had sold ".$_POST["setTiccoin"]." TicCoin(s).','".date("Y-m-d H:i:s")."');";
                            $conn->query($sql);
                            echo "<script>window.alert('You sold ".$_POST["setTiccoin"]." TicCoin(s)');</script>";
                        } else {
                            echo "<script>window.alert(\"You don't have enough TicCoins!\");</script>";
                        }
                    } elseif($_POST["getLoan"]) {
                        if($row["AccountType"]=="loan") {
                            $sql = "update UserAccounts set status='loanRequest(".$_POST["getLoan"].")' where username='".$_SESSION["username"]."';";
                            $conn->query($sql);
                            $sql = "insert into UserActivity(username, description, DNT) values('".$_SESSION['username']."','You had requested for a loan.','".date("Y-m-d H:i:s")."');";
                            $conn->query($sql);
                            echo "<script>window.alert('Your request for loan has been noted. You will be contacted by our staff. Thank you!');</script>";
                        } else {
                            echo "<script>window.alert('Your account is not a loan account\\nTo get a loan, please switch your account to loan account');</script>";
                        }
                    } elseif($_POST["receiver"]) {
                        $sql = "select Balance, TicCoin from UserAccounts where username='".$_POST["receiver"]."';";
                        $result1 = $conn->query($sql);
                        if($result1->num_rows) {
                            if($_POST["pay"]>0) {
                                $row4 = $result1->fetch_assoc();
                                if($_POST["payvia"]=="Currency") {
                                    if($row["Balance"]>=$_POST["pay"]) {
                                        $sql = "update UserAccounts set Balance=".$row["Balance"]-$_POST["pay"]." where username='".$_SESSION["username"]."';";
                                        $conn->query($sql);
                                        $sql = "update UserAccounts set Balance=".$row4["Balance"]+$_POST["pay"]." where username='".$_POST["receiver"]."';";
                                        $conn->query($sql);
                                        $description = "(No description)";
                                        if($_POST["paymentDescription"])
                                            $description = $_POST["paymentDescription"];
                                        $sql = "insert into Transactions(sender, receiver, Amount, TicCoin, PaymentType, description, status, DNT) values('".$_SESSION["username"]."', '".$_POST["receiver"]."', ".$_POST["pay"].", 0, '".$_POST["paymentMode"]."', '".$description."', 'successful', '".date("Y-m-d H:i:s")."');";
                                        $conn->query($sql);
                                        $sql = "insert into UserActivity(username, description, DNT) values('".$_SESSION['username']."','You had transfered ".$_POST["pay"]." ".$_POST["payvia"]." through ".$_POST["paymentMode"]." to ".$_POST["receiver"].".','".date("Y-m-d H:i:s")."');";
                                        $conn->query($sql);
                                    } else {
                                        echo "<script>window.alert(\"You don't have enough balance!\");</script>";
                                    }
                                } else {
                                    if($row["TicCoin"]>=$_POST["pay"]) {
                                        $sql = "update UserAccounts set TicCoin=".$row["TicCoin"]-$_POST["pay"]." where username='".$_SESSION["username"]."';";
                                        $conn->query($sql);
                                        $sql = "update UserAccounts set TicCoin=".$row4["TicCoin"]+$_POST["pay"]." where username='".$_POST["receiver"]."';";
                                        $conn->query($sql);
                                        $description = "(No description)";
                                        if($_POST["paymentDescription"])
                                            $description = $_POST["paymentDescription"];
                                        $sql = "insert into Transactions(sender, receiver, Amount, TicCoin, PaymentType, description, status, DNT) values('".$_SESSION["username"]."', '".$_POST["receiver"]."', 0, ".$_POST["pay"].", '".$_POST["paymentMode"]."', '".$description."', 'successful', '".date("Y-m-d H:i:s")."');";
                                        $conn->query($sql);
                                        $sql = "insert into UserActivity(username, description, DNT) values('".$_SESSION['username']."','You had transfered ".$_POST["pay"]." ".$_POST["payvia"]." through ".$_POST["paymentMode"]." to ".$_POST["receiver"].".','".date("Y-m-d H:i:s")."');";
                                        $conn->query($sql);
                                    } else {
                                        echo "<script>window.alert(\"You don't have enough TicCoins!\");</script>";
                                    }
                                }
                            } else {
                                echo "<script>window.alert('The transfer should be more than 0!');</script>";
                            }
                        } else {
                            echo "<script>window.alert('Please make sure the given username is valid!');</script>";
                        }
                    } elseif($_POST["sender"]) {
                        $sql = "select username from Auth where username='".$_POST["sender"]."';";
                        $result1 = $conn->query($sql);
                        if($result1->num_rows) {
                            if($_POST["pay"]>0) {
                                if($_POST["payvia"]=="Currency") {
                                    $description = "(No description)";
                                    if($_POST["paymentDescription"])
                                        $description = $_POST["paymentDescription"];
                                    $sql = "insert into Transactions(sender, receiver, Amount, TicCoin, PaymentType, description, status, DNT) values('".$_POST["sender"]."', '".$_SESSION["username"]."', ".$_POST["pay"].", 0, '".$_POST["paymentMode"]."', '".$description."', 'pending', '".date("Y-m-d H:i:s")."');";
                                    $conn->query($sql);
                                    $sql = "insert into UserActivity(username, description, DNT) values('".$_SESSION['username']."','You had sent an invoice of ".$_POST["pay"]." ".$_POST["payvia"]." through ".$_POST["paymentMode"]." to ".$_POST["sender"].".','".date("Y-m-d H:i:s")."');";
                                    $conn->query($sql);
                                } else {
                                    $description = "(No description)";
                                    if($_POST["paymentDescription"])
                                        $description = $_POST["paymentDescription"];
                                    $sql = "insert into Transactions(sender, receiver, Amount, TicCoin, PaymentType, description, status, DNT) values('".$_POST["sender"]."', '".$_SESSION["username"]."', 0, ".$_POST["pay"].", '".$_POST["paymentMode"]."', '".$description."', 'pending', '".date("Y-m-d H:i:s")."');";
                                    $conn->query($sql);
                                    $sql = "insert into UserActivity(username, description, DNT) values('".$_SESSION['username']."','You had sent an invoice of ".$_POST["pay"]." ".$_POST["payvia"]." through ".$_POST["paymentMode"]." to ".$_POST["sender"].".','".date("Y-m-d H:i:s")."');";
                                    $conn->query($sql);
                                }
                            } else {
                                echo "<script>window.alert('The transfer should be more than 0!');</script>";
                            }
                        } else {
                            echo "<script>window.alert('Please make sure the given username is valid!');</script>";
                        }
                    } elseif($_POST["payDirection"]) {
                        $sql = "select * from Transactions where";
                        if($_POST["payDirection"]=="sent")
                            $sql .= " sender='".$_SESSION["username"]."'";
                        elseif($_POST["payDirection"]=="recv")
                            $sql .= " receiver='".$_SESSION["username"]."'";
                        else
                            $sql .= " (sender='".$_SESSION["username"]."' or receiver='".$_SESSION["username"]."')";
                        if($_POST["payStatus"])
                            $sql .= " and status='".$_POST["payStatus"]."'";
                        if($_POST["payType"])
                            $sql .= " and PaymentType='".$_POST["payType"]."'";
                        if($_POST["startDate"])
                            $sql .= " and DNT>'".$_POST["startDate"]."'";
                        if($_POST["endDate"])
                            $sql .= " and DNT<DATE_ADD('".$_POST["endDate"]."', INTERVAL 1 DAY)";
                        $sql .= " order by InvoiceNo DESC;";
                        $result1 = $conn->query($sql);
                    } else {
                        echo "<script>window.alert('This action is prevented!');</script>";
                    }
                }
                if($_SERVER["REQUEST_METHOD"]=="POST"&&!$_POST["payDirection"]||$_SERVER["REQUEST_METHOD"]!="POST")
                    $result1 = $conn->query("select * from Transactions where sender='".$_SESSION["username"]."' or receiver='".$_SESSION["username"]."' order by InvoiceNo DESC;");
                if($_GET["typechange"]) {
                    if($_GET["typechange"]==$row["AccountType"]) {
                        echo "<script>window.alert('Your account is already a ".$_GET["typechange"]." account!');</script>";
                    } else {
                        $sql = "update UserAccounts set AccountType='".$_GET["typechange"]."' where username='".$_SESSION["username"]."';";
                        $conn->query($sql);
                        $sql = "insert into UserActivity(username, description, DNT) values('".$_SESSION['username']."','You had changed your account type to ".$_GET["typechange"]." account.','".date("Y-m-d H:i:s")."');";
                        $conn->query($sql);
                        echo "<script>window.alert('Your account is changed successfully');</script>";
                    }
                }
                if($_GET["uname"]) {
                    $sql = "select Name from UserDetails where username='".$_GET["uname"]."';";
                    $result2 = $conn->query($sql);
                    if($result2->num_rows)
                        $name = $result2->fetch_assoc()["Name"];
                    else
                        $name = "*No one with this username*";
                }
                if($_GET["cancel"]||$_GET["deny"]||$_GET["pay"]) {
                    if($_REQUEST["pay"]) {
                        $result2 = $conn->query("select * from Transactions where sender='".$_SESSION["username"]."' and InvoiceNo=".$_GET["pay"].";");
                        if($result2->num_rows) {
                            $row5 = $result2->fetch_assoc();
                            $result2 = $conn->query("select Balance, TicCoin from UserAccounts where username='".$row5["receiver"]."';");
                            $row6 = $result2->fetch_assoc();
                            if($row5["Amount"]) {
                                if($row["Balance"]>=$row5["Amount"]) {
                                    $conn->query("update UserAccounts set Balance=".$row["Balance"]-$row5["Amount"]." where username='".$row5["sender"]."';");
                                    $conn->query("update UserAccounts set Balance=".$row6["Balance"]+$row5["Amount"]." where username='".$row5["receiver"]."';");
                                    $conn->query("insert into UserActivity values('".$_SESSION['username']."','You had transfered ".$row5["Amount"]." Currency through ".$row5["PaymentType"]." to ".$row5["receiver"].".','".date("Y-m-d H:i:s")."');");
                                    $conn->query("update Transactions set status='successful' where InvoiceNo=".$_GET["pay"].";");
                                } else {
                                    echo "<script>window.alert(\"You don't have enough balance!\");</script>";
                                }
                            } else {
                                if($row["TicCoin"]>=$row5["TicCoin"]) {
                                    $conn->query("update UserAccounts set TicCoin=".$row["TicCoin"]-$row5["TicCoin"]." where username='".$row5["sender"]."';");
                                    $conn->query("update UserAccounts set TicCoin=".$row6["TicCoin"]+$row5["TicCoin"]." where username='".$row5["receiver"]."';");
                                    $conn->query("insert into UserActivity values('".$_SESSION['username']."','You had transfered ".$row5["TicCoin"]." TicCoins through ".$row5["PaymentType"]." to ".$row5["receiver"].".','".date("Y-m-d H:i:s")."');");
                                    $conn->query("update Transactions set status='successful' where InvoiceNo=".$_GET["pay"].";");
                                } else {
                                    echo "<script>window.alert(\"You don't have enough TicCoins!\");</script>";
                                }
                            }
                        }
                    } elseif ($_REQUEST["deny"]) {
                        $result2 = $conn->query("select * from Transactions where sender='".$_SESSION["username"]."' and InvoiceNo=".$_GET["deny"].";");
                        if($result2->num_rows) {
                            $row5 = $result2->fetch_assoc();
                            if($row5["status"]=="pending") {
                                $conn->query("update Transactions set status='denied' where InvoiceNo=".$_GET["deny"].";");
                                $conn->query("insert into UserActivity values('".$_SESSION['username']."','You had denied the Invoice No. ".$row5["InvoiceNo"].".','".date("Y-m-d H:i:s")."');");
                            }
                        }
                    } else {
                        $result2 = $conn->query("select * from Transactions where receiver='".$_SESSION["username"]."' and InvoiceNo=".$_GET["cancel"].";");
                        if($result2->num_rows) {
                            $row5 = $result2->fetch_assoc();
                            if($row5["status"]=="pending") {
                                $conn->query("update Transactions set status='cancelled' where InvoiceNo=".$_GET["cancel"].";");
                                $conn->query("insert into UserActivity values('".$_SESSION['username']."','You had cancelled the Invoice No. ".$row5["InvoiceNo"].".','".date("Y-m-d H:i:s")."');");
                            }
                        }
                    }
                }
                $sql = "select * from Transactions where DNT>'".date("Y-m-d H:i:s",strtotime("-30 days"))."';";
                $result2 = $conn->query($sql);
                $expenditure = $earn = 0;
                while($row5=$result2->fetch_assoc()) {
                    if($row5["receiver"]==$_SESSION["username"]) {
                        if($row5["Amount"])
                            $earn += $row5["Amount"];
                        else
                            $earn += $row5["TicCoin"]*450;
                    } elseif($row5["sender"]==$_SESSION["username"]) {
                        if($row5["Amount"])
                            $expenditure += $row5["Amount"];
                        else
                            $expenditure += $row5["TicCoin"]*450;
                    } else {
                        //
                    }
                }
                if($earn==0&&$expenditure==0)
                    $percent = 0;
                else
                    $percent = round($earn*360/($earn+$expenditure));
                $conn->close();
                $profile = "data:image;base64,".base64_encode($row1["profile"])."";
            } else {
                header("Location: auth/");
            }
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>E-Safe Finance</title>
        <link rel="icon" href="img/icon.png" type="image/x-icon">
        <link rel="stylesheet" href="loader.css">
        <script src="loader.js"></script>
        <script>
            var currency = [1,0.011,0.012,0.087,1.8,16.09,0.0095];
            function toggle_dialog() {
                let dialog = document.getElementById("dialog").style.display;
                if(dialog=="block") {
                    document.getElementById("dialog").style.display = "none";
                } else {
                    document.getElementById("dialog").style.display = "block";
                }
            }
            function toggle_dropdown(pos) {
                let dropdown = document.getElementsByClassName("dropdown-2")[pos].style.display;
                if(dropdown=="flex") {
                    document.getElementsByClassName("dropdown-2")[pos].style.display = "none";
                    document.getElementsByClassName("dropdown-1")[pos].src = "img/dropdown.png";
                } else {
                    document.getElementsByClassName("dropdown-2")[pos].style.display = "flex";
                    document.getElementsByClassName("dropdown-1")[pos].src = "img/pickup.svg";
                }
            }
            function hash_change() {
                let hash = window.location.hash;
                let tags = document.getElementById("content").children;
                for(tag of tags) {
                    tag.style.display = "none";
                }
                switch(hash) {
                    case "#account":
                        document.getElementById("account").style.display = "grid";
                        break;
                    case "#transfer":
                        document.getElementById("transfer").style.display = "block";
                        break;
                    case "#converter":
                        document.getElementById("converter").style.display = "block";
                        break;
                    case "#converter-1":
                        document.getElementById("converter-1").style.display = "block";
                        break;
                    case "#converter-2":
                        document.getElementById("converter-2").style.display = "block";
                        break;
                    case "#SI":
                        document.getElementById("SI").style.display = "block";
                        break;
                    case "#LI":
                        document.getElementById("LI").style.display = "block";
                        break;
                    case "#typechange":
                        document.getElementById("typechange").style.display = "flex";
                        break;
                    case "#loan":
                        document.getElementById("loan").style.display = "block";
                        break;
                    case "#history":
                        document.getElementById("history").style.display = "block";
                        break;
                    case "#help":
                        document.getElementById("help").style.display = "block";
                        break;
                    default:
                        window.location = "finance.php#account";
                }
            }
            window.addEventListener("DOMContentLoaded", hash_change);
            window.addEventListener("hashchange", hash_change);
            function convert() {
                let amount = document.getElementById("amount");
                let from = document.getElementById("from");
                let to = document.getElementById("to");
                if(from.value!="From"&&to.value!="To")
                    amount.value = Math.round(100*amount.value*currency[to.value]/currency[from.value])/100;
                else
                    window.alert("Please select from and to currencies");
            }
            function redirectToChatBot() {
                setTimeout(function() {
                    window.location = "chatbot.html";
                },2000);
                document.getElementById("getStarted").children[0].style.transform = "translateX(150px) rotate(180deg)";
                document.getElementById("getStarted").children[1].innerHTML="Loading...";
            }
            function tab_switch(pos) {
                document.getElementsByClassName("tabs-body")[pos].style.display = "flex";
                document.getElementsByClassName("tabs-body")[Math.abs(pos-1)].style.display = "none";
            }
            let i = 360;
            function toggle_payment(pos) {
                let payment = document.getElementsByClassName("tr-3")[pos].style.display;
                if(payment=="flex")
                    document.getElementsByClassName("tr-3")[pos].style.display = "none";
                else
                    document.getElementsByClassName("tr-3")[pos].style.display = "flex";
            }
        </script>
        <style>
            #top {
                background-image : linear-gradient(to right, rgb(100,64,255), rgb(255,100,255));
                display : flex;
                flex-direction : row-reverse;
                position : fixed;
                top : 0;
                left : 0;
                width : 100vw;
                height : 100px;
                justify-content : space-between;
                align-items : center;
            }
            #opt {
                display : flex;
                justify-content : space-around;
                align-items : center;
                color : white;
                cursor : pointer;
                min-width : 300px;
            }
            #logo {
                display : flex;
                flex-direction : row;
                align-items : center;
                color : white;
            }
            #profile {
                width: 40px;
                height: 40px;
                border: 1px solid black;
                border-radius:20px;
                margin-right:20px;
                cursor : pointer;
            }
            #dialog {
                display : none;
                border-radius : 10px;
                position : fixed;
                top : 75px;
                right : 20px;
                background-image : linear-gradient(to bottom, rgb(100,64,255), rgb(255,100,255));
                padding : 10px 50px;
            }
            #dialog button {
                border : none;
                border-radius : 5px;
                width : 46%;
                padding : 5px 1%;
                cursor : pointer;
            }
            #main {
                position : fixed;
                top : 100px;
                left : 0;
                width : 100%;
                bottom : 0;
            }
            #topic {
                background-color : rgb(178,82,255);
                display : flex;
                flex-direction : column;
                position : absolute;
                border-radius : 25px;
                padding : 25px 0;
                transform : translateX(-95%);
                transition : 1s transform;
            }
            #topic:hover {
                transform : translateX(0);
            }
            #topic div:nth-child(odd) {
                display : flex;
                justify-content : space-between;
                align-items : center;
                width : 300px;
                padding : 0 30px;
                cursor : pointer;
            }
            #topic div:hover {
                background-color : rgb(255,190,255);
            }
            .dropdown-2 {
                display : none;
                flex-direction : column;
            }
            .dropdown-2 h3 {
                padding : 10px 0;
                margin : 0;
                text-align : center;
            }
            .dropdown-2 h3:hover {
                background-image : linear-gradient(to right, rgba(100,64,255,0.5), rgba(255,100,255,0.5));
                cursor : pointer;
            }
            #content {
                position : absolute;
                left : 25px;
                right : 0;
                height : 100%;
                overflow : scroll;
            }
            #converter, #converter-1, #converter-2, #help {
                margin : 5%;
            }
            .form {
                border-radius : 25px;
                background-image : linear-gradient(to right, rgba(100,64,255,0.5), rgba(255,100,255,0.5));
                padding : 5%;
                color : white;
            }
            #amount {
                width : 40vw;
                min-width : 300px;
                height : 40px;
                text-align : center;
                border : none;
                border-radius : 10px;
                margin : 1%;
                padding: 0;
                outline-color : rgb(178,82,255);
            }
            #from, #to {
                text-align : center;
                width : 49%;
                height : 40px;
                border : none;
                border-radius : 10px;
                outline-color : rgb(178,82,255);
            }
            #getTiccoin, #setTiccoin {
                width : 20vw;
                min-width : 200px;
                height : 40px;
                text-align : center;
                margin : 2%;
                border : none;
                border-radius : 10px;
                outline-color : rgb(178,82,255);
            }
            .about {
                text-align : center;
                border-radius : 25px;
                background-image : linear-gradient(to right, rgb(100,64,255), rgb(255,100,255));
                padding : 5%;
                color : white;
            }
            .submit, .reset {
                background-image : linear-gradient(to right, rgb(100,64,255), rgb(255,100,255));
                color : white;
                width : 30%;
                height : 40px;
                border : none;
                border-radius : 10px;
                margin : 1%;
                outline-color : rgb(178,82,255);
                cursor : pointer;
            }
            #help-container {
                background-image : linear-gradient(to right, rgba(100,64,255,0.3), rgba(255,100,255,0.3));
                border-radius : 10px;
                padding : 100px 50px;
                display: flex;
                justify-content : space-around;
                align-items : center;
            }
            #help-container div {
                width : 40%;
            }
            #help-container #chatBot {
                position : relative;
                width : 100%;
                border : 5px solid rgb(178,82,255);
                border-radius : 100px;
                padding : 5%;
                background-color : rgb(190,164,255);
            }
            #chatBot #getStarted {
                position : absolute;
                left : 50%;
                bottom : 5px;
                transform : translateX(-50%);
                height : 50px;
                width : 200px;
                border : 1px solid rgb(178,82,255);
                border-radius : 25px;
                display : flex;
                align-items : center;
                background-color : rgb(150, 124, 233);
                cursor : pointer;
            }
            #account {
                padding : 3%;
                display : grid;
                grid-template-rows : 300px auto;
                grid-template-columns : calc(50% - 25px) calc(50% - 25px);
                grid-gap : 50px;
                height : 94%;
            }
            #accountProfile {
                background-image : linear-gradient(to right, rgba(100,64,255,0.5), rgba(255,100,255,0.5));
                display : flex;
                border-radius : 50px;
                padding : 50px;
                align-items : center;
                grid-column : 1 / span 2;
            }
            #accountType {
                width : 80%;
                display : flex;
                align-items : center;
            }
            #accountBalance {
                width : 20%;
                display : flex;
                flex-direction : column;
                justify-content : space-evenly;
                height : 200px;
            }
            #accountBalance p {
                margin : 0;
                flex-grow : 1;
            }
            #accountBalance h2 {
                margin : 0;
                flex-grow : 2;
            }
            #moneyManagement {
                padding : 50px;
                border-radius : 50px;
                background-color : rgba(150, 124, 233, 0.5);
                display : flex;
                justify-content : space-evenly;
                align-items : center;
            }
            #accountActivity {
                padding : 50px;
                border-radius : 50px;
                background-color : rgba(222, 93, 222, 0.5);
                display : flex;
                flex-direction : column;
                overflow : scroll;
            }
            #pieChart {
                min-width: 200px;
                height: 200px;
                border-radius: 50%;
                background-image: conic-gradient(
                    rgb(100, 64, 255) <?php echo $percent."deg"; ?>,
                    rgb(255, 100, 255) <?php echo $percent."deg"; ?>
                );
            }
            .colorBox {
                display : inline-block;
                width : 10px;
                height : 10px;
            }
            #account li {
                padding : 10px;
                border-radius : 10px;
                display : flex;
            }
            #account li div:nth-child(1) {
                width : 30%;
            }
            #account li:hover {
                cursor : default;
                background-color : rgba(0,0,0,0.1)
            }
            #SI, #LI, #typechange {
                margin : 50px;
                padding : 50px;
                border-radius : 50px;
                background-image : linear-gradient(to right, rgba(100,64,255,0.4), rgba(255,100,255,0.4));
                height : calc(100% - 200px);
                overflow : scroll;
            }
            #loan {
                position : relative;
                background : rgba(255,255,255,0.8) url('img/loan.jpg') center/min(80vw, 80vh) no-repeat fixed;
                background-blend-mode : overlay;
                height : 100%;
            }
            #loan-form {
                position : absolute;
                left : 50%;
                transform : translateX(-50%);
                text-align : center;
            }
            #loan select {
                margin : 10px;
                padding : 10px;
                max-width : 300px;
                outline-color : rgb(178,82,255);
            }
            #loan input {
                margin : 20px;
                padding : 10px;
                border : none;
                border-radius : 5px;
                background-color : rgb(178,82,255);
                outline-color : rgb(178,82,255);
                cursor : pointer;
            }
            #SA h2, #LA h2 {
                color : rgb(200,90,255);
                text-align : center;
            }
            #SA p, #LA p {
                height : 50%;
                margin : auto 10%;
                text-align : justify;
                width : 80%;
                overflow : scroll;
            }
            #SA img, #LA img {
                margin-left : 50%;
                height : 150px;
                transform : translateX(-50%);
            }
            #SA button, #LA button, .tabs-body input[type="submit"] {
                margin-top : 50px;
                margin-left : 50%;
                padding : 10px 20px;
                border : none;
                border-radius : 5px;
                background-color : rgb(178,82,255);
                outline-color : rgb(178,82,255);
                transform : translateX(-50%);
                color : white;
                cursor : pointer
            }
            #transfer {
                padding : 50px;
                height : calc(100% - 100px);
            }
            #tabs {
                display : flex;
                height : 40px;
            }
            #tabs input, .tabs-body {
                display : none;
            }
            .tabs {
                border : 2px solid #eee;
                border-radius : 10px 10px 0 0;
                padding : 10px;
                background-color : rgba(178,82,255,0.3);
                cursor : pointer;
            }
            .tabs-body {
                height : 100%;
                width : 100%;
                justify-content : center;
            }
            .tabs-body input[type="radio"] + label {
                cursor : pointer;
            }
            #tabs input[type="radio"]:checked + .tabs {
                background-color : rgb(178,82,255);
            }
            #tabs-body {
                background-color : #eee;
                height : calc(100% - 84px);
                border-radius : 0 20px 20px;
                padding : 20px;
            }
            #directPay, #invoices {
                width : 500px;
            }
            textarea {
                resize : none;
                width : 100%;
                outline-color : rgb(178,82,255);
                border : none;
                border-radius : 10px;
                background-color : #ccc;
            }
            .form-element {
                display : flex;
                justify-content : space-between;
                margin : 25px 0;
            }
            .tabs-body input[type="text"], .tabs-body input[type="number"] {
                display : block;
                outline-color : rgb(178,82,255);
                border : none;
                border-radius : 10px;
                background-color : #ccc;
                padding : 10px;
                width : max(50%, 200px);
                text-align : center;
            }
            .tabs-body input[type="radio"] {
                accent-color : rgb(178,82,255);
            }
            #history {
                padding : 3%;
                height : 94%;
            }
            #history input[type="image"] {
                background-color:rgb(200,100,255);
                border-radius:15px;
                padding:5px;
                cursor:pointer;
                transition : transform 1s ease-in-out;
            }
            #date-filters input {
                text-align : center;
                min-width : 200px;
                border : none;
                background-color : #eee;
                padding : 10px;
                outline : none;
                flex-grow : 1;
            }
            #history select {
                text-align : center;
                min-width : 200px;
                border : none;
                background-color : #eee;
                padding : 10px;
                border-radius : 10px;
                outline : none;
                flex-grow : 1;
                margin : 0 1%;
            }
            .table {
                display : flex;
                flex-direction : column;
                margin : 50px 0;
                min-width : 700px;
            }
            .tr-1 {
                background-image : linear-gradient(to right, rgba(100,64,255,0.5), rgba(255,100,255,0.5));
                border-radius : 10px;
                margin : 10px;
            }
            .tr-2 {
                display : flex;
                cursor : pointer;
            }
            .tr-3 {
                display : none;
                justify-content : space-evenly;
                background-color : #eee;
                padding : 10px;
                border-radius : 0 0 10px 10px;
            }
            .td, .th {
                display : flex;
                flex-direction : column;
                justify-content : center;
                align-items : center;
                padding : 15px;
                width : 20%;
                margin : 0;
            }
            .TD {
                text-align : center;
                padding : 15px;
                width : 30%;
                margin : 0;
            }
            .td p {
                margin : 0;
            }
            .td span {
                display : inline-block;
                padding : 5px;
                border-radius : 5px;
            }
            .successful {
                background-image : linear-gradient(to right, rgb(0,199,0), rgb(0,255,0), rgb(0,199,0));
            }
            .pending {
                background-image : linear-gradient(to right, rgb(199,150,0), rgb(255,180,0), rgb(199,150,0));
            }
            .cancelled, .denied {
                background-image : linear-gradient(to right, rgb(199,0,0), rgb(255,0,0), rgb(199,0,0));
            }
            .cancel, .deny {
                padding : 10px;
                border-radius : 10px;
                border : none;
                background-color : red;
                cursor : pointer;
            }
            .accept {
                padding : 10px;
                border-radius : 10px;
                border : none;
                background-color : green;
                margin-right : 10px;
                cursor : pointer;
            }
        </style>
    </head>
    <body>
        <div class="loader">
            <img src="img/main-loader.gif" alt="Loading">
        </div>
        <div id="top">
            <div id="opt">
                <h2 onclick="window.location='../E-Safe Finance'">About</h2>
                <h2 onclick="window.location='location.html'">Location</h2>
                <div id="profile" onclick="toggle_dialog()">
                    <img src="<?php echo $profile; ?>" alt="Profile" width="40px" height="40px" style="border: 1px solid black; border-radius: 20px;">
                </div>
            </div>
            <div id="logo">
                <img src="img/logo.png" alt="LOGO" width="100px" height="100px">
                <h1>E-Safe</h1>
            </div>
        </div>
        <div id="main">
            <div id="content">
                <div id="account">
                    <div id="accountProfile">
                        <div id="accountType">
                            <img src="<?php echo $profile; ?>" alt="Profile" width="200px" height="200px" style="border-radius: 100px;">
                            <div style="margin-left : 5%;">
                                <h1><?php echo $_SESSION["username"]; ?></h1>
                                <h3><?php echo $row["AccountType"];?> account</h3>
                            </div>
                        </div>
                        <div id="accountBalance">
                            <p>Account Balance</p>
                            <h2>₹<?php echo $row["Balance"]; ?></h2>
                            <p>Ticcoins</p>
                            <h2><?php echo $row["TicCoin"]; ?></h2>
                        </div>
                    </div>
                    <div id="moneyManagement">
                        <div id="pieChart"></div>
                        <div>
                            <h3>Earnings and Expenditures in last 30 days</h3>
                            <p><span class="colorBox" style="background-color:rgb(100,64,255);"></span> Earning</p>
                            <p><span class="colorBox" style="background-color:rgb(255,100,255);"></span> Expenditure</p>
                        </div>
                    </div>
                    <div id="accountActivity">
                        <h3>Account Activity</h3>
                        <ul type="none">
                            <?php
                                while($row3 = $result->fetch_assoc()) {
                                    echo "<li><div>".$row3["DNT"]."</div><div>".$row3["description"]."</div></li>";
                                }
                            ?>
                        </ul>
                    </div>
                </div>
                <div id="transfer">
                    <div id="tabs">
                        <input type="radio" name="tab" id="directPay" onchange="tab_switch(0)">
                        <label for="directPay" class="tabs">Direct Pay</label>
                        <input type="radio" name="tab" id="invoices" onchange="tab_switch(1)">
                        <label for="invoices" class="tabs">Invoices</label>
                    </div>
                    <div id="tabs-body">
                        <div class="tabs-body">
                            <form id="directPay" action="?" method="post">
                                <div class="form-element">
                                    <label for="receiver">Receiver's Username</label>
                                    <input type="text" id="receiver" name="receiver" placeholder="eg. john" value="<?php echo $_GET["uname"]; ?>" onfocusout="window.location='finance.php?uname='+this.value+'&t=r#transfer';" required>
                                </div>
                                <p style="text-align:right;"><?php echo $name; ?></p>
                                <h4>Payment Modes</h4>
                                <input type="radio" id="imps-1" value="IMPS" name="paymentMode" checked>
                                <label for="imps-1" title="Immediate Payment Service">IMPS</label>
                                <input type="radio" name="paymentMode" id="neft-1" value="NEFT">
                                <label for="neft-1" title="National Electronic Fund Transfer">NEFT</label>
                                <input type="radio" name="paymentMode" id="rtgs" value="RTGS">
                                <label for="rtgs" title="Real Time Gross Settlement">RTGS</label>
                                <input type="radio" name="paymentMode" id="ecs" value="ECS">
                                <label for="ecs" title="Electronic Clearing System">ECS</label><br>
                                <h4>Pay via</h4>
                                <input type="radio" name="payvia" id="currency-1" value="Currency" checked>
                                <label for="currency-1">Currency</label>
                                <input type="radio" name="payvia" id="ticcoins-1" value="TicCoins">
                                <label for="ticcoins-1">TicCoins</label><br>
                                <div class="form-element">
                                    <label for="pay-1">Pay</label>
                                    <input type="number" name="pay" id="pay-1" placeholder="XXXX" required>
                                </div>
                                <label for="paymentDescription-1">Description :</label><br>
                                <textarea name="paymentDescription" id="paymentDescription-1" rows="3" placeholder="write your description here.." maxlength="200"></textarea>
                                <input type="submit" value="Pay">
                            </form>
                        </div>
                        <div class="tabs-body">
                            <form id="invoices" action="?" method="post">
                                <div class="form-element">
                                    <label for="sender">Sender's Username</label>
                                    <input type="text" id="sender" name="sender" placeholder="eg. john" value="<?php echo $_GET["uname"]; ?>" onfocusout="window.location='finance.php?uname='+this.value+'&t=s#transfer';" required>
                                </div>
                                <p style="text-align:right;"><?php echo $name; ?></p>
                                <h4>Payment Modes</h4>
                                <input type="radio" id="imps-2" name="paymentMode" value="IMPS" checked>
                                <label for="imps-2" title="Immediate Payment Service">IMPS</label>
                                <input type="radio" name="paymentMode" id="neft-2" value="NEFT">
                                <label for="neft-2" title="National Electronic Fund Transfer">NEFT</label>
                                <input type="radio" name="paymentMode" id="ach" value="ACH">
                                <label for="ach" title="Automated Clearing House">ACH</label><br>
                                <h4>Pay via</h4>
                                <input type="radio" name="payvia" id="currency-2" value="Currency" checked>
                                <label for="currency-2">Currency</label>
                                <input type="radio" name="payvia" id="ticcoins-2" value="TicCoins">
                                <label for="ticcoins-2">TicCoins</label><br>
                                <div class="form-element">
                                    <label for="pay-2">Pay</label>
                                    <input type="number" name="pay" id="pay-2" placeholder="XXXX" required>
                                </div>
                                <label for="paymentDescription-2">Description :</label><br>
                                <textarea name="paymentDescription" id="paymentDescription-2" rows="3" placeholder="write your description here.." maxlength="200"></textarea>
                                <input type="submit" value="Send">
                            </form>
                        </div>
                    </div>
                </div>
                <div id="converter">
                    <h2 style="color : rgb(178,82,255);">Currency Converter</h2>
                    <p style="font-size : 1.2em;">Currency to Currency</p>
                    <div style="display : flex; justify-content : center;">
                        <div class="form">
                            <h3>Amount To Convert : </h3>
                            <form action="?">
                                <input type="number" id="amount"><br>
                                <div style="display : flex; justify-content : space-between; margin : 1%; width:100%;">
                                    <select id="from">
                                        <option disabled selected>From</option>
                                        <option value="0">INR</option>
                                        <option value="1">EUR</option>
                                        <option value="2">USD</option>
                                        <option value="3">CNY</option>
                                        <option value="4">JPY</option>
                                        <option value="5">KRW</option>
                                        <option value="6">GBP</option>
                                    </select>
                                    <select id="to">
                                        <option disabled selected>To</option>
                                        <option value="0">INR</option>
                                        <option value="1">EUR</option>
                                        <option value="2">USD</option>
                                        <option value="3">CNY</option>
                                        <option value="4">JPY</option>
                                        <option value="5">KRW</option>
                                        <option value="6">GBP</option>
                                    </select>
                                </div><br>
                                <div style="display : flex; justify-content : space-evenly; margin : 1%; width:100%;">
                                    <input type="button" value="Calculate" class="submit" onclick="convert()">
                                    <input type="reset" value="Reset" class="reset">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="converter-1">
                    <h2 style="color : rgb(178,82,255);">Currency Converter</h2>
                    <p style="font-size : 1.2em;">Currency to Ticcoin</p>
                    <div style="display : flex; justify-content : center;">
                        <div class="form" style="text-align : center;">
                            <form action="?" method="post">
                                <input type="number" name="getTiccoin" id="getTiccoin">
                                <br>
                                <p>1 Ticcoin = 450 INR</p>
                                <br>
                                <input type="submit" value="Buy" class="submit">
                            </form>
                            <p style="color : red;"><b>Note : </b>Ensure you have enough balance<br>in your account before you purchase.</p>
                        </div>
                        <div class="about" style="transform : translateX(-50px);">
                            <h2 style="margin:0">TICCOIN</h2>
                            <img src="img/ticcoin.png" alt="TicCoin" width=150px height=150px>
                            <p>Ticcoin is a crypto currency offer by our bank.<br>It won't be available in any other banking. you<br>can purchase and sell them anytime via our<br>website.</p>
                        </div>
                    </div>
                </div>
                <div id="converter-2">
                    <h2 style="color : rgb(178,82,255);">Currency Converter</h2>
                    <p style="font-size : 1.2em;">Ticcoin to Currency</p>
                    <div style="display : flex; flex-direction : row-reverse; justify-content : center;">
                        <div class="form" style="text-align : center;">
                            <form action="?" method="post">
                                <input type="number" name="setTiccoin" id="setTiccoin">
                                <br>
                                <p>1 Ticcoin = 450 INR</p>
                                <br>
                                <input type="submit" value="Sell" class="submit">
                            </form>
                            <p style="color : red;"><b>Note : </b>Ensure you have enough Ticcoin<br>in your account before you sell.</p>
                        </div>
                        <div class="about" style="transform : translateX(50px);">
                            <h2 style="margin:0">TICCOIN</h2>
                            <img src="img/ticcoin.png" alt="TicCoin" width=150px height=150px>
                            <p>Ticcoin is a crypto currency offer by our bank.<br>It won't be available in any other banking. you<br>can purchase and sell them anytime via our<br>website.</p>
                        </div>
                    </div>
                </div>
                <div id="SI">
                    <h2 style="color:rgb(200,90,255);">Savings Interest</h2>
                    <h3>High-Yield Savings Accounts:</h3>
                    <p>E-Safe Finance understands the significance of offering high-yield savings accounts as a cornerstone of effective wealth management. Our high-yield accounts are meticulously crafted to provide customers with a competitive advantage in accruing interest. Unlike conventional savings accounts that often offer meager interest rates, our high-yield accounts feature substantially higher rates, ensuring that your savings work harder for you. Whether you're saving for a rainy day, a major purchase, or long-term financial security, our high-yield savings accounts provide a robust foundation for achieving your goals. Moreover, these accounts are backed by the safety and security of FDIC insurance, offering peace of mind knowing that your deposits are protected up to the maximum allowable limit. By entrusting your savings to E-Safe Finance's high-yield accounts, you're not only maximizing interest earnings but also fortifying your financial future.</p>
                    <h3>Certificates of Deposit (CDs):</h3>
                    <p>Certificates of Deposit (CDs) represent another powerful tool in E-Safe Finance's arsenal for maximizing savings interest. CDs offer customers a secure and predictable means of accumulating interest over a defined period. With E-Safe Finance's range of CD options, you can select terms that align with your saving objectives and time horizon. Whether you opt for a short-term CD to meet immediate financial needs or a long-term CD to bolster retirement savings, our CDs deliver competitive interest rates, often surpassing those of traditional savings accounts. By locking in your funds with a CD, you shield your savings from market volatility while capitalizing on guaranteed interest earnings. Furthermore, our CDs offer flexibility in terms of interest payment options, allowing you to choose between periodic interest payouts or compounded interest to maximize your savings potential.</p>
                    <h3>Automatic Savings Plans:</h3>
                    <p>E-Safe Finance recognizes the importance of fostering disciplined saving habits through automatic savings plans. Our automated solutions are designed to streamline the savings process, eliminating the need for manual intervention and ensuring consistent contributions to your savings account or CD. Through features such as automatic transfers from your checking account to your savings vehicle of choice, you can effortlessly allocate a portion of your income towards savings, regardless of fluctuations in your spending patterns. By automating your savings contributions, you establish a regular cadence of wealth accumulation, harnessing the power of compounding interest to accelerate your financial growth. Moreover, our automatic savings plans can be customized to align with your specific savings goals and preferences, providing a tailored approach to building wealth over time.</p>
                    <h3>Financial Advisory Services:</h3>
                    <p>At E-Safe Finance, we understand that every individual's financial journey is unique, which is why we offer personalized financial advisory services to guide you along the path to financial success. Our team of seasoned advisors collaborates closely with you to gain a comprehensive understanding of your financial aspirations, risk tolerance, and time horizon. Leveraging this insight, we develop a customized savings strategy tailored to your needs, with a keen focus on maximizing interest earnings while mitigating risk. Whether you're saving for short-term goals such as a dream vacation or long-term objectives like retirement planning, our advisors provide strategic guidance and actionable insights to optimize your savings approach. Moreover, our commitment to transparency ensures that you have a clear understanding of the rationale behind our recommendations, empowering you to make informed decisions that align with your financial objectives.</p>
                    <h3>Online Banking Tools:</h3>
                    <p>E-Safe Finance empowers customers with a suite of intuitive online banking tools designed to enhance the savings experience. Our user-friendly platform provides convenient access to your accounts, allowing you to monitor savings progress, track interest earnings, and manage transactions with ease. With features such as customizable alerts and notifications, you stay informed about account activity and can proactively manage your finances to maximize savings interest. Additionally, our online banking tools offer robust security features to safeguard your sensitive information and protect against unauthorized access. Whether you prefer to bank on-the-go via our mobile app or access account details from the comfort of your home through our website, E-Safe Finance ensures a seamless and secure digital banking experience tailored to your needs.</p>
                    <h3>Educational Resources:</h3>
                    <p>As part of our commitment to financial literacy, E-Safe Finance provides a wealth of educational resources to empower customers to make informed decisions about saving interest. Our comprehensive library of articles, guides, webinars, and workshops covers a wide range of topics, from basic savings principles to advanced investment strategies. Whether you're a novice saver looking to build a solid financial foundation or a seasoned investor seeking to fine-tune your wealth management approach, our educational materials offer valuable insights and practical tips to help you achieve your savings goals. Moreover, our educational initiatives are designed to promote financial empowerment and equip you with the knowledge and skills necessary to navigate the complexities of personal finance confidently. By leveraging these resources, you can enhance your financial literacy and embark on a journey towards long-term financial well-being.</p>
                </div>
                <div id="LI">
                    <h2 style="color:rgb(200,90,255);">Loan Interest</h2>
                    <h3>Competitive Loan Rates:</h3>
                    <p>E-Safe Finance is dedicated to offering competitive loan rates tailored to meet your borrowing needs. Whether you're seeking a personal loan, auto loan, mortgage, or business loan, we provide transparent and competitive interest rates designed to minimize the cost of borrowing. Our commitment to affordability ensures that you can access the funds you need without paying exorbitant interest charges, allowing you to achieve your financial goals while maintaining financial stability.</p>
                    <h3>Flexible Loan Terms:</h3>
                    <p>At E-Safe Finance, we understand that one size does not fit all when it comes to loans. That's why we offer flexible loan terms designed to accommodate your unique financial situation and repayment preferences. Whether you prefer shorter loan terms for faster repayment or longer terms for lower monthly payments, we work with you to tailor a loan solution that aligns with your needs. Our flexible terms empower you to choose a repayment schedule that suits your budget and financial goals, minimizing the burden of interest payments over the life of the loan.</p>
                    <h3>Refinancing Options:</h3>
                    <p>E-Safe Finance offers refinancing options to help you reduce your loan interest and save money over time. Whether you're looking to lower your monthly payments, shorten your loan term, or secure a lower interest rate, our refinancing solutions can help you achieve your objectives. By refinancing your existing loans with E-Safe Finance, you may be able to take advantage of better terms and conditions, resulting in significant savings on interest costs. Our streamlined refinancing process ensures a hassle-free experience, allowing you to enjoy the benefits of reduced loan interest without unnecessary complexity.</p>
                    <h3>Personalized Loan Assistance:</h3>
                    <p>Our team of experienced loan advisors is committed to providing personalized assistance to help you navigate the borrowing process and minimize loan interest. Whether you're unsure about which loan product best suits your needs or need guidance on optimizing your repayment strategy, our advisors are here to help. We take the time to understand your financial goals, risk tolerance, and budgetary constraints, tailoring our recommendations to ensure that you secure the most favorable loan terms and minimize interest expenses. With our personalized loan assistance, you can make informed decisions that empower you to achieve your financial objectives while minimizing the cost of borrowing.</p>
                    <h3>Online Loan Management Tools:</h3>
                    <p>E-Safe Finance offers intuitive online loan management tools designed to help you monitor and manage your loans effectively. Our user-friendly platform provides convenient access to loan details, payment history, and account balances, allowing you to stay informed about your loan status at all times. With features such as online payments, loan calculators, and account alerts, you can streamline the loan management process and optimize your repayment strategy to minimize interest costs. Additionally, our online tools provide valuable insights into your loan performance, empowering you to make proactive decisions to reduce interest expenses and accelerate debt repayment.</p>
                    <h3>Financial Education Resources:</h3>
                    <p>As part of our commitment to financial empowerment, E-Safe Finance offers a wealth of educational resources to help you minimize loan interest and achieve long-term financial success. Our educational materials cover a wide range of topics, from debt management strategies to credit score improvement techniques. Whether you're looking to consolidate debt, refinance loans, or negotiate better terms with lenders, our educational resources provide valuable insights and actionable tips to help you minimize interest costs and achieve financial freedom. By leveraging these resources, you can make informed decisions about borrowing and repayment, empowering you to take control of your financial future and minimize the impact of loan interest on your overall financial well-being.</p>
                </div>
                <div id="typechange">
                    <div id="SA">
                        <h2>Savings Account</h2>
                        <p>A savings account is a type of bank account where individuals can deposit money and earn interest. It provides a safe and secure way to save money over time. Interest rates vary depending on the financial institution and market conditions. Funds deposited in savings accounts are typically insured by the government up to a certain limit. Customers can access their funds easily through withdrawals or transfers. Some savings accounts may require a minimum balance to open or avoid fees. Online and mobile banking services make managing savings accounts convenient. Savings accounts are suitable for short-term savings goals or emergency funds. They offer flexibility and liquidity compared to other types of investments. Overall, savings accounts are a valuable tool for individuals to grow their savings and achieve financial stability.</p>
                        <img src="img/SA.png" alt="Savings Account">
                        <button onclick="window.location='finance.php?typechange=savings'">Change to Savings Account</button>
                    </div>
                    <hr>
                    <div id="LA">
                        <h2>Loan Account</h2>
                        <p>A loan account is a financial arrangement where a lender provides funds to a borrower, who agrees to repay the loan amount plus interest over a specified period. It allows individuals or businesses to borrow money for various purposes, such as purchasing a home, financing a car, or funding education. Interest rates on loans can be fixed or variable, depending on the type of loan and market conditions. Loan terms and repayment schedules vary, with options for short-term or long-term loans. Collateral may be required for secured loans, while unsecured loans rely solely on the borrower's creditworthiness. Defaulting on loan payments can result in penalties or damage to credit scores. Loan accounts can be managed through online and mobile banking platforms for convenience. Overall, loan accounts provide access to capital to fulfill financial needs, with repayment obligations structured according to agreed terms.</p>
                        <img src="img/LA.png" alt="Loan Account">
                        <button onclick="window.location='finance.php?typechange=loan'">Change to Loan Account</button>
                    </div>
                </div>
                <div id="loan">
                    <div id="loan-form">
                        <h2>Loan Application</h2>
                        <p>Are you looking for/interested in taking any bank loan?</p>
                        <p style="font-weight:bold;">We are here to assist you.</p>
                        <p style="max-width:600px; height:400px; overflow:scroll; text-align:justify;">E-Safe Finance is providing varaity of loans. Each type of loan serves a specific purpose and comes with its own terms, interest rates, and repayment schedules. It's crucial to understand the details of any loan before borrowing to ensure it fits your financial situation and needs.<br><b>Personal Loans: </b>Ideal for individuals looking to finance various personal expenses such as home improvements, weddings, or debt consolidation. Secured personal loans are suitable for those with valuable assets to use as collateral, while unsecured personal loans are better for borrowers who don't want to risk their assets.<br><b>Mortgages:</b> Perfect for individuals or families seeking to purchase a home. Fixed-rate mortgages offer stability in monthly payments, whereas adjustable-rate mortgages may be suitable for those expecting changes in their financial situation.<br><b>Auto Loans:</b> Designed for people looking to purchase a vehicle. New car loans are for those buying brand-new vehicles, while used car loans cater to buyers looking at pre-owned vehicles. Refinance auto loans are suitable for individuals aiming to lower their monthly car payments or interest rates.<br><b>Student Loans:</b> Geared towards students or their parents who need financial assistance for higher education. Federal student loans offer various repayment plans and forgiveness options, while private student loans may suit those who have exhausted federal aid or need additional funding.<br><b>Business Loans:</b> Tailored for entrepreneurs or small business owners seeking capital for startup costs, expansion, or operational expenses. SBA loans provide government-backed funding with favorable terms, while other options like equipment financing or business lines of credit offer flexibility.<br><b>Payday Loans:</b> Intended for individuals facing short-term financial emergencies and in need of immediate cash. Traditional payday loans are best for borrowers who can repay the loan in full by their next payday, while installment payday loans offer more flexible repayment options.<br><b>Debt Consolidation Loans:</b> Helpful for individuals burdened with multiple debts and seeking to simplify their payments. Personal consolidation loans merge debts into one manageable payment, while home equity loans or HELOCs leverage home equity to consolidate debt at potentially lower interest rates.<br><b>Emergency Loans:</b> Geared towards individuals facing sudden and unexpected financial challenges. Short-term emergency loans offer quick access to funds, while emergency cash loans provide immediate financial relief for urgent expenses.</p>
                        <form action="?" method="post">
                            <select name="getLoan" id="getLoan" required>
                                <option disabled selected value="">Choose your Loan :</option>
                                <optgroup label="Personal Loans">
                                    <option value="securedPersonal">Secured Personal Loans</option>
                                    <option value="unsecuredPersonal">Unsecured Personal Loans</option>
                                </optgroup>
                                <optgroup label="Mortgages">
                                    <option value="fixedRate">Fixed-Rate Mortgages</option>
                                    <option value="adjustableRate">Adjustable-Rate Mortgages (ARMs)</option>
                                    <option value="FHA">FHA Loans</option>
                                    <option value="VA">VA Loans</option>
                                </optgroup>
                                <optgroup label="Auto Loans">
                                    <option value="newCar">New Car Loans</option>
                                    <option value="usedCar">Used Car Loans</option>
                                    <option value="refinanceAuto">Refinance Auto Loans</option>
                                </optgroup>
                                <optgroup label="Student Loans">
                                    <option value="federalStudent">Federal Student Loans</option>
                                    <option value="privateStudent">Private Student Loans</option>
                                </optgroup>
                                <optgroup label="Business Loans">
                                    <option value="smallBusiness">Small Business Administration (SBA) Loans</option>
                                    <option value="startUp">Startup Loans</option>
                                    <option value="equipmentFinancing">Equipment Financing</option>
                                    <option value="business">Business Lines of Credit</option>
                                </optgroup>
                                <optgroup label="Payday Loans">
                                    <option value="traditional">Traditional Payday Loans</option>
                                    <option value="installment">Installment Payday Loans</option>
                                </optgroup>
                                <optgroup label="Debt Consolidation Loans">
                                    <option value="personalConsolidation">Personal Consolidation Loans</option>
                                    <option value="homeEquity">Home Equity Loans or Lines of Credit (HELOCs)</option>
                                </optgroup>
                                <optgroup label="Emergency Loans">
                                    <option value="shortTimeEmergency">Short-Term Emergency Loans</option>
                                    <option value="emergencyCash">Emergency Cash Loans</option>
                                </optgroup>
                            </select>
                            <br>
                            <input type="submit" value="Proceed">
                        </form>
                    </div>
                </div>
                <div id="history">
                    <h2 style="color : rgb(178,82,255);">TRANSACTION HISTORY</h2>
                    <h3>Account History</h3>
                    <form action="?#history" method="post">
                        <div style="display:flex; align-items:center; margin-bottom:10px;">
                            <input type="image" src="img/reload.png" alt="reload" id="reload" width="15px" height="15px" onmouseover="this.style.transform='rotate('+i+'deg)';i+=360;">
                            <label for="reload" style="margin-left:10px;">filter your transaction history</label>
                        </div>
                        <div style="display:flex; flex-wrap:wrap;">
                            <div id="date-filters" style="display:flex; flex-grow:1; margin:0 5%;">
                                <input type="date" name="startDate" id="startDate" style="border-radius:10px 0 0 10px; border-right:1px solid #ccc;">
                                <input type="date" name="endDate" id="endDate" style="border-radius:0 10px 10px 0; border-left:1px solid #ccc;">
                            </div>
                            <select name="payType" id="payType">
                                <option value="" disabled selected>Type</option>
                                <option value="imps">IMPS</option>
                                <option value="neft">NEFT</option>
                                <option value="rtgs">RTGS</option>
                                <option value="ecs">ECS</option>
                                <option value="ach">ACH</option>
                            </select>
                            <select name="payStatus" id="payStatus">
                                <option value="" disabled selected>Status</option>
                                <option value="successful">Successful</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="denied">Denied</option>
                            </select>
                            <select name="payDirection" id="payDirection">
                                <option value="all" selected>All</option>
                                <option value="recv">Received</option>
                                <option value="sent">Sent</option>
                            </select>
                        </div>
                    </form>
                    <div class="table">
                        <div style="display:flex;">
                            <h4 class="th">Invoice No.</h4>
                            <h4 class="th">Date & Time</h4>
                            <h4 class="th">Type</h4>
                            <h4 class="th">Status</h4>
                            <h4 class="th">Amount</h4>
                        </div>
                        <?php
                            $i = 0;
                            while($row4 = $result1->fetch_assoc()) {
                                $code3 = "";
                                if($row4["receiver"]==$_SESSION["username"]) {
                                    $code1 = " style='color:green'>Cr";
                                    $code2 = "received from ".$row4["sender"];
                                    if($row4["status"]=="pending")
                                        $code3 = "<p class='TD'><button class='cancel' onclick='window.location=\"finance.php?cancel=".$row4["InvoiceNo"]."\";'>Cancel</button></p>";
                                } else {
                                    $code1 = " style='color:red'>Dr";
                                    $code2 = "sent to ".$row4["receiver"];
                                    if($row4["status"]=="pending")
                                        $code3 = "<p class='TD'><button class='accept' onclick='if(confirm(\"do you wanna proceed to pay?\")) {if(prompt(\"What is the username of the receiver?\")==\"".$row4["receiver"]."\") window.location=\"finance.php?pay=".$row4["InvoiceNo"]."\"; else window.alert(\"UserName is not correct\");}'>Pay</button><button class='deny' onclick='window.location=\"finance.php?deny=".$row4["InvoiceNo"]."\";'>Deny</button></p>";
                                }
                                echo '<div class="tr-1">
                                    <div class="tr-2" onclick="toggle_payment('.$i.')">
                                        <p class="td">'.$row4["InvoiceNo"].'</p>
                                        <p class="td">'.$row4["DNT"].'</p>
                                        <p class="td">'.$row4["PaymentType"].'</p>
                                        <p class="td"><span class="'.$row4["status"].'">'.$row4["status"].'<span></p>
                                        <div class="td"><p>₹'.$row4["Amount"].' <span'.$code1.'</span></p><p>'.$row4["TicCoin"].' TicCoins</p></div>
                                    </div>
                                    <div class="tr-3">
                                        <p class="TD">'.$code2.'</p>
                                        <p class="TD">'.$row4["description"].'</p>
                                        '.$code3.'
                                    </div>
                                </div>';
                                $i++;
                            }
                        ?>
                    </div>
                </div>
                <div id="help">
                    <h2 style="color:rgb(178,82,255); margin:25px;">Help Desk</h2>
                    <div id="help-container">
                        <div>
                            <h2 style="color:rgb(160,73,228);">Your helper is here!</h2>
                            <p style="text-align:justify;">Welcome to the E-Safe Help Desk! We're here to assist you with any questions or concerns you may have regarding your banking needs. Whether you're new to online banking or a long-time customer, we're dedicated to providing you with the support you need for a seamless banking experience.</p>
                        </div>
                        <div>
                            <div id="chatBot">
                                <img src="img/chatbot.png" alt="chat robo" width="200px" height="200px">
                                <img src="img/chatbotdialog.png" alt="Try our new Chat Bot" width="300px" height="150px" style="transform:translate(-10%,-25%);">
                                <div id="getStarted" onclick="redirectToChatBot()">
                                    <img src="img/gear.webp" width="50px" style="transition:transform 2s ease-in-out">
                                    <p>Get Started!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="topic">
                <div onclick="toggle_dropdown(0)">
                    <h2>Account Management</h2>
                    <img class="dropdown-1" src="img/dropdown.png" alt="show" width="25px" height="25px">
                </div>
                <div class="dropdown-2">
                    <h3 onclick="window.location='finance.php#account'">Account Details</h3>
                    <h3 onclick="window.location='finance.php#transfer'">Net Banking</h3>
                </div>
                <div onclick="toggle_dropdown(1)">
                    <h2>Currency Converter</h2>
                    <img class="dropdown-1" src="img/dropdown.png" alt="show" width="25px" height="25px">
                </div>
                <div class="dropdown-2">
                    <h3 onclick="window.location='finance.php#converter'">Currency to Currency</h3>
                    <h3 onclick="window.location='finance.php#converter-1'">Currency to Ticcoin</h3>
                    <h3 onclick="window.location='finance.php#converter-2'">Ticcoin to Currency</h3>
                </div>
                <div onclick="toggle_dropdown(2)">
                    <h2>Interest</h2>
                    <img class="dropdown-1" src="img/dropdown.png" alt="show" width="25px" height="25px">
                </div>
                <div class="dropdown-2">
                    <h3 onclick="window.location='finance.php#SI'">Savings Interest</h3>
                    <h3 onclick="window.location='finance.php#LI'">Loan Interest</h3>
                </div>
                <div onclick="toggle_dropdown(3)">
                    <h2>Loan/Savings</h2>
                    <img class="dropdown-1" src="img/dropdown.png" alt="show" width="25px" height="25px">
                </div>
                <div class="dropdown-2">
                    <h3 onclick="window.location='finance.php#typechange'">Change of Account Type</h3>
                    <h3 onclick="window.location='finance.php#loan'">Apply for Loan</h3>
                </div>
                <div onclick="toggle_dropdown(4)">
                    <h2>Transaction History</h2>
                    <img class="dropdown-1" src="img/dropdown.png" alt="show" width="25px" height="25px">
                </div>
                <div class="dropdown-2">
                    <h3 onclick="window.location='finance.php#history'">Account History</h3>
                </div>
                <div onclick="toggle_dropdown(5)">
                    <h2>Help Desk</h2>
                    <img class="dropdown-1" src="img/dropdown.png" alt="show" width="25px" height="25px">
                </div>
                <div class="dropdown-2">
                    <h3 onclick="window.location='chatbot.html'">ChatBot</h3>
                    <h3 onclick="window.open('mailto:chinnikrishna854@gmail.com')">Contact Us</h3>
                </div>
            </div>
        </div>
        <div id="dialog">
            <h3>Personal Info:</h3>
            <p><b>Name : </b><?php echo $row1["Name"];?></p>
            <p><b>Date Of Birth : </b><?php echo $row1["DOB"]?></p>
            <p><b>Phone No. : </b><?php echo $row2["phone"]?></p>
            <button onclick="window.location='edit.php'">Edit</button>
            <button onclick="window.location='auth/logout.php'" style="background-color:red; color:white;">Sign Out</button>
        </div>
        <script>
            window.addEventListener("load", loadingDone);
            <?php
                if($_GET["t"]) {
                    if($_GET["t"]=="r") {
                        echo "document.getElementById('directPay').checked = true;\ntab_switch(0);";
                    } else {
                        echo "document.getElementById('invoices').checked = true;\ntab_switch(1);";
                    }
                } else {
                    echo "document.getElementById('directPay').checked = true;\ntab_switch(0);";
                }
            ?>
        </script>
    </body>
</html>