Created by Tara Pandey and Dalton Soper. 

This is a website created for an assignment for Comp 333, a Software Engineering course at Wesleyan University. 
This website is incomplete and does not have the functionality that is said on the landing page. 
This website is comprised of two HTML files, one CSS file, and multiple images that are used in the html files. 

To run the code, all that is necessary is to download all files and open the start.html (or about.html, but start.html is the intended first page) which then has links via a navbar to the other html page. 
You can also access the website at https://dalton2727.github.io/start.html

The work for this project was split 50/50


SQL inputs:
first set up a database named "login". Then input the following command under SQL:
CREATE TABLE users (username VARCHAR(255) PRIMARY KEY, password TEXT);

CREATE TABLE reviews
    (id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username TEXT,
    location TEXT,
    meal TEXT,
    rating INTEGER);
