<?php 
session_start();
include 'dbconnection.php';
$userid = isset($_GET['userid']) ? $_GET['userid'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="Swings points balance" content="" />
    <meta name="description" content="Wesleyan University Point budgeter"/>
    <div id="navbar">
        <ul>
        <li style="color: white;">User: <?php echo $userid?></li>
                    <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#about">About</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#menu">Menu</a>'; ?> </li>
                    <li> <?php echo '<a href="about2.php?userid=' . urlencode($userid) . '#other">Other</a>'; ?> </li>
                    <li><?php if ($_SESSION['loggedin']){ echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Start</a>';} else {echo '<a href="index.php">Start</a>';}?></li>
        </ul>
      </div>
    <body id = font style="background-color:rgb(32, 31, 31);">
        <h2 class = "head_text" >About</h2>
    <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="About Page" content="" />
        <title>About Us</title>
        <link rel="stylesheet" href="CSScode.css" />
    </head>
    <div class="body_text">
        We know its easy to get lost in the delicious meals 
        around campus without paying attention to your 
        declining points. We want students to be able to enjoy 
        our plethora of on campus dining and grocery options 
        without worrying about spending too many points.
    </div>
    <div class="body_text">
        The purpose of our site is to encourage and support students in budgeting their points.
         Just input your points through <a href="https://wesleyan-sp.transactcampus.com/eaccounts/(S(o2u5ivncjnjzvcpsccto1fr2))/AnonymousHome.aspx">eAccounts</a>
        and you can easily create a weekly budget and factor in which purchases 
        would allow you to stick to your predetermined budget all through the site! 
        Simply swipe and our app will update your profile with how many points
         you've spent and how much it deviates from your budget. (This site was designed and published as part of the COMP
         333 Software Engineering class at Wesleyan University. This is an exercise.)
    </div>

    <div id="other">
        <h2 class = "head_text"> Other Options</h2>
        <div class = "body_text">
        <p>Sometimes it is hard to save all your points and you run out. Or maybe you are trying to
            save your points until later in the semester. Maybe you are a freshman. Whatever reason, 
            if you don't want to (or can't) spend your points there are other options for food. 
            Instead of spending points at Swings and RBC, spend meals at participating Bon Apetite locations
            at Wesleyan university (there are also points option at all locations). These locations include
            Usdan Marketplace, Summerfields, and Pi Cafe. Operating times and locations for these options 
            can be found <a href="https://www.wesleyan.edu/dining/placestoeat.html">on the Wesleyan page for Bon Apetite</a>.
        </p>
        </div>
    </div>

    <h2 id = "menu" class = "head_text">Menu</h2>

    <div class="flex">
        <div>
        <img src = "./IMG_0392.JPG" alt="Avo + Bacon" />
        </div>
        <div>
            <img src = "./IMG_1704.JPG" alt="Birria Tacos"/>
        </div>
        <div>
        <img src = "./IMG_3975.JPG" alt="French Toast" />
        </div>
        
    </div>
    <div class = "body_text" >
        Swings' and Red and Black Cafe's menus can be found <a href="https://weswings.com/">on their website</a>. 
        Here you can find their year-long static menu as well as keep up with their rotating specials!
    </div>
    <div id="schedule" class="body_text">
        Wesleyan University dining services' hours can be found <a href="https://www.wesleyan.edu/dining/Hours%20of%20Operation.html">on the University schedule</a>.
    </div> 
    <iframe src="https://www.youtube.com/embed/F91nYGi8Ot0?si=4_2hCQq_gKGrz2eE&amp;clip=UgkxkSVBE4Uu0BFs0mcI--pRkzdNOQUU1Sov&amp;clipt=ELj8CBjYmAo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
</body>
<!--    Copyright [2025] [Tara Pandey and Dalton Soper]

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0
       -->
    </html>