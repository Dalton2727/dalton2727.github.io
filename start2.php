//start page in php
<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_GET['userid']) ? $_GET['userid'] : '';
?>
<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="Swings points balance" content="" />
        <meta name="description" content="Wesleyan University Point budgeter"/>
        <title>COMP 333: Software Engineering Swings Points</title>
        <link rel="stylesheet" href="CSScode.css" />
    </head>
        <body id = "font" style="background-color:rgb(32, 31, 31);">
            <div id="navbar">
                <ul>
                    <li style="color: white;">User: <?php echo $userid?></li>
                    <li> <?php echo '<a href="demo.php?userid=' . urlencode($userid) . '">Start</a>'?> </li>
                    <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#about">About</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#menu">Menu</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#other">Other</a>'; ?> </li>
                    <li> <?php echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Reviews</a>'?> </li>
                    <li><?php echo '<a href="logout.php">Log Out</a>'; ?></li>
                </ul>
              </div>
              <h1 id = "slogan"> Spend your points guilt free. </h1>
              <div class="boxed">
                <div>
                    <img id = "graph" src="./app_graphic_budget-removebg-preview.png" alt="Graph depicting steady numbers">
                </div>
                    <div class = "info">
                        <p>Keeping track of your points can be stressful and laborious.
                        With our site, budgeting is merely a swipe away!
                        </p>
                        //if session is logged in, have log out option in navbar that logs them out then directs them to the log in page
                        //if user is logged out, allows them to log in through navbar which redirects them to log in page
                        <a href="<?php if ($_SESSION["loggedin"] == true) {echo 'reviews.php?userid=' . urlencode($userid) ;} else {echo 'index.php' ;}?>" class="button">Start</a>
                    </div>
            </div>
            <div>
                <img id = "round" src="more_info.png" alt="rounded corner info boxes">
                </div>
        </div>
        </body>
        <!--   Copyright [2025] [Tara Pandey and Dalton Soper]

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0
       -->
</html>
