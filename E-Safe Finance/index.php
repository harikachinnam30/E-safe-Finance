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
                $conn->close();
                $profile = "data:image;base64,".base64_encode($row1["profile"])."";
            }
        ?>
        <title>E-Safe Finance</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="img/icon.png" type="image/x-icon">
        <link rel="stylesheet" href="loader.css">
        <script src="loader.js"></script>
        <script>
            function toggle_dialog() {
                let dialog = document.getElementById("dialog").style.display;
                if(dialog=="block") {
                    document.getElementById("dialog").style.display = "none";
                } else {
                    document.getElementById("dialog").style.display = "block";
                }
            }
            function switch_content(value) {
                document.getElementById("about").style.display = value=="about"? "block":"none";
                document.getElementById("vision").style.display = value=="vision"? "block":"none";
                document.getElementById("evolution").style.display = value=="evolution"? "block":"none";
                document.getElementById("awards").style.display = value=="awards"? "block":"none";
            }
            function hash_checker() {
                var hash = window.location.hash;
                if(!["#About","#Subsidiaries","#Relations","#NEWS"].includes(hash))
                    window.location.hash = "#About";
                document.getElementById("About").style.display = hash=="#About"? "grid":"none";
                document.getElementById("Subsidiaries").style.display = hash=="#Subsidiaries"? "grid":"none";
                document.getElementById("Relations").style.display = hash=="#Relations"? "grid":"none";
                document.getElementById("NEWS").style.display = hash=="#NEWS"? "grid":"none";
            }
            window.addEventListener("DOMContentLoaded", hash_checker);
            window.addEventListener("hashchange", hash_checker);
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
                padding-bottom : 10px;
                justify-content : space-between;
                align-items : center;
            }
            .nav {
                display : flex;
                position : fixed;
                left : 0;
                width : 100vw;
                height : 50px;
                align-items : center;
                justify-content : space-evenly;
            }
            .nav h2, .nav h3 {
                transition : transform 0.5s;
            }
            .nav h2:hover, .nav h3:hover {
                cursor : pointer;
                transform : scale(1.2);
            }
            #nav1 {
                top : 60px;
                color : white;
            }
            #nav2 {
                top : 110px;
            }
            #nav3, #nav4 {
                display : none;
                background-image : linear-gradient(to right, rgb(100,64,255), rgb(255,100,255));
                top : 160px;
                color : white;
            }
            .main {
                display : grid;
                width : 100vw;
                height : 70vh;
                grid-template-columns : 70vw 30vw;
                grid-template-rows : auto;
                position : fixed;
                top : 210px;
                left : 0;
            }
            .content {
                padding : 20px;
                overflow : scroll;
            }
            .content h2 {
                color : purple;
            }
            .content p, .content li {
                font-family : sans-serif;
                text-align : justify;
            }
            .content-selector {
                display : flex;
                flex-direction : column;
                align-items : center;
                justify-content : space-evenly;
                padding : 10px;
            }
            .content-selector div {
                width : 100px;
                height : 100px;
                padding : 50px;
                border-radius : 100px;
                background-image : linear-gradient(to right, rgb(100,64,255), rgb(255,100,255));
                box-shadow : 3px 3px 5px 2px black;
            }
            .content-selector h2 {
                color : purple;
                transition : transform 3s;
            }
            .content-selector h2:hover {
                cursor : pointer;
                transform : rotateY(360deg) scale(1.5);
            }
            #logo {
                display : flex;
                flex-direction : row;
                align-items : center;
                color : white;
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
            marquee {
                font-size : 1.2em;
            }
        </style>
    </head>
    <body>
        <div class="loader">
            <img src="img/main-loader.gif" alt="Loading">
        </div>
        <div id="top">
            <div id="profile" onclick="toggle_dialog()">
                <img src="<?php echo $profile; ?>" alt="Profile" width="40px" height="40px" style="border: 1px solid black; border-radius: 20px;">
            </div>
            <div id="signin" onclick="window.location='auth'">
                <img src="img/avatar.png" alt="Avatar" width="40px" height="40px" style="border: 1px solid black; border-radius: 20px;">
                <label style="cursor:pointer;">Sign In</label>
            </div>
            <div id="logo">
                <img src="img/logo.png" alt="LOGO" width="100px" height="100px">
                <h1>E-Safe</h1>
            </div>
        </div>
        <div class="nav" id="nav1">
            <h2 onclick="window.location.hash='#About'">About Us</h2>
            <h2 onclick="window.location.hash='#Subsidiaries'">Subsidiaries</h2>
            <h2 onclick="window.location.hash='#Relations'">Investor Relations</h2>
            <h2 onclick="window.location.hash='#NEWS'">E-Safe in the NEWS</h2>
        </div>
        <div class="nav" id="nav2">
            <h3 onclick="window.location='info.html#personal'">Personal</h3>
            <h3 onclick="window.location='info.html#nri'">NRI</h3>
            <h3 onclick="window.location='info.html#business'">Business</h3>
            <h3 onclick="window.location='info.html#agricultural&rural'">Agricultural & Rural</h3>
            <h3 onclick="window.location='info.html#international'">International Banking</h3>
            <h3 onclick="window.location='info.html#wealth'">E-Safe Wealth</h3>
        </div>
        <div class="nav" id="nav3">
            <h3 onclick="window.location='finance.php#account'">Account Details</h3>
            <h3 onclick="window.location='finance.php#transfer'">Money Transfer</h3>
            <h3 onclick="window.location='finance.php#converter'">Currency Converter</h3>
            <h3 onclick="window.location='finance.php#history'">Transaction History</h3>
            <h3 onclick="window.location='finance.php#help'">Help Desk</h3>
        </div>
        <div class="nav" id="nav4">
            <marquee behavior="scroll" direction="right">Please Sign In to see the options</marquee>
        </div>
        <div class="main" id="About">
            <div class="content">
                <div id="about">
                    <h2>About Us - E-safe</h2>
                    <p>E-safe Bank is an Indian Multinational, Public Sector Banking and Financial services statutory body headquartered in Mumbai. The rich heritage and legacy of over 200 years, accredits E-safe  as the most trusted Bank by Indians through generations.</p>
                    <p>E-safe, the largest Indian Bank with 1/4th market share, serves over 48 crore customers through its vast network of over 22,405 branches, 65,627 ATMs/ADWMs, 76,089 BC outlets, with an undeterred focus on innovation, and customer centricity, which stems from the core values of the Bank - Service, Transparency, Ethics, Politeness and Sustainability.</p>
                    <p>The Bank has successfully diversified businesses through its various subsidiaries i.e E-safe General Insurance, E-safe Life Insurance, E-safe Mutual Fund , etc. It has spread its presence globally and operates across time zones through 235 offices in 29 foreign countries.</p>
                    <p>Growing with times, E-safe continues to redefine banking in India, as it aims to offer responsible and sustainable Banking solutions</p>
                </div>
                <div id="vision">
                    <h2>Vision Mission Values</h2>
                    <img src="img/vision.jpeg" alt="vision mission values">
                </div>
                <div id="evolution">
                    <h2>Evolution Of E-Safe</h2>
                    <p><b>The origin of the E-SAFE of INDIA goes back to the first decade of the nineteenth century with the establishment of the Bank E-SAFE .The bank was re-designed as E-safe  (15 January 1987). It was evolve  all over the INDIA in  17 july 1995.</b></p>
                </div>
                <div id="awards">
                    <h2>Awards</h2>
                    <ul>
                        <li>E-safe honoured with India's Best Annual Report Awards-2022 by Free Press Journal</li>
                        <li>E-safe honoured with three Gold Awards at ET Human Capital Awards</li>
                        <li>HR Leader of the Year - Large Scale Organisations</li>
                        <li>Excellence in Business Continuity Planning & Management</li>
                        <li>Most Valuable Employer During COVID-19</li>
                        <li>E-safe won two Awards from NASSCOM -DSCI</li>
                        <li>Best Security Operations Centre of the Year</li>
                        <li>Cyber Security Awareness</li>
                        <li>Gold & Silver awards in The ET HR World Future Skill Awards</li>
                    </ul>
                </div>
            </div>
            <div class="content-selector">
                <div>
                    <img src="img/about.png" alt="About" width="100px" height="100px">
                </div>
                <h2 onclick="switch_content('about')">About Us</h2>
                <h2 onclick="switch_content('vision')">Vision Mission Values</h2>
                <h2 onclick="switch_content('evolution')">Evolution Of E-Safe</h2>
                <h2 onclick="switch_content('awards')">Awards</h2>
            </div>
        </div>
        <div class="main" id="Subsidiaries">
            <div class="content">
                <h2>Subsidiaries/Joint Ventures</h2>
                <p><b>E-Safe LIFE INSURANCE COMPANY LIMITED (SBI-LIFE)</b></p>
                <p>E-safe Life Insurance , one of the most trusted life insurance companies in India, was  incorporated in October 2000 and was registered with the Insurance Regulatory and Development Authority of India (IRDAI) in March 2001. Serving millions of families across India, E-safe Life's diverse range of products caters to individuals as well as group customers through Protection, Pension, Savings and Health solutions. Driven by 'Customer-First' approach, E-safe Life places great emphasis on maintaining world class operating efficiency and providing hassle-free claim settlement experience to its customers by following high ethical standards of service. Additionally, E-safe Life is committed to enhance digital experiences for its customers, distributors, and employees alike</p>
            </div>
            <div class="content-selector">
                <div><img src="img/subsidiaries.png" alt="Subsidiaries"></div>
                <h2>Subsidiaries/Joint Ventures</h2>
            </div>
        </div>
        <div class="main" id="Relations">
            <div class="content">
                <h2>Investor Relation</h2>
                <p>E-SAFE Bank, the largest country's  Bank in terms of profits, assets, deposits, branches and employees, welcomes you to its 'Investors Relations' Section. SBI, with its heritage dating back to the year 1990, strives to continuously provide latest and up to date information on its financial performance. The Bank communicates with the stakeholders through a variety of channels, such as through e-mail, website, conference call, one-on-one meeting, analysts' meet and attendance at Investor Conference throughout the world Please find Bank's financial results, analysis of performance and other highlights which will be of interest to Investors, Fund Managers and Analysts. E-Safe has always been fundamentally strong in its core business which is mirrored in its results - year after year</p>
            </div>
            <div class="content-selector">
                <div><img src="img/relations.png" alt="Investor Relations"></div>
                <h2>Investor Relation</h2>
            </div>
        </div>
        <div class="main" id="NEWS">
            <div class="content">
                <h2>E-Safe In News</h2>
                <p>e-Safe Bank shattered expectations in its first quarter of operation, attracting over 500,000 new customers and processing transactions totaling $1 billion. This unprecedented growth speaks volumes about the growing demand for secure digital banking solutions in today's increasingly interconnected world.</p>
                <p>Industry experts have lauded e-Safe Bank for its innovative approach to digital banking security, with many hailing it as a game-changer in the financial sector. "e-Safe Bank's emphasis on security and transparency sets a new standard for digital banking," remarked cybersecurity analyst Dr. Emily Chen. "Their commitment to safeguarding customer assets and personal information is commendable and sorely needed in today's threat landscape."</p>
            </div>
            <div class="content-selector">
                <div><img src="img/news.png" alt="E-Safe in NEWS"></div>
                <h2>E-Safe In News</h2>
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
        </script>
    </body>
    <script>
            <?php
                if($_SESSION["username"]) {
                    echo "document.getElementById('profile').style.display = 'block';";
                    echo "document.getElementById('nav3').style.display = 'flex';";
                } else {
                    echo "document.getElementById('signin').style.display = 'flex';";
                    echo "document.getElementById('nav4').style.display = 'flex';";
                }
            ?>
            switch_content('about');
    </script>
</html>