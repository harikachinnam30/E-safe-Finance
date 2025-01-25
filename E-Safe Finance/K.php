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
            } else {
                header("Location: auth/");
            }
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>E-Safe Finance</title>
        <link rel="icon" href="img/icon.png" type="image/x-icon">
        <script>
            var accounts = {"Teja":"12/3", "Ashu":"7/3", "Vaishu":"1/3", "Chinni":"23/2", "Asheesh":"17/2", "Kyate":"14/2", "RamanaUma":"1/1"};
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
                    case "#contacts":
                        document.getElementById("contacts").style.display = "block";
                        break;
                    case "#account":
                        document.getElementById("account").style.display = "block";
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
                        document.getElementById("typechange").style.display = "block";
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
                        window.location.hash = "#contacts";
                }
            }
            window.addEventListener("DOMContentLoaded", hash_change);
            window.addEventListener("hashchange", hash_change);
            function search() {
                let indexes = Object.keys(accounts).filter(initMatch);
                let element = document.getElementById("list");
                element.innerHTML = "";
                for(index of indexes) {
                    element.innerHTML += `<div class="contact" onclick="window.location.search='name=${index}'">\n<div class="person">\n<svg width="50" height="50">\n<circle cx="25" cy="25" r="25" fill="rgb(178,82,255)" />\n<circle cx="25" cy="20" r="8" fill="white" />\n<ellipse cx="25" cy="40" rx="15" ry="8" fill="white" />\n</svg>\n<p>${index}</p>\n</div>\n<p>${accounts[index]}</p>\n</div>`;
                }
            }
            function initMatch(value) {
                let name = document.getElementById("search").value;
                return value.startsWith(name);
            }
        </script>
        <style>
            body {
                background-color : rgb(234,234,234);
            }
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
            }
            #topic {
                background-color : rgb(178,82,255);
                display : flex;
                flex-direction : column;
                position : fixed;
                top : 100px;
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
                background-image : linear-gradient(to right, rgb(100,64,255,0.5), rgb(255,100,255,0.5));
                cursor : pointer;
            }
            #content {
                padding : 25px;
                transition : margin 1s;
            }
            #contacts {
                background-color : white;
                padding : 25px;
                border-radius : 25px;
            }
            #searchbox-out {
                background-image : linear-gradient(to right, rgb(100,64,255), rgb(255,100,255));
                border-radius : 10px;
                padding : 5px;
            }
            #searchbox-in {
                background-color : white;
                display : flex;
                align-items : center;
                border-radius : 10px;
            }
            #search {
                border : none;
                margin : 10px;
                font-size : 1.5em;
                outline-color : white;
                flex : 1;
            }
            #list {
                display : flex;
                flex-direction : column;
                padding : 50px 25px;
            }
            .contact {
                display : flex;
                justify-content : space-between;
                align-items : center;
                background-image : linear-gradient(to right, rgb(100,64,255,0.5), rgb(255,100,255,0.5));
                padding : 5px 10px;
                margin : 5px 0;
                border-radius : 5px;
                cursor : pointer;
            }
            .person {
                display : flex;
                align-items : center;
            }
            .contact p, #navbar p {
                font-size : 1.3em;
            }
            .contact svg, #navbar svg {
                margin : 0 50px;
            }
            #history {
                display : none;
                padding : 50px 25px;
            }
            #navbar {
                display : flex;
                justify-content : space-between;
                align-items : center;
                background-image : linear-gradient(to right, rgb(100,64,255,0.5), rgb(255,100,255,0.5));
                padding : 5px 10px;
                border-radius : 5px 5px 0 0;
            }
            #navbar img {
                cursor : pointer;
            }
            #chat {
                background-color : lightgrey;
                padding : 50px;
                border-radius : 0 0 5px 5px;
                text-align : center;
            }
        </style>
    </head>
    <body>
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
                <div id="contacts">
                    <div id="searchbox-out">
                        <div id="searchbox-in">
                            <input type="text" id="search" placeholder="Search from here" onkeypress="if(event.key=='Enter') {event.preventDefault(); search();}">
                            <img src="https://uxwing.com/wp-content/themes/uxwing/download/user-interface/search-icon.png" alt="locate" width="25px" height="25px" style="padding : 10px; cursor : pointer;" onclick="search()">
                        </div>
                    </div>
                    <div id="list">
                    </div>
                    <div id="history">
                        <div id="navbar">
                            <div class="person">
                                <img src="https://cdn-icons-png.flaticon.com/512/109/109618.png" alt="back" width="40px" height="40px" onclick="window.location='K.php'">
                                <svg width="50" height="50">
                                    <circle cx="25" cy="25" r="25" fill="rgb(178,82,255)" />
                                    <circle cx="25" cy="20" r="8" fill="white" />
                                    <ellipse cx="25" cy="40" rx="15" ry="8" fill="white" />
                                </svg>
                                <p><?php echo $_REQUEST["name"]; ?></p>
                            </div>
                            <img src="https://cdn.icon-icons.com/icons2/2719/PNG/512/dots_three_vertical_icon_175228.png" alt="more options" width="40px" height="40px">
                        </div>
                        <div id="chat">history</div>
                    </div>
                </div>
                <div id="account">a</div>
                <div id="transfer">b</div>
                <div id="converter">c</div>
                <div id="converter-1">d</div>
                <div id="converter-2">e</div>
                <div id="SI">f</div>
                <div id="LI">g</div>
                <div id="typechange">h</div>
                <div id="loan">i</div>
                <div id="history">j</div>
                <div id="help">k</div>
            </div>
            <div id="topic" onmouseover="document.getElementById('content').style.marginLeft='360px';" onmouseout="document.getElementById('content').style.marginLeft='0';">
                <div onclick="toggle_dropdown(0)">
                    <h2>Account Management</h2>
                    <img class="dropdown-1" src="img/dropdown.png" alt="show" width="25px" height="25px">
                </div>
                <div class="dropdown-2">
                    <h3 onclick="window.location.hash='#account'">Account Details</h3>
                    <h3 onclick="window.location.hash='#transfer'">Net Banking</h3>
                </div>
                <div onclick="toggle_dropdown(1)">
                    <h2>Currency Converter</h2>
                    <img class="dropdown-1" src="img/dropdown.png" alt="show" width="25px" height="25px">
                </div>
                <div class="dropdown-2">
                    <h3 onclick="window.location.hash='#converter'">Currency to Currency</h3>
                    <h3 onclick="window.location.hash='#converter-1'">Currency to Ticcoin</h3>
                    <h3 onclick="window.location.hash='#converter-2'">Ticcoin to Currency</h3>
                </div>
                <div onclick="toggle_dropdown(2)">
                    <h2>Interest</h2>
                    <img class="dropdown-1" src="img/dropdown.png" alt="show" width="25px" height="25px">
                </div>
                <div class="dropdown-2">
                    <h3 onclick="window.location.hash='#SI'">Savings Interest</h3>
                    <h3 onclick="window.location.hash='#LI'">Loan Interest</h3>
                </div>
                <div onclick="toggle_dropdown(3)">
                    <h2>Loan/Savings</h2>
                    <img class="dropdown-1" src="img/dropdown.png" alt="show" width="25px" height="25px">
                </div>
                <div class="dropdown-2">
                    <h3 onclick="window.location.hash='#typechange'">Change of Account Type</h3>
                    <h3 onclick="window.location.hash='#loan'">Apply for Loan</h3>
                </div>
                <div onclick="toggle_dropdown(4)">
                    <h2>Transaction History</h2>
                    <img class="dropdown-1" src="img/dropdown.png" alt="show" width="25px" height="25px">
                </div>
                <div class="dropdown-2">
                    <h3 onclick="window.location.hash='#history'">Account History</h3>
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
    </body>
    <script>
        search();
    </script>
    <?php
        if($_REQUEST["name"]) {
            echo "<script>document.getElementById('list').style.display='none';\ndocument.getElementById('history').style.display='block';</script>";
        } else {
            echo "<script>document.getElementById('list').style.display='flex';\ndocument.getElementById('history').style.display='none';</script>";
        }
    ?>
</html>