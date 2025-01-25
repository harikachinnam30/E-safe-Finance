<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Status!</title>
        <link rel="icon" href="img/icon.png" type="image/x-icon">
        <?php
            date_default_timezone_set("Asia/Kolkata");
            session_start();
            if(!$_SESSION["username"])
                header("Location: auth/");
            $conn = new mysqli("localhost","root","","E-Safe Finance");
            $result = $conn->query("select status from UserAccounts where username='".$_SESSION["username"]."';");
            $status = $result->fetch_assoc()["status"];
            switch(substr($status, 0, 11)) {
                case "not working":
                    $heading = "Please Wait..!";
                    $paragraph = "Your account is not activated yet. It will take around 24 hours after the creation of account to activate completely";
                    $color = "#DA0";
                    break;
                case "freeze":
                    $heading = "Warning";
                    $paragraph = "Your account is freezed. For more information, contact your nearest bank";
                    $color = "blue";
                    break;
                case "loanRequest":
                    $heading = "Loan Request";
                    $paragraph = "You loan request has been noted. You will be contacted by our staff soon. For the time period, you can't use your account";
                    $color = "darkblue";
                    break;
                /*case "loanRequest(securedPersonal)":
                    break;
                case "loanRequest(unsecuredPersonal)":
                    break;
                case "loanRequest(fixedRate)":
                    break;
                case "loanRequest(adjustableRate)":
                    break;
                case "loanRequest(FHA)":
                    break;
                case "loanRequest(VA)":
                    break;
                case "loanRequest(newCar)":
                    break;
                case "loanRequest(usedCar)":
                    break;
                case "loanRequest(refinanceAuto)":
                    break;
                case "loanRequest(federalStudent)":
                    break;
                case "loanRequest(privateStudent)":
                    break;
                case "loanRequest(smallBusiness)":
                    break;
                case "loanRequest(startUp)":
                    break;
                case "loanRequest(equipmentFinancing)":
                    break;
                case "loanRequest(business)":
                    break;
                case "loanRequest(traditional)":
                    break;
                case "loanRequest(installment)":
                    break;
                case "loanRequest(personalConsolidation)":
                    break;
                case "loanRequest(homeEquity)":
                    break;
                case "loanRequest(shortTimeEmergency)":
                    break;
                case "loanRequest(emergencyCash)":
                    break;*/
                default:
                    $heading = "Working";
                    $paragraph = "Your account is working just fine";
                    $color = "green";
            }
            $conn->close();
        ?>
        <style>
            body {
                padding : max(5%, 50px);
                margin : 0;
                background-color : #eee;
            }
            .container {
                border-radius : 50px;
                background-color : white;
                min-width : 800px;
            }
            .container-top {
                padding : 50px;
                border-radius : 50px 50px 0 0;
                background-color : <?php echo $color; ?>;
                width : calc(100% - 100px);
                color : white;
                text-align : center;
            }
            .container-body {
                padding : 50px;
                border-radius : 0 0 50px 50px;
                width : calc(100% - 100px);
                text-align : center;
                font-size : 2em;
            }
            .imp {
                position : absolute;
                display : flex;
                justify-content : center;
                align-items : center;
                border : 2px solid white;
                border-radius : 20px;
                width : 20px;
                height : 20px;
                transform : scale(4);
            }
            .msg {
                font-size : 3em;
            }
            .logout {
                position : fixed;
                top : 0;
                right : 0;
                background-color : red;
                padding : 10px;
                cursor : pointer;
            }
        </style>
    </head>
    <body>
        <div class="logout" onclick="window.location='auth/logout.php'">LOGOUT</div>
        <div class="container">
            <div class="container-top">
                <div class="imp">!</div>
                <div class="msg"><?php echo $heading; ?></div>
            </div>
            <div class="container-body"><?php echo $paragraph; ?></div>
        </div>
    </body>
</html>